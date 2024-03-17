<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('repair_id');
            $table->unsignedBigInteger('company_id')->nullable();

            $table->date('generation_date')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->boolean('invoice_pdf_generated')->default(0);

            $table->string('invoice_number_pref')->nullable();
            $table->string('invoice_number_year')->nullable();
            $table->integer('invoice_number_suff')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('invoice_detailed_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->integer('due_days')->nullable();
            $table->integer('discount_days')->nullable();
            $table->decimal('discount_amount', 15, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->date('discount_date')->nullable();
            $table->date('payment_date')->nullable();
            
            $table->string('offer_number')->nullable();
            $table->date('offer_date')->nullable();
            $table->date('order_date')->nullable();
            $table->string('order_number')->nullable();
            $table->string('client')->nullable();

            $table->decimal('es_price', 15, 2)->nullable();
            $table->decimal('rr_price', 15, 2)->nullable();
            $table->decimal('wh_price', 15, 2)->nullable();
            $table->decimal('empl_dr_price', 15, 2)->nullable();
            $table->decimal('dr_price', 15, 2)->nullable();

            $table->decimal('total', 15, 2)->nullable();
            $table->decimal('total_vat', 15, 2)->nullable();
            $table->decimal('total_with_vat', 15, 2)->nullable();

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
        Schema::dropIfExists('invoices');
    }
}
