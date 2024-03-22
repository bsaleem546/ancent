<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'street', 'pc_town',
        'phone', 'fax', 'email',
        'turnover_tax_id', 'company_reg_nr', 'ceo',
        'personally_liable_partner_name', 'personally_liable_partner_reg_nr',
        'bank', 'bank_id', 'iban', 'bic',
        'logo'];
}
