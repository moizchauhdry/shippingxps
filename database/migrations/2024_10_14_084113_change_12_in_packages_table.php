<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Change12InPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('xls_tracking_no', 100)->nullable();
            $table->string('xls_carrier_cost', 100)->nullable();
            $table->dateTime('xls_carrier_cost_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropColumn('xls_tracking_no');
            $table->dropColumn('xls_carrier_cost');
            $table->dropColumn('xls_carrier_cost_at');
        });
    }
}
