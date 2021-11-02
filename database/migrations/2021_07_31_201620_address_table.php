<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('fullname',64);
            $table->string('country_id',55);
            $table->string('state',32);
            $table->string('city',32);
            $table->string('phone',16);
            $table->string('address',255);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}
