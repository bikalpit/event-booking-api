<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_coupon', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('boxoffice_id',100);
            $table->string('coupon_title',100);
            $table->string('coupon_code',100);
            $table->date('valid_from');
            $table->integer('max_redemption');
            $table->enum('discount_type', ['P','F']);
            $table->string('discount',100);
            $table->date('valid_till');
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
        Schema::dropIfExists('et_coupon');
    }
}
