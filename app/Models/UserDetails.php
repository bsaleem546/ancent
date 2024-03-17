<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDetails extends Model
{
    protected $table = 'user_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'fax', 'sms', 'notes', 'customer_id', 'operator_id', 'customer_operator_filter'
    ];

    /**
     * All of the relationships to be touched.
     *
     * @var array
     */
    protected $touches = ['user'];

    /**
     * The one to one relationship between user_details and user tables
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The one to one relationship between user and customers tables
     *
     * @param  none
     * @return OneToOne relationship
     */
//    public function customer()
//    {
//        return $this->hasOne(Customer::class, 'id', 'customer_id');
//    }

    /**
     * The one to one relationship between user and customers tables
     *
     * @param  none
     * @return OneToOne relationship
     */
//    public function operator()
//    {
//        return $this->hasOne(Operator::class, 'id', 'operator_id');
//    }
}
