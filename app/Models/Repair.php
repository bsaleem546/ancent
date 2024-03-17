<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Repair extends Model
{
    use Filterable;

    protected $table = 'repairs';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['equipment_id', 'company_id', 'location_id', 'customer_id', 'operator_id', 'user_id', 'repair_blocked', 'repair_date', 'number', 'estimation', 'status', 'work_description',
        'rough_schedule_start', 'rough_schedule_end', 'exact_schedule_start', 'exact_schedule_end',
        'internal_notes', 'offer_needed', 'had_offer_needed', 'reviewed', 'hours_of_operations', 'rate_id', 'invoicing_needed', 'active_travel_cost', 'travel_costs', 'travel_cost_factor',
        'active_km', 'km_costs', 'active_per_km', 'km', 'costs_per_km', 'active'];

    /**
     * The one to many relationship between repairs and equipment
     * get the equipment that this repair belongs to
     *
     * @param none
     * @return OneToMany relationship
     */
//    public function equipment()
//    {
//        return $this->belongsTo(Equipment::class)->with('equipmentType', 'equipmentManufacturer', 'location', 'customer', 'operator', 'equipmentLocationHistory');
//    }

    /**
     * The one to one relationship between repair and company
     *
     * @param none
     * @return OneToOne relationship
     */
//    public function company()
//    {
//        return $this->hasOne(Company::class, 'id', 'company_id');
//    }

    /**
     * The one to one relationship between repair and location
     *
     * @param none
     * @return OneToOne relationship
     */
//    public function repairLocation()
//    {
//        return $this->hasOne(Location::class, 'id', 'location_id');
//    }

    /**
     * The one to one relationship between repair and customer
     *
     * @param none
     * @return OneToOne relationship
     */
//    public function repairCustomer()
//    {
//        return $this->hasOne(Customer::class, 'id', 'customer_id')->with('customerRates', 'validCustomerRates');
//    }

    /**
     * The one to one relationship between repair and operator
     *
     * @param none
     * @return OneToOne relationship
     */
//    public function repairOperator()
//    {
//        return $this->hasOne(Operator::class, 'id', 'operator_id');
//    }

    /**
     * The one to one relationship between repair and rate
     *
     * @param none
     * @return OneToOne relationship
     */
//    public function rate()
//    {
//        return $this->hasOne(CustomerRates::class, 'id', 'rate_id');
//    }

    /**
     * The one to one relationship between repairs and users
     *
     * @param none
     * @return OneToOne relationship
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')->with('userDetails');
    }

    /**
     * The one to many relationship between repairs and scheduled_employees tables
     *
     * @param none
     * @return OneToMany relationship
     */
//    public function scheduledEmployees()
//    {
//        return $this->hasMany(ScheduledEmployee::class);
//    }

    /**
     * The one to many relationship between repairs and time_tracking tables
     *
     * @param none
     * @return OneToMany relationship
     */
//    public function timeTracking()
//    {
//        return $this->hasMany(TimeTracking::class)->with('employee');
//    }

    /**
     * The one to many relationship between repairs and customer_invoicing tables
     *
     * @param none
     * @return OneToMany relationship
     */
//    public function customerInvoicing()
//    {
//        return $this->hasMany(CustomerInvoicing::class)->with('employee');
//    }

    /**
     * The one to many relationship between repairs and repair_replacements tables
     *
     * @param none
     * @return OneToMany relationship
     */
//    public function repairReplacements()
//    {
//        return $this->hasMany(RepairReplacement::class)->orderBy("position", 'asc')->with('unit');
//    }

    /**
     * The one to one relationship between repair and invoice
     *
     * @param none
     * @return OneToOne relationship
     */
    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class, 'repair_id', 'id')
            ->with('company', 'user');
    }


    /**
     * Sync rates.
     */
    public function syncOneToMany($newValues, $relation)
    {
        $oldValues = $relation->get()->keyBy('id');
        $results = [
            'created_ids' => [],
            'deleted_ids' => [],
            'updated_ids' => []
        ];

        foreach ($newValues as $newVlaue) {
            if (array_key_exists('id', $newVlaue) && $newVlaue['id'] && $existingValue = $oldValues->get($newVlaue['id'])) {
                // Update existing rate
                $existingValue->fill($newVlaue);

                // if( $existingValue->isDirty() && !Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) return false;

                $existingValue->save();
                if ($existingValue->getChanges()) {
                    $results['updated_ids'][] = $existingValue->id;
                }
                $oldValues->forget($newVlaue['id']);
            } else {
                // Create new rate
                // if(!Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) return false;
                $results['created_ids'][] = $relation->create($newVlaue)->id;

            }
        }

        // Delete remaining rates in $oldValues
        foreach ($oldValues as $deleted) {
            // if(!Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) return false;
            $results['deleted_ids'][] = $deleted->id;
            $deleted->delete();
        }

        return $results;
    }


    // START Repair related data getters

    /**
     * Get the latest repair number
     *
     * @param none
     * @return string
     */
    public static function getLastRepairNumber()
    {
        $lastRepairNumber = self::select('number')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$lastRepairNumber || $lastRepairNumber->number < 59999) return 59999;
        return $lastRepairNumber->number;
    }

    /**
     * Update all the repairs not invoiced with new rate
     *
     * @param none
     * @return string
     */
    public static function updateAllRepairsNotInvoicedForRate($old_rate_id, $new_rate, $startDate, $endDate)
    {
        error_log("Old rate id: " . $old_rate_id);
        error_log("Start Date: " . $startDate);
        error_log("End Date: " . $endDate);
        error_log("New st rate: " . json_encode($new_rate));

        $repairs = self::leftJoin('invoices', 'repairs.id', '=', 'invoices.repair_id')
            ->where('invoices.invoice_pdf_generated', '=', 0)
            // ->where(function ($query) use ($old_rate_id) {
            //     return $query->where('repairs.rate_id', '=', $old_rate_id)
            //                 ->orWhereNull('repairs.repair_date');
            // })
            ->where('repairs.rate_id', '=', $old_rate_id)
            ->whereNull('repairs.repair_date')
            // ->whereNotNull('repair_date')
            // ->whereBetween('repair_date', [$startDate, $endDate])
            ->get();

        error_log("Repairs: " . json_encode($repairs));

        $repairs = self::leftJoin('invoices', 'repairs.id', '=', 'invoices.repair_id')
            ->where('invoices.invoice_pdf_generated', '=', 0)
            // ->where(function ($query) use ($old_rate_id) {
            //     return $query->where('repairs.rate_id', '=', $old_rate_id)
            //                 ->orWhereNull('repairs.repair_date');
            // })
            ->where('repairs.rate_id', '=', $old_rate_id)
            ->whereNull('repairs.repair_date')
            // ->whereNotNull('repair_date')
            // ->whereBetween('repair_date', [$startDate, $endDate])
            ->update(['rate_id' => $new_rate['id']]);
        // ->get();

        error_log("Repairs: " . json_encode($repairs));
        return $repairs;
    }

    /**
     * Get the VAT value
     *
     * @param none
     * @return string
     */
//    public function getVATForDate($date)
//    {
//        $vat = Vat::getVATForDate($date);
//        return $vat;
//    }
}
