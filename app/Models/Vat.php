<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vat extends Model
{
    protected $table = 'vat';

    // START VAT related data getters
    /**
     * Get all Vats
     *
     * @param  none
     * @return array
     */
    public static function getAllVats()
    {
        $allVats = Vat::orderBy('from', 'DESC')->get();

        if(!$allVats) return [];
        return $allVats;
    }

    /**
     * Get VAT for date
     *
     * @param  none
     * @return array
     */
    public static function getVATForDate($date)
    {
        $vat = Vat::where('from', '<=', $date)->orderBy('from', 'DESC')->first();

        if(!$vat) return 19/100;
        return ($vat->vat/100);
    }
}
