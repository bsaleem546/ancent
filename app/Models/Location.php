<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory, Filterable;

    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'street', 'postal_code', 'place', 'country', 'contact_person','phone', 'fax', 'email',
        'gsm_email', 'notes', 'internal_notes', 'special_features', 'active_travel_cost', 'travel_costs', 'active_km', 'km_costs',
        'active', 'is_gsm'];
}
