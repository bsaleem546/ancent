<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $table = 'working_hours';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repair_id', 'employee_id', 'worked_on', 'description',
        'work_h', 'work_min', 'travel_h', 'travel_min'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repairs'];


    /**
     * The one to one relationship between working hours and employees
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    /**
     * The one to many relationship between working_hours and repairs tables
     * get the repair that this working_hour belong to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function repairs()
    {
        return $this->belongsTo(Repair::class);
    }
}
