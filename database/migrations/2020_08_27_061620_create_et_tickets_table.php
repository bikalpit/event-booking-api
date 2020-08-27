<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->integer('event_id');
            $table->string('ticket_name',100);
            $table->decimal('prize', 8, 2);
            $table->integer('qty');
            $table->enum('advance_setting', ['Y','N']);
            $table->string('description',100);
            $table->decimal('booking_fee', 8, 2);
            $table->enum('status', ['OS','H','ACR','DSO','DU','OVA']);
            $table->integer('min_per_order');
            $table->integer('max_per_order');
            $table->enum('hide_untill', ['Y','N']);
            $table->enum('hide_after', ['Y','N']);
            $table->date('untill_date');
            $table->time('untill_time', 0);
            $table->date('after_date');
            $table->time('after_time', 0);
            $table->enum('sold_out', ['Y','N']);
            $table->enum('show_qty', ['Y','N']);
            $table->enum('discount', ['Y','N']);
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
        Schema::dropIfExists('et_tickets');
    }
}
