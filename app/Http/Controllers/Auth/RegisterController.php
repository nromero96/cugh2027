<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::MYINSCRIPTION;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        // Define las reglas de validación principales
        $validator = Validator::make($data, [
            'email' => [
                'required', 
                'string', 
                'email:rfc,dns', 
                'max:255', 
                'unique:users,email',
            ],

            'password' => [
                'required', 
                'string', 
                'min:8', 
                'confirmed',
            ],

            'document_type' => [
                'required', 
                'string',
            ],

            'document_number' => [
                'required',
                'string',
                'max:30',
            ],
        ]);


        if (!empty($data['document_number'])) {
            $user = User::where(
                'document_number',
                $data['document_number']
            )->first();

            if ($user) {
                $userEmail = $user->email;

                $email = substr($userEmail, 0, 3)
                    . '....'
                    . substr($userEmail, -6);

                $validator->after(function ($validator) use ($data, $email) {
                    $validator->errors()->add(
                        'document_number',
                        'The document number '
                        . $data['document_number']
                        . ' is already registered. If this is your account, please sign in using your email '
                        . $email
                        . '.'
                    );
                });
            }
        }

        return $validator;
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => '',
                'email' => $data['email'],
                'document_type' => $data['document_type'],
                'document_number' => $data['document_number'],
                'password' => Hash::make($data['password']),
                'status' => 'active',
                'photo' => 'default-profile.jpg',
            ]);

            $user->assignRole('Participante');

            $user->inscription()->create([
                'invoice_type' => 'Boleta',
                'status' => 'Draft',
            ]);

            return $user;
        });
    }
}
