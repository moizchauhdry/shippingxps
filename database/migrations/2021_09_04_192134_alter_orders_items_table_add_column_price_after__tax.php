<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersItemsTableAddColumnPriceAfterTax extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('order_items', 'price_after_tax')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->float('price_after_tax')->default(0)->after('unit_price');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('order_items', 'price_after_tax')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('price_after_tax');
            });
        }
    }
}
