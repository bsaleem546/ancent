<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TimeTracking extends Model
{
    protected $table = 'time_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repair_id', 'employee_id',
        'worked_on', 'description',
        'work_time_from_h', 'work_time_from_m', 'work_time_till_h', 'work_time_till_m',
        'drive_to_h', 'drive_to_m', 'drive_to_km',
        'drive_from_h', 'drive_from_m', 'drive_from_km'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repairs'];


    /**
     * The one to one relationship between time tracking and employees
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    /**
     * The one to many relationship between time_tracking and repairs tables
     * get the repair that this time tracking belongs to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function repairs(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }
}
