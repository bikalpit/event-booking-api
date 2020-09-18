<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtApiTokenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_api_token', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('user_id',100);
            $table->string('token',100);
            $table->enum('user_type', ['SA', 'A', 'EO', 'OM']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('et_api_token');
    }
}
