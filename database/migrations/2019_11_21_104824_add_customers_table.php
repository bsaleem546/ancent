<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name')->default("");
            $table->string('street')->default("");
            $table->string('postal_code')->default("");
            $table->string('place')->default("");
            $table->string('country')->default("");
            $table->string('contact_person')->default("");
            $table->string('phone')->default("");
            $table->string('fax')->default("");
            $table->string('email')->default("");

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();

            $table->string('id_rw')->default("");
            $table->string('vat_id')->default("");
            $table->string('invoice_prefix')->default("");
            $table->string('accounting_area')->default("");
            $table->string('supplier_number')->default("");

            $table->decimal('discount', 5, 2)->default(0.00);

            $table->boolean('active')->default(1);

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
        Schema::dropIfExists('customers');
    }
}
