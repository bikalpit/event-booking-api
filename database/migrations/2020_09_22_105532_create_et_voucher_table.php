<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtVoucherTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_voucher', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('boxoffice_id',100);
            $table->string('voucher_name',100);
            $table->string('voucher_value',100);
            $table->string('voucher_code',100);
            $table->date('expiry_date');
            $table->string('event_id',100);
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
        Schema::dropIfExists('et_voucher');
    }
}
