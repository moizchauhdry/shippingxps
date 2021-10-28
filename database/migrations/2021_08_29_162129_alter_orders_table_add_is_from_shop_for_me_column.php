<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTableAddIsFromShopForMeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('orders', 'is_shop_for_me')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_shop_for_me')->after('order_type')->default(0)
                    ->comment('This column will be used to identify how many orders are converted from shop for me or der to actual order');
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
        if (Schema::hasColumn('orders', 'is_shop_for_me')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_shop_for_me');
            });
        }
    }
}
