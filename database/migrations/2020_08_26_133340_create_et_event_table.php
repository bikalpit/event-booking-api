<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtEventTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('et_event', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code',100);
            $table->string('boxoffice_id',100);
            $table->string('event_title',100);
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time', 0);
            $table->time('end_time', 0);
            $table->string('venue_name',100);
            $table->string('postal_code',100);
            $table->integer('country');
            $table->enum('online_event', ['Y','N']);
            $table->string('description',100);
            $table->enum('platform', ['Z','GH','YU','HP','VM','SKY','OTH','N']);
            $table->string('event_link',100);
            $table->enum('event_status', ['draft','publish']);
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
        Schema::dropIfExists('et_event');
    }
}
