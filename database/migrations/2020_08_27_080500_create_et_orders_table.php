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
            $table->string('unique_code',100);
            $table->string('boxoffice_id',100);
            $table->bigInteger('event_id');
            $table->bigInteger('customer_id');
            $table->date('order_date');
            $table->time('order_time', 0);
            $table->decimal('amount', 8, 2);
            $table->enum('status', ['CO','p','C','VO']);
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
