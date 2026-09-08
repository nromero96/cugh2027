<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HotelReservation;
use App\Models\User;
use App\Models\Inscription;
use App\Http\Requests\StoreHotelierReservationRequest;
use App\Http\Requests\StoreHotelReservationRequest;
use App\Http\Requests\UpdateHotelReservationRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class HotelReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userid = \Auth::user()->id;

        $data = [
            'category_name' => 'hotelreservations',
            'page_name' => 'hotelreservations',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        if (\Auth::user()->hasRole('Administrador') || \Auth::user()->hasRole('Secretaria') || \Auth::user()->hasRole('Hotelero') ) {
            //get hotelreservations and join with users table
            $hotelreservations = HotelReservation::join('users', 'hotel_reservations.user_id', '=', 'users.id')
                ->select('hotel_reservations.*', 'users.name', 'users.lastname', 'users.second_lastname', 'users.email')
                ->orderBy('hotel_reservations.id', 'desc')
                ->get();
        } else {
            //get hotelreservations and join with users table
            $hotelreservations = HotelReservation::join('users', 'hotel_reservations.user_id', '=', 'users.id')
                ->select('hotel_reservations.*', 'users.name', 'users.lastname', 'users.second_lastname', 'users.email')
                ->where('hotel_reservations.user_id', $userid)
                ->orderBy('hotel_reservations.id', 'desc')
                ->get();
        }

        return view('pages.hotelreservations.index')->with($data)->with('hotelreservations', $hotelreservations);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $id = \Auth::user()->id;

        $data = [
            'category_name' => 'hotelreservations',
            'page_name' => 'hotelreservations_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        $user = User::find($id);

        return view('pages.hotelreservations.create')->with($data)->with('user', $user);
    }

    public function createByHotelier()
    {
        abort_unless(auth()->user()->hasRole('Hotelero'), 403);

        $participants = User::query()
            ->select('users.id', 'users.name', 'users.lastname', 'users.second_lastname', 'users.email')
            ->join('model_has_roles', function ($join) {
                $join->on('model_has_roles.model_id', '=', 'users.id')
                    ->where('model_has_roles.model_type', User::class);
            })
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('roles.name', 'Participante')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('inscriptions')
                    ->whereColumn('inscriptions.user_id', 'users.id')
                    ->where('inscriptions.status', 'Confirmed');
            })
            ->distinct()
            ->orderBy('users.name')
            ->orderBy('users.lastname')
            ->get();

        return view('pages.hotelreservations.create-hotelier', [
            'category_name' => 'hotelreservations',
            'page_name' => 'hotelreservations_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'participants' => $participants,
            'hotels' => StoreHotelierReservationRequest::hotels(),
            'roomTypes' => $this->roomTypes(),
        ]);
    }

    public function storeByHotelier(StoreHotelierReservationRequest $request)
    {
        $data = $request->validated();

        $reservation = DB::transaction(function () use ($data) {
            $acceptedInscription = Inscription::where('user_id', $data['user_id'])
                ->where('status', 'Confirmed')
                ->lockForUpdate()
                ->first();

            if (!$acceptedInscription || !User::whereKey($data['user_id'])->whereHas('roles', function ($query) {
                $query->where('name', 'Participante');
            })->exists()) {
                throw ValidationException::withMessages([
                    'user_id' => 'The selected participant does not have a confirmed registration.',
                ]);
            }

            return HotelReservation::create([
                'user_id' => $data['user_id'],
                'hotel_name' => $data['hotel_name'],
                'habitacion_type' => $data['habitacion_type'],
                'number_guests' => $data['number_guests'],
                'check_in' => $data['check_in'],
                'check_out' => $data['check_out'],
                'comment' => $data['comment'] ?? null,
                'status' => 'Pendiente',
            ]);
        });

        return redirect()->route('hotelreservations.show', $reservation->id)
            ->with('success', 'Hotel reservation created successfully.');
    }

    private function roomTypes(): array
    {
        return ['Simple', 'Matrimonial', 'Doble dos camas'];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreHotelReservationRequest $request)
    {
                //get logged in user id
                $user_id = \Auth::user()->id;

                //store form data
                $hotelreservation = new HotelReservation;

                $hotelreservation->user_id = $user_id;
                $hotelreservation->hotel_name = $request->input('hotel_name');
                $hotelreservation->habitacion_type = $request->input('habitacion_type');
                $hotelreservation->number_guests = $request->input('number_guests');
                $hotelreservation->check_in = $request->input('check_in');
                $hotelreservation->check_out = $request->input('check_out');
                $hotelreservation->comment = $request->input('comment');

                $hotelreservation->save();

                //flash success message redirect route hotelreservations.index
                return redirect()->route('hotelreservations.index')->with('success', 'Hotel reservation created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $reservationOwner = HotelReservation::findOrFail($id);

        //solo puede ver la reserva el hotelero o Administrador o Secretaria o el usuario que la creo
        if (!\Auth::user()->hasRole('Hotelero') && !\Auth::user()->hasRole('Administrador') && !\Auth::user()->hasRole('Secretaria')) {
            $userid = \Auth::user()->id;
            if ($reservationOwner->user_id != $userid) {
                return redirect()->route('hotelreservations.index')->with('error', 'No tienes permiso para ver esta reserva de hotel');
            } 

        }

        //show hotelreservation by id and join with users table
        $hotelreservation = HotelReservation::join('users', 'hotel_reservations.user_id', '=', 'users.id')
            ->select(
                'hotel_reservations.*', 
                'users.name as user_name',
                'users.lastname as user_lastname',
                'users.second_lastname as user_second_lastname',
                'users.phone_code as user_phone_code',
                'users.phone_number as user_phone_number',
                'users.whatsapp_code as user_whatsapp_code',
                'users.whatsapp_number as user_whatsapp_number',
                'users.email as user_email',
            )
            ->where('hotel_reservations.id', $id)
            ->firstOrFail();

        $data = [
            'category_name' => 'hotelreservations',
            'page_name' => 'hotelreservations_show',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
        ];

        return view('pages.hotelreservations.show')->with($data)->with('hotelreservation', $hotelreservation);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        abort_unless(auth()->user()->hasRole('Hotelero'), 403);

        $hotelreservation = HotelReservation::with('user')->whereHas('user')->findOrFail($id);

        return view('pages.hotelreservations.edit', [
            'category_name' => 'hotelreservations',
            'page_name' => 'hotelreservations_create',
            'has_scrollspy' => 0,
            'scrollspy_offset' => '',
            'hotelreservation' => $hotelreservation,
            'hotels' => StoreHotelierReservationRequest::hotels(),
            'roomTypes' => $this->roomTypes(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateHotelReservationRequest $request, $id)
    {
        $hotelreservation = HotelReservation::findOrFail($id);
        $hotelreservation->fill($request->validated())->save();

        return redirect()->route('hotelreservations.show', $id)
            ->with('success', 'Hotel reservation updated successfully.');

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
