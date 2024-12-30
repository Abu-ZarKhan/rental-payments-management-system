<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_electricity_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddElectricityToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('electricity')->nullable(); // Add electricity column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('electricity'); // Remove electricity column if rolled back
        });
    }
}
