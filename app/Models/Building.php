<?php

namespace App\Models;

use App\Models\Contract;
use App\Models\Apartment;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Building extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'address',
        'number',
        'executive_table',
        'executive_chair',
        'guest_chair',
        'staff_workstations',
        'staff_chairs',
        'cabinet',
        'conference_room',
        'sofa',
        'cleaning',
        'parking',
        'drinking_water',
        'electricity',
        'internet',
        'refreshment_tea_coffee',
    ];

    public $translatable = ['address'];

    public function apartments()
    {
        return $this->hasMany(Apartment::class);
    }

    public function contracts() {
        return $this->hasManyThrough(Contract::class, Apartment::class);
    }
}
