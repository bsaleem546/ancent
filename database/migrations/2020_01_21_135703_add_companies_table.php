<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->default("");
            $table->string('street')->default("");
            $table->string('pc_town')->default("");

            $table->string('phone')->default("");
            $table->string('fax')->default("");
            $table->string('email')->default("");

            $table->string('turnover_tax_id')->default("");
            $table->string('company_reg_nr')->default("");
            $table->string('ceo')->default("");

            $table->string('personally_liable_partner_name')->default("");
            $table->string('personally_liable_partner_reg_nr')->default("");
            
            $table->string('bank')->default("");
            $table->string('bank_id')->default("");
            $table->string('iban')->default("");
            $table->string('bic')->default("");

            $table->string('logo')->default("");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
