<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerInvoicing extends Model
{
    protected $table = 'customer_invoicing';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'repair_id', 'employee_id',
        'worked_on', 'description',
        'work_h', 'work_min',
        'unit_cost', 'total_cost',
        'internal', 'driving_time'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['repairs'];


    /**
     * The one to one relationship between customer invoicing and employees
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    /**
     * The one to many relationship between customer_invoicing and repairs tables
     * get the repair that this customer invoicing belongs to
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function repairs(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }
}
