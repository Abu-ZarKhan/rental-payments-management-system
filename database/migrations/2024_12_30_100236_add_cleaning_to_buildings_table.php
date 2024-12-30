<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_cleaning_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCleaningToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('cleaning')->nullable(); // Add cleaning column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('cleaning'); // Remove cleaning column if rolled back
        });
    }
}
