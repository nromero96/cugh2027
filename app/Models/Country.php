<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Country extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'phone',
        'price_type',
    ];

    // Usuarios cuya nacionalidad es este país
    public function nationalityUsers()
    {
        return $this->hasMany(User::class, 'nationality');
    }

    // Usuarios que residen en este país
    public function residenceUsers()
    {
        return $this->hasMany(User::class, 'country');
    }
    
}
