<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_executive_chair_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExecutiveChairToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('executive_chair')->nullable(); // Add executive_chair column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('executive_chair');
        });
    }
}
