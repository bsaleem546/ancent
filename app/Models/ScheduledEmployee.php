<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScheduledEmployee extends Model
{
    protected $table = 'repair_scheduled_employees';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repair_id', 'employee_id'
    ];

    /**
     * The one to one relationship between repair_scheduled_employees and employees
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    /**
     * The one to many relationship between repair_scheduled_employees and repairs tables
     * get the repair that this repair_scheduled_employees belong to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function repairs(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }
}
