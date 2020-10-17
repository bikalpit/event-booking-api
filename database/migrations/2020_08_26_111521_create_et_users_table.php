<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_users', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('firstname',100);
            $table->string('lastname',100);
            $table->string('email',100);
            $table->string('password',100);
            $table->string('phone',30);
            $table->enum('role', ['SA','EO','A','OM']);
            $table->enum('permission', ['A','EM','OM','OV']);
            $table->string('sub_permission',100)->nullable();
            $table->enum('status', ['Y','N']);
            $table->enum('email_verify', ['Y','N']);
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
        Schema::dropIfExists('et_users');
    }
}
