<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTableAddOrderOriginColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumn('orders', 'order_origin')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_shop_for_me');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->string('order_origin', 15)->default('order')->after('store_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('orders', 'order_origin')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_origin');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_shop_for_me')->default(0)->after('store_id');
            });
        }
    }
}
