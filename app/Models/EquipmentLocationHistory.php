<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EquipmentLocationHistory extends Model
{
    protected $table = 'equipment_location_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'equipment_id', 'location_id', 'customer_id', 'operator_id',
        'from', 'to'
    ];

    /**
     * The one to many relationship between equipment_location_history and equipment tables
     * get the equipment that this old location belongs to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    /**
     * The one to one relationship between equipment location history and location tables
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    /**
     * The one to one relationship between equipment location history and customers tables
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    /**
     * The one to one relationship between equipment location history and operators tables
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function operator(): HasOne
    {
        return $this->hasOne(Operator::class, 'id', 'operator_id');
    }
}
