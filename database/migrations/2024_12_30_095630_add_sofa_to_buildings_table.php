<?php

// database/migrations/xxxx_xx_xx_xxxxxx_add_sofa_to_buildings_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSofaToBuildingsTable extends Migration
{
    public function up()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->string('sofa')->nullable(); // Add sofa column
        });
    }

    public function down()
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('sofa'); // Remove sofa column if rolled back
        });
    }
}
