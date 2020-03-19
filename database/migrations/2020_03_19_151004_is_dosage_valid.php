<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class IsDosageValid extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('meds', function (Blueprint $table) {
            //
        });
        $pdo = DB::connection()->getPdo();
        $pdo->query("ALTER TABLE meds ADD CONSTRAINT is_dosage_valid CHECK(dosage > 0 AND dosage_count > 0 AND dosage_count % dosage = 0) ") ;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('meds', function (Blueprint $table) {
            //
        });
        $pdo = DB::connection()->getPdo();
        $pdo->query("ALTER TABLE meds DROP CONSTRAINT is_dosage_valid") ;
    }
}
