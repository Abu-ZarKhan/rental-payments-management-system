<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_guest_chair_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGuestChairToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('guest_chair')->nullable(); // Add guest_chair column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('guest_chair');
        });
    }
}
