<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Change10InPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->double('shipping_charges_gross',10,2)->nullable()->after('shipping_charges');
            $table->integer('shipping_markup_percentage')->nullable()->after('shipping_charges_gross');
            $table->double('shipping_markup_fee',10,2)->nullable()->after('shipping_markup_percentage');
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
            $table->dropColumn('shipping_charges_gross');
            $table->dropColumn('shipping_markup_percentage');
            $table->dropColumn('shipping_markup_fee');
        });
    }
}
