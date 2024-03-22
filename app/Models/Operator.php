<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operator extends Model
{
    use HasFactory, Filterable;

    protected $table = 'operators';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'street', 'postal_code', 'place', 'country', 'contact_person','phone', 'fax', 'email',
        'gsm_email', 'notes', 'internal_notes', 'active'];

}
