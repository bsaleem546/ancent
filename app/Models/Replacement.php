<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Replacement extends Model
{
    protected $table = 'replacements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'group_id', 'number_id',
        'description', 'description2', 'price', 'discount', 'unit_id'
    ];

    /**
     * The one to one relationship between repair_replacement and units
     *
     * @param none
     * @return OneToOne relationship
     */
    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    // START Replacement related data getters

    /**
     * Get all groups
     *
     * @param none
     * @return array
     */
    public static function getAllGroups()
    {
        $allGroups = ReplacementGroup::orderBy('name')->get();

        if (!$allGroups) return [];
        return $allGroups;
    }
}
