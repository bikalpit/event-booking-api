<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EtPayment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_payment', function (Blueprint $table) {
            $table->id();
            $table->string('order_id', 100);
            $table->enum('payment_status', ['paid','unpaid']);
            $table->string('amount',100);
            $table->enum('payment_method', ['cash','card']);
            $table->decimal('sub_total', 8, 2);
            $table->string('transaction_id', 100);
            $table->string('event_id', 100);
            $table->string('boxoffice_id', 100);
            $table->string('customer_id', 100);
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
        //
    }
}
