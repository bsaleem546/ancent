<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use Filterable;

    protected $table = 'customers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'street', 'postal_code', 'place', 'country', 'contact_person', 'phone', 'fax', 'email',
        'notes', 'internal_notes', 'id_rw', 'vat_id', 'invoice_prefix', 'accounting_area', 'supplier_number', 'discount', 'active'];


    /**
     * The one to many relationship between customer and customer_rates tables
     *
     * @param none
     * @return OneToMany relationship
     */
    public function customerRates(): HasMany
    {
        return $this->hasMany(CustomerRates::class);
    }

    /**
     * The one to many relationship between customer and customer_rates tables
     *
     * @param none
     * @return OneToMany relationship
     */
    public function validCustomerRates(): HasMany
    {
        return $this->hasMany(CustomerRates::class)
            ->whereDate('valid_to', '>=', Carbon::now()->subYears(2))
            ->orderBy("checked", "desc");
    }


    /**
     * Sync rates.
     */
    public function syncRates($newRates)
    {
        $oldRates = $this->customerRates()->get()->keyBy('id');
        $results = [
            'created_ids' => [],
            'deleted_ids' => [],
            'updated_ids' => []
        ];

        foreach ($newRates as $newRate) {
            if (array_key_exists('id', $newRate) && $newRate['id'] && $existingRate = $oldRates->get($newRate['id'])) {
                // Update existing rate
                $existingRate->fill($newRate);
                $existingRate->save();
                if ($existingRate->getChanges()) {
                    $results['updated_ids'][] = $existingRate->id;
                }
                $oldRates->forget($newRate['id']);
            } else {
                // Create new rate
                $results['created_ids'][] = $this->customerRates()->create($newRate)->id;

            }
        }

        // Delete remaining rates in $oldRates
        foreach ($oldRates as $deleted) {
            $results['deleted_ids'][] = $deleted->id;
            $deleted->delete();
        }

        return $results;
    }
}
