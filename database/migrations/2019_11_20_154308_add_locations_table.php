<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
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
            $table->string('gsm_email')->default("");

            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('special_features')->nullable();

            $table->boolean('active_travel_cost')->default(0);
            $table->decimal('travel_costs', 8, 2)->default(0.00);

            $table->boolean('active_km')->default(0);
            $table->decimal('km_costs', 8, 2)->default(0.00);

            $table->boolean('active')->default(1);
            $table->boolean('is_gsm')->default(0);

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
        Schema::dropIfExists('locations');
    }
}
