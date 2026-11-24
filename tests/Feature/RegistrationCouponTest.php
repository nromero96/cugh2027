<?php

namespace Tests\Feature;

use App\Services\RegistrationCoupon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class RegistrationCouponTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'coupon_testing', 'database.connections.coupon_testing' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('special_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->decimal('amount', 8, 2);
            $table->string('status');
            $table->string('payment_required');
            $table->integer('quantity');
            $table->date('expiration');
            $table->string('usage_rule');
            $table->string('allowed_email')->nullable();
            $table->timestamps();
        });
        Schema::create('category_inscriptions', function (Blueprint $table) {
            $table->id();
        });
        Schema::create('special_code_categories', function (Blueprint $table) {
            $table->unsignedBigInteger('special_code_id');
            $table->unsignedBigInteger('category_inscription_id');
        });
        Schema::create('special_code_allowed_emails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('special_code_id');
            $table->string('email');
        });
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email');
        });
        Schema::create('inscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('special_code');
            $table->string('special_code_email')->nullable();
            $table->string('status');
        });
        DB::table('category_inscriptions')->insert([['id' => 1], ['id' => 2]]);
        DB::table('special_codes')->insert([
            'id' => 1, 'code' => 'PROMO', 'amount' => 320, 'status' => 'Activo',
            'payment_required' => 'No', 'quantity' => 2,
            'expiration' => now('America/Lima')->toDateString(),
            'usage_rule' => 'per_email', 'allowed_email' => 'person@example.com',
        ]);
        DB::table('special_code_categories')->insert(['special_code_id' => 1, 'category_inscription_id' => 1]);
        DB::table('users')->insert(['id' => 1, 'email' => 'person@example.com']);
    }

    public function test_coupon_accepts_case_insensitive_code_and_expiration_day()
    {
        $coupon = app(RegistrationCoupon::class)->check(' promo ', 1, 'PERSON@example.com');
        $this->assertSame('No', $coupon->payment_required);
        $this->assertSame(320.0, (float) $coupon->amount);
    }

    public function test_category_and_per_email_rules_exclude_drafts()
    {
        DB::table('inscriptions')->insert([
            ['id' => 1, 'user_id' => 1, 'special_code' => 'PROMO', 'special_code_email' => null, 'status' => 'Draft'],
        ]);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'person@example.com');
        DB::table('inscriptions')->where('id', 1)->update(['status' => 'Processing', 'special_code_email' => 'person@example.com']);
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'PERSON@example.com');
    }

    public function test_coupon_rejects_unconfigured_category()
    {
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 2, 'person@example.com');
    }

    public function test_single_use_limit_ignores_a_draft_but_counts_a_processing_registration()
    {
        DB::table('special_codes')->where('id', 1)->update(['usage_rule' => 'single_use', 'allowed_email' => 'other@example.com']);
        DB::table('inscriptions')->insert([
            'id' => 1, 'user_id' => 1, 'special_code' => 'PROMO', 'status' => 'Draft',
        ]);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'other@example.com');
        DB::table('inscriptions')->where('id', 1)->update(['status' => 'Processing']);
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'other@example.com');
    }

    public function test_email_restriction_is_case_insensitive()
    {
        DB::table('special_codes')->where('id', 1)->update(['allowed_email' => 'Person@Example.com']);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'PERSON@example.com');
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'other@example.com');
    }

    public function test_several_authorized_emails_can_use_one_code_once_each_up_to_quantity()
    {
        DB::table('special_code_allowed_emails')->insert([
            ['special_code_id' => 1, 'email' => 'first@example.com'],
            ['special_code_id' => 1, 'email' => 'second@example.com'],
            ['special_code_id' => 1, 'email' => 'third@example.com'],
        ]);
        DB::table('inscriptions')->insert([
            'id' => 1, 'user_id' => 1, 'special_code' => 'PROMO',
            'special_code_email' => 'first@example.com', 'status' => 'Processing',
        ]);
        app(RegistrationCoupon::class)->check('promo', 1, 'SECOND@example.com');
        try {
            app(RegistrationCoupon::class)->check('promo', 1, 'outsider@example.com');
            $this->fail('An unlisted email used the coupon.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString('reserved', $exception->errors()['specialcode'][0]);
        }
        DB::table('inscriptions')->insert([
            'id' => 2, 'user_id' => 1, 'special_code' => 'PROMO',
            'special_code_email' => 'second@example.com', 'status' => 'Processing',
        ]);
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'third@example.com');
    }

    public function test_email_list_is_normalized_and_rejects_invalid_addresses()
    {
        $this->assertSame(['first@example.com', 'second@example.com'],
            RegistrationCoupon::normalizeAllowedEmails(" FIRST@EXAMPLE.COM\nsecond@example.com,first@example.com"));
        $this->expectException(ValidationException::class);
        RegistrationCoupon::normalizeAllowedEmails('not-an-email');
    }

    public function test_only_active_codes_assigned_to_the_account_email_are_listed()
    {
        $this->assertSame(['PROMO'], app(RegistrationCoupon::class)->assignedToEmail('PERSON@EXAMPLE.COM')->pluck('code')->all());
        $this->assertCount(0, app(RegistrationCoupon::class)->assignedToEmail('other@example.com'));
        DB::table('special_codes')->where('id', 1)->update(['allowed_email' => null]);
        $this->assertCount(0, app(RegistrationCoupon::class)->assignedToEmail('person@example.com'));
        $this->expectException(ValidationException::class);
        app(RegistrationCoupon::class)->check('PROMO', 1, 'person@example.com');
    }
}
