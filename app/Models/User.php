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
        'phone_code_city',
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
    ];

    public function inscription()
    {
        return $this->hasOne(Inscription::class);
    }

}
