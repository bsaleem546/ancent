<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentManufacturer extends Model
{
    protected $table = 'equipment_manufacturer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['equipment'];
}
