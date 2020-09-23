<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtBroadcastTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_broadcast', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('event_id',100);
            $table->string('recipients',100);
            $table->string('subject',100);
            $table->mediumText('message'); 
            $table->enum('send', ['IMM','AT_SED_DATE_TIME','AT_SED_ITR_BFO_EVT_ST','AT_SED_ITR_AFT_EVT_ND']);
            $table->string('terms',100);
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
        Schema::dropIfExists('et_broadcast');
    }
}
