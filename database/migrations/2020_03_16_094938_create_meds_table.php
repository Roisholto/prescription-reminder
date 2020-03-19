<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('meds', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id') ;
            $table->timestamps();
            $table->integer('med_id') ;
            $table->string('name') ;
            $table->string('dosage') ;
            $table->string('interval') ;
            $table->string('dosage_count') ;
            $table->string('start_at') ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('meds');
    }
}
