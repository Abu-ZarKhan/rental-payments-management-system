<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_staff_chairs_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStaffChairsToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('staff_chairs')->nullable(); // Add staff_chairs column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('staff_chairs');
        });
    }
}
