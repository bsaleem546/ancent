<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RepairReplacement extends Model
{
    protected $table = 'repair_replacements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repair_id', 'position', 'count', 'number_id',
        'description', 'price', 'discount', 'replacement_discount', 'unit_id'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repairs'];

    /**
     * The one to one relationship between repair_replacement and units
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    /**
     * The one to many relationship between repair_replacement and repairs tables
     * get the repair that this repair_replacement belong to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function repairs(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }
}
