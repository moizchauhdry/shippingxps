<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsToWarehouse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('state',32);
            $table->string('city',32);
            $table->string('phone',16);
            $table->string('address',255);
            $table->string('contact_person',255);
            $table->string('email',255);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('state');
                $table->dropColumn('city');
                $table->dropColumn('phone');
                $table->dropColumn('address');
                $table->dropColumn('contact_person');
                $table->dropColumn('email');
            });
    }
}
