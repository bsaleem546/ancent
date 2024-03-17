<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInvoiceRequiredAndNoInvoiceToStatusEnumOnRepairsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('not_scheduled', 'roughly_scheduled', 'exactly_scheduled', 'repair_not_done', 'repair_done', 'offer_needed', 'reviewed_no_invoice', 'reviewed_invoice_required', 'invoice_generated', 'invoice_paid', 'invoice_required', 'no_invoice')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE repairs MODIFY COLUMN status ENUM('not_scheduled', 'roughly_scheduled', 'exactly_scheduled', 'repair_not_done', 'repair_done', 'offer_needed', 'reviewed_no_invoice', 'reviewed_invoice_required', 'invoice_generated', 'invoice_paid')");
    }
}
