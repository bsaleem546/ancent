<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerRates extends Model
{
    protected $table = 'customer_rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id', 'name', 'checked', 'valid_from', 'valid_to', 'travel_costs', 'work_costs',
        'due_days', 'discount_days', 'discount_amount'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['customer'];

    /**
     * The one to many relationship between customer_rates and customers tables
     * get the customer that this rate belong to
     *
     * @param none
     * @return OneToMany relationship
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public static function getStandardRate($customer_id)
    {
        $stdRate = self::where('customer_id', '=', $customer_id)
            ->where('checked', '=', 1)
            ->first();

        return $stdRate;
    }
}
