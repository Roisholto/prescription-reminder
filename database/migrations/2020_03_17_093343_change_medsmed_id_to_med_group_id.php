<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeMedsmedIdToMedGroupId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meds', function (Blueprint $table) {
            $table->renameColumn('med_id', 'med_group_id')->nullable(true) ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::table('meds', function (Blueprint $table) {
          $table->renameColumn('med_group_id', 'med_id')->nullable(false);
      });
    }
}
