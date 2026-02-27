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

        'sector',
        'other_sector',

        'area_of_work',
        'other_area_of_work',

        'how_did_you_hear_about',
        'other_how_did_you_hear_about',

        'why_attending',
        'other_why_attending',

        'ability_to_present_work',

        'how_is_your_attendance_funded',
        'other_how_is_your_attendance_funded',

        'your_areas_of_focus_in_global_health',
        'other_your_areas_of_focus_in_global_health',

        'obstacles_to_attending_cughs_conferences',
        'other_obstacles_to_attending_cughs_conferences',

        'receive_news_and_updates',
        'contact_info',
        'oral_poster_abstract_presenter',
        'panel_presenter_moderator',
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
        'sector' => 'array',
        'area_of_work' => 'array',
        'how_did_you_hear_about' => 'array',
        'why_attending' => 'array',
        'how_is_your_attendance_funded' => 'array',
        'your_areas_of_focus_in_global_health' => 'array',
        'obstacles_to_attending_cughs_conferences' => 'array',
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
