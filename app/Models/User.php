<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Inscription;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'salutation',
        'name',
        'lastname',
        'second_lastname',
        'degrees',
        'other_degrees',
        'is_cugh_member',
        'cugh_member_institution',
        'job_title',
        'document_type',
        'document_number',
        'nationality',
        'gender',
        'occupation',
        'occupation_other',
        'workplace',
        'address',
        'city',
        'state',
        'country',
        'work_phone_code',
        'work_phone_code_city',
        'work_phone_number',
        'phone_code',
        'phone_number',
        'whatsapp_code',
        'whatsapp_number',
        'email',
        'cc_email',
        'password',
        'status',
        'photo',
        'solapin_name',
        'solapin_lastname',
        'confir_information',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_cugh_member' => 'boolean',
    ];

    public function inscription()
    {
        return $this->hasOne(Inscription::class);
    }

    // Nacionalidad (opcional)
    public function nationalityCountry()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }

    // País de residencia (opcional)
    public function residenceCountry()
    {
        return $this->belongsTo(Country::class, 'country');
    }

}
