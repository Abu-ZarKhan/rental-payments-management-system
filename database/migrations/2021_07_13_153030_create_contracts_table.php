<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();

            $table->string('company') ->nullable();
            $table->date('start_date');
            $table->decimal('duration');
            $table->integer('rent_amount');
            $table->unsignedBigInteger('apartment_id')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->timestamps();
            $table->string('landlord_name') ->nullable();
            $table->string('land_location') ->nullable();
            $table->string('location')->nullable();
            $table->string('trade_license')->nullable();
            $table->string('nationality')->nullable();
            $table->string('eid_no')->nullable();
            $table->string('ejari')->nullable();
            $table->string('contact_no')->nullable();

            $table->foreign('apartment_id')->references('id')->on('apartments')->nullOnDelete();
            $table->foreign('tenant_id')->references('id')->on('tenants')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}
