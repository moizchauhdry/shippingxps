<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrdersUpdateOrderTypeColumnValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasColumn('orders', 'order_Type')) {

            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('order_Type');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->enum('order_Type', ['order', 'shopping', 'pickup'])
                    ->default('order')->after('notes');
                $table->unsignedBigInteger('store_id')->after('order_Type')->default(0);
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {

        if (Schema::hasColumn('orders', 'order_Type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['order_Type', 'store_id']);
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->enum('order_Type', ['order', 'shopping'])
                    ->default('order')->after('notes');
            });
        }
    }
}
