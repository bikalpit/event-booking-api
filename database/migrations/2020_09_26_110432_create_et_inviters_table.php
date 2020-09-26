<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtInvitersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_inviters', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code', 100);
            $table->string('admin_id', 100);
            $table->string('email_id', 100);
            $table->enum('status', ['P','APP']);
            $table->timestamp('invite_datetime', 0);
            $table->string('verify_token',100);
            $table->enum('role', ['SA','EO','A','OM']);
            $table->enum('permission', ['A', 'EM', 'OM', 'OV']);
            $table->string('sub_permission', 100);
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
 


        Schema::dropIfExists('et_inviters');
    }
}
