<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_drinking_water_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDrinkingWaterToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('drinking_water')->nullable(); // Add drinking_water column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('drinking_water'); // Remove drinking_water column if rolled back
        });
    }
}
