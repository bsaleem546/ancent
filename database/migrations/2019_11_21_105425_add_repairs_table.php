<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('repairs', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('equipment_id')->default(0);
            $table->unsignedBigInteger('company_id')->default(1);

            $table->unsignedBigInteger('location_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('operator_id')->nullable();
            
            $table->unsignedBigInteger('user_id')->default(0);
            
            $table->date('repair_date')->nullable();
            $table->string('estimation')->nullable();
            $table->date('rough_schedule_start')->nullable();
            $table->date('rough_schedule_end')->nullable();
            $table->dateTime('exact_schedule_start')->nullable();
            $table->dateTime('exact_schedule_end')->nullable();
            $table->string('number')->default("");
            $table->string('related_repair')->nullable();
            $table->unsignedBigInteger('related_repair_id')->nullable();
            $table->enum('status', ['not_scheduled', 'roughly_scheduled', 'exactly_scheduled', 'repair_not_done', 'repair_done', 'offer_needed', 'reviewed_no_invoice', 'reviewed_invoice_required', 'invoice_generated', 'invoice_paid'])->default('not_scheduled');
            $table->text('work_description')->nullable();
            $table->text('internal_notes')->nullable();
            $table->boolean('offer_needed')->nullable();
            $table->boolean('had_offer_needed')->nullable();

            $table->boolean('repair_details_added')->default(0);
            $table->boolean('reviewed')->nullable();
            $table->decimal('hours_of_operations', 15, 2)->nullable();
            $table->unsignedBigInteger('rate_id')->default(0);
            $table->boolean('invoicing_needed')->nullable();
            $table->boolean('active_travel_cost')->nullable();
            $table->decimal('travel_costs', 8, 2)->nullable();
            $table->boolean('active_km')->nullable();
            $table->decimal('km_costs', 8, 2)->nullable();
            $table->boolean('active_per_km')->nullable();
            $table->decimal('km', 8, 2)->nullable();
            $table->decimal('costs_per_km', 8, 2)->nullable();

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
        Schema::dropIfExists('repairs');
        
    }
}
