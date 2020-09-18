<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtBoxOfficeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_box_office', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('box_office_name',100);
            $table->string('admin_id',100);
            $table->string('language',50);
            $table->string('timezone',100);
            $table->string('box_office_link',50);
            $table->enum('email_order_notification', ['Y','N']);
            $table->enum('account_owner', ['Y','N']);
            $table->string('add_email',50);
            $table->enum('hide_tailor_logo', ['Y','N']);
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
        Schema::dropIfExists('et_box_office');
    }
}
