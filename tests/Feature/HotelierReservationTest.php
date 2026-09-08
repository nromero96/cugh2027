<?php

namespace Tests\Feature;

use App\Http\Controllers\HotelReservationController;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use Tests\TestCase;

class HotelierReservationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'hotel_testing', 'database.connections.hotel_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('lastname')->nullable();
            $table->string('second_lastname')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('phone_code')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('whatsapp_code')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->timestamps();
        });
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
        });
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
        });
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('status');
            $table->timestamps();
        });
        Schema::create('hotel_reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('hotel_name');
            $table->string('habitacion_type');
            $table->string('number_guests');
            $table->string('check_in');
            $table->string('check_out');
            $table->text('comment')->nullable();
            $table->text('note')->nullable();
            $table->string('status')->default('Pendiente');
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'Participante', 'guard_name' => 'web'],
            ['id' => 2, 'name' => 'Administrador', 'guard_name' => 'web'],
        ]);
    }

    private function actingAsRole(bool $isHotelier)
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->id = 999;
        $user->shouldReceive('hasRole')->with('Hotelero')->andReturn($isHotelier);
        $this->actingAs($user);
    }

    private function participant(int $id, string $status, int $roleId = 1): void
    {
        DB::table('users')->insert([
            'id' => $id, 'name' => 'User '.$id, 'email' => "user{$id}@example.com",
            'created_at' => now(), 'updated_at' => now(),
        ]);
        DB::table('model_has_roles')->insert([
            'role_id' => $roleId, 'model_type' => User::class, 'model_id' => $id,
        ]);
        DB::table('inscriptions')->insert([
            'user_id' => $id, 'status' => $status, 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    private function payload(int $userId): array
    {
        return [
            'user_id' => $userId,
            'hotel_name' => 'Swissôtel Lima *****',
            'habitacion_type' => 'Simple',
            'number_guests' => 1,
            'check_in' => '2027-02-24',
            'check_out' => '2027-02-28',
            'comment' => 'Created by hotel manager.',
        ];
    }

    public function test_form_lists_only_participants_with_confirmed_registration()
    {
        $this->participant(1, 'Confirmed');
        $this->participant(2, 'Pending');
        $this->participant(3, 'Confirmed', 2);
        $this->actingAsRole(true);

        $response = app(HotelReservationController::class)->createByHotelier();
        $this->assertSame([1], $response->getData()['participants']->pluck('id')->all());
    }

    public function test_hotelier_can_create_reservation_for_confirmed_participant()
    {
        $this->participant(1, 'Confirmed');
        $this->actingAsRole(true);

        $this->withoutMiddleware()->post(route('hotelreservations.hotelier.store'), $this->payload(1))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('hotelreservations.show', 1));

        $this->assertDatabaseHas('hotel_reservations', [
            'user_id' => 1, 'status' => 'Pendiente', 'hotel_name' => 'Swissôtel Lima *****',
        ]);
    }

    public function test_server_rejects_participant_without_confirmed_registration()
    {
        $this->participant(1, 'Pending');
        $this->actingAsRole(true);

        $this->withoutMiddleware()->from(route('hotelreservations.hotelier.create'))
            ->post(route('hotelreservations.hotelier.store'), $this->payload(1))
            ->assertSessionHasErrors('user_id');

        $this->assertDatabaseCount('hotel_reservations', 0);
    }

    public function test_other_roles_cannot_open_or_submit_hotelier_form()
    {
        $this->participant(1, 'Confirmed');
        $this->actingAsRole(false);

        $this->withoutMiddleware()->get(route('hotelreservations.hotelier.create'))->assertForbidden();
        $this->withoutMiddleware()->post(route('hotelreservations.hotelier.store'), $this->payload(1))->assertForbidden();
        $this->assertDatabaseCount('hotel_reservations', 0);
    }

    public function test_check_out_must_be_after_check_in()
    {
        $this->participant(1, 'Confirmed');
        $this->actingAsRole(true);
        $payload = array_merge($this->payload(1), ['check_out' => '2027-02-24']);

        $this->withoutMiddleware()->post(route('hotelreservations.hotelier.store'), $payload)
            ->assertSessionHasErrors('check_out');
    }

    public function test_allowed_hotel_date_window_is_enforced()
    {
        $this->participant(1, 'Confirmed');
        $this->actingAsRole(true);

        $this->withoutMiddleware()->post(route('hotelreservations.hotelier.store'), array_merge($this->payload(1), [
            'check_in' => '2027-02-20', 'check_out' => '2027-03-05',
        ]))->assertSessionHasNoErrors();

        $this->withoutMiddleware()->post(route('hotelreservations.hotelier.store'), array_merge($this->payload(1), [
            'check_in' => '2027-02-19', 'check_out' => '2027-03-06',
        ]))->assertSessionHasErrors(['check_in', 'check_out']);
    }

    public function test_show_query_uses_existing_personal_phone_columns()
    {
        $this->participant(1, 'Confirmed');
        DB::table('users')->where('id', 1)->update([
            'phone_code' => '+51', 'phone_number' => '999999999',
        ]);
        DB::table('hotel_reservations')->insert(array_merge($this->payload(1), [
            'status' => 'Pendiente', 'created_at' => now(), 'updated_at' => now(),
        ]));
        $this->actingAsRole(true);

        $response = app(HotelReservationController::class)->show(1);
        $reservation = $response->getData()['hotelreservation'];
        $this->assertSame('+51', $reservation->user_phone_code);
        $this->assertSame('999999999', $reservation->user_phone_number);
    }

    public function test_hotelier_can_edit_reservation_without_changing_participant()
    {
        $this->participant(1, 'Confirmed');
        $this->participant(2, 'Confirmed');
        DB::table('hotel_reservations')->insert(array_merge($this->payload(1), [
            'status' => 'Pendiente', 'created_at' => now(), 'updated_at' => now(),
        ]));
        $this->actingAsRole(true);

        $editResponse = app(HotelReservationController::class)->edit(1);
        $this->assertSame(1, $editResponse->getData()['hotelreservation']->user_id);

        $payload = array_merge($this->payload(2), [
            'hotel_name' => 'Novotel Lima ****',
            'status' => 'Reservado',
            'note' => 'Reservation confirmed by hotel.',
            'name' => 'Attempted user change',
        ]);
        $this->withoutMiddleware()->put(route('hotelreservations.update', 1), $payload)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('hotelreservations.show', 1));

        $this->assertDatabaseHas('hotel_reservations', [
            'id' => 1, 'user_id' => 1, 'hotel_name' => 'Novotel Lima ****',
            'status' => 'Reservado', 'note' => 'Reservation confirmed by hotel.',
        ]);
        $this->assertSame('User 1', DB::table('users')->where('id', 1)->value('name'));
    }

    public function test_non_hotelier_cannot_edit_or_update_reservation()
    {
        $this->participant(1, 'Confirmed');
        DB::table('hotel_reservations')->insert(array_merge($this->payload(1), [
            'status' => 'Pendiente', 'created_at' => now(), 'updated_at' => now(),
        ]));
        $this->actingAsRole(false);

        $this->withoutMiddleware()->get(route('hotelreservations.edit', 1))->assertForbidden();
        $this->withoutMiddleware()->put(route('hotelreservations.update', 1), array_merge($this->payload(1), [
            'status' => 'Reservado',
        ]))->assertForbidden();
        $this->assertSame('Pendiente', DB::table('hotel_reservations')->where('id', 1)->value('status'));
    }

    public function test_edit_rejects_invalid_status_and_dates()
    {
        $this->participant(1, 'Confirmed');
        DB::table('hotel_reservations')->insert(array_merge($this->payload(1), [
            'status' => 'Pendiente', 'created_at' => now(), 'updated_at' => now(),
        ]));
        $this->actingAsRole(true);

        $this->withoutMiddleware()->put(route('hotelreservations.update', 1), array_merge($this->payload(1), [
            'status' => 'INVALID', 'check_in' => '2027-02-19', 'check_out' => '2027-03-06',
        ]))->assertSessionHasErrors(['status', 'check_in', 'check_out']);
    }
}
