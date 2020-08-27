<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtEventSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_event_setting', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->integer('event_id');
            $table->string('timezone',100);
            $table->enum('make_donation', ['Y','N']);
            $table->string('event_button_title',100);
            $table->string('donation_title',100);
            $table->decimal('donation_amt', 8, 2);
            $table->string('donation_description',100);
            $table->enum('ticket_avilable', ['PB','SDT','SIB']);
            $table->enum('ticket_unavilable', ['TOS','SDT','SIB']);
            $table->enum('redirect_confirm_page', ['Y','N']);
            $table->string('redirect_url',100);
            $table->enum('hide_office_listing', ['Y','N']);
            $table->enum('customer_access_code', ['Y','N']);
            $table->string('access_code',100);
            $table->enum('hide_share_button', ['Y','N']);
            $table->enum('custom_sales_tax', ['Y','N']);
            $table->decimal('sales_tax_amt', 8, 2);
            $table->string('sales_tax_label',100);
            $table->bigInteger('sales_tax_id');
            $table->timestamps();
            $table->collation = "utf8_general_ci";
            $table->charset = 'utf8';
            $table->engine = "InnoDB";
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('et_event_setting');
    }
}
