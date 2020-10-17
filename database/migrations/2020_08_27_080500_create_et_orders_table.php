<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_orders', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code', 100);
            $table->string('boxoffice_id', 100);
            $table->bigInteger('event_id');
            $table->decimal('qty', 8, 2);
            $table->string('ticket_id',100);
            $table->decimal('sub_total', 8, 2);
            $table->string('discount_code', 100);
            $table->string('discount_amt', 100);
            $table->string('voucher_code', 100);
            $table->string('varchar_amt', 100);
            $table->string('customer_id', 100);
            $table->date('order_date');
            $table->time('order_time', 0);
            $table->decimal('grand_total', 8, 2);
            $table->enum('order_status', ['CO','P','C','VO']);
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
        Schema::dropIfExists('et_orders');
    }
}
