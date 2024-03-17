<?php

namespace App\Models;

use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Equipment extends Model
{
    use Filterable;

    protected $table = 'equipment';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['number', 'manufacture_year', 'manufacture_no', 'next_uvv',
        'next_checkup', 'model', 'type_id', 'manufacturer_id', 'location_id',
        'customer_id', 'operator_id', 'notes', 'internal_notes', 'active', 'maintenance_contract'];


    /**
     * The one to one relationship between equipment and location tables
     *
     * @param none
     * @return OneToOne relationship
     */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class, 'id', 'location_id');
    }

    /**
     * The one to one relationship between equipment and customer tables
     *
     * @param none
     * @return OneToOne relationship
     */
    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class, 'id', 'customer_id');
    }

    /**
     * The one to one relationship between equipment and operator tables
     *
     * @param none
     * @return OneToOne relationship
     */
    public function operator(): HasOne
    {
        return $this->hasOne(Operator::class, 'id', 'operator_id');
    }

    /**
     * The one to one relationship between equipment and equipment manufacturer tables
     *
     * @param none
     * @return OneToOne relationship
     */
    public function equipmentManufacturer(): HasOne
    {
        return $this->hasOne(EquipmentManufacturer::class, 'id', 'manufacturer_id');
    }

    /**
     * The one to one relationship between equipment and equipment type tables
     *
     * @param none
     * @return OneToOne relationship
     */
    public function equipmentType(): HasOne
    {
        return $this->hasOne(EquipmentType::class, 'id', 'type_id');
    }

    /**
     * The one to many relationship between equipment and equipment_location_history tables
     *
     * @param none
     * @return OneToMany relationship
     */
    public function equipmentLocationHistory(): HasMany
    {
        return $this->hasMany(EquipmentLocationHistory::class)->orderBy('to', 'DESC');
    }

    /**
     * The one to many relationship between equipment and repairs
     *
     * @param none
     * @return OneToMany relationship
     */
    public function repairs(): HasMany
    {
        return $this->hasMany(Repair::class)->orderByRaw('ISNULL(repair_date) DESC')->orderBy("repair_date", "DESC")->orderBy("created_at", "DESC");
    }

    // START Equipment related data getters

    /**
     * Get all the manufacture years
     *
     * @param none
     * @return array
     */
    public static function getAllManufactureYears()
    {
        $allManufactureYears = self::select('manufacture_year')
            ->distinct('manufacture_year')
            ->orderBy('manufacture_year', 'DESC')
            ->pluck('manufacture_year');

        if (!$allManufactureYears) return [];
        return $allManufactureYears;
    }
}
