<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ExtraService extends Model
{
    protected $table = 'extra_services';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_id', 'position', 'count', 'name',
        'price', 'discount', 'unit_id'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['invoices'];

    /**
     * The one to one relationship between extra_services and units
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    /**
     * The one to many relationship between extra services and invoice tables
     * get the invoice that this extra service belong to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function invoices(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
