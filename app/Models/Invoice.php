<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Invoice extends Model
{
    use Filterable;

    protected $table = 'invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id',
        'invoice_pdf_generated',
        'invoice_number_pref', 'invoice_number_year', 'invoice_number_suff', 'invoice_number', 'invoice_detailed_number', 'invoice_date', 'delivery_date', 'customer_id',
        'due_days', 'discount_days', 'discount_amount', 'due_date', 'discount_date', 'payment_date',
        'offer_number', 'offer_date', 'order_date', 'order_number', 'client',
        'es_price', 'rr_price', 'wh_price', 'empl_dr_price', 'dr_price', 'total', 'total_vat', 'total_with_vat'];

    /**
     * The one to one relationship between repairs and invoice
     * get the repair that this invoice belongs to
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function repair(): BelongsTo
    {
        return $this->belongsTo(Repair::class);
    }

    /**
     * The one to one relationship between invoice and company
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function company(): HasOne
    {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    /**
     * The one to one relationship between invoice and users
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id')
            ->with('userDetails');
    }

    /**
     * The one to one relationship between invoice and customers
     *
     * @param  none
     * @return OneToOne relationship
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id')->with('customerRates', 'validCustomerRates');
    }

    /**
     * The one to many relationship between invoice and extra services tables
     *
     * @param  none
     * @return OneToMany relationship
     */
    public function extraServices(): HasMany
    {
        return $this->hasMany(ExtraService::class)->orderByRaw("0 - position desc")->with('unit');
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

        foreach($newValues as $newVlaue) {
            if( array_key_exists('id', $newVlaue) && $newVlaue['id'] && $existingValue = $oldValues->get($newVlaue['id'])) {
                // Update existing rate
                $existingValue->fill($newVlaue);

                // if( $existingValue->isDirty() && !Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) return false;

                $existingValue->save();
                if($existingValue->getChanges()){
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
        foreach($oldValues as $deleted) {
            // if(!Auth::user()->hasAnyPermission(['create repair_details', 'write repair_details'])) return false;
            $results['deleted_ids'][] = $deleted->id;
            $deleted->delete();
        }

        return $results;
    }

    // START Invoice related data getters
    /**
     * Get the latest repair number
     *
     * @param  none
     * @return string
     */
    public static function getLastInvoiceNumberByYear($year)
    {
        $lastInvoiceNumber = self::select('invoice_number_suff')
            ->where('invoice_number_year', '=', $year)
            ->orderBy('invoice_number_suff', 'DESC')
            ->first();

        if(!$lastInvoiceNumber) return 0;
        return $lastInvoiceNumber->invoice_number_suff;
    }

    /**
     * Get the latest repair number
     *
     * @param  none
     * @return string
     */
    public function setTotalWithVatForDate($date)
    {
        $this->total = $this->es_price + $this->rr_price + $this->wh_price +$this->dr_price;
        error_log("TOTAL Price: ". $this->total);
        $vat = Vat::getVATForDate($date);
        error_log("VAT: ". $vat);
        $this->vat = $vat;
        $this->total_vat = $this->total * $vat;
        error_log("TOTAL VAT: ". $this->total_vat);
        $this->total_with_vat = $this->total + $this->total_vat;
        error_log("TOTAL price with VAT: ". $this->total_with_vat);
    }

    /**
     * Get the VAT value for invoice date
     *
     * @param  none
     * @return string
     */
    public function getVATForInvoiceDate()
    {
        $vat = Vat::getVATForDate($this->invoice_date ? $this->invoice_date : Carbon::now());
        return $vat*100;
    }

    /**
     * Get the VAT value for repair date
     *
     * @param  none
     * @return string
     */
    public function getVATForRepairDate()
    {
        $vat = Vat::getVATForDate($this->repair->repair_date ? $this->repair->repair_date : Carbon::now());
        return $vat*100;
    }

    /**
     * Get the VAT value for offer date
     *
     * @param  none
     * @return string
     */
    public function getVATForOfferDate()
    {
        $vat = Vat::getVATForDate($this->offer_date ? $this->offer_date : Carbon::now());
        return $vat*100;
    }
}
