<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtEventImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_event_image', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->integer('event_id');
            $table->string('image',100);
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
        Schema::dropIfExists('et_event_image');
    }
}
