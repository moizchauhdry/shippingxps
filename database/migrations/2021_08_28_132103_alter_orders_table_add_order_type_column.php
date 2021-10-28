<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrdersTableAddOrderTypeColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumns('orders', ['order_type', 'site_name', 'site_url', 'shipping_from_shop', 'sales_tax'])) {
            Schema::table('orders', function (Blueprint $table) {
               $table->enum('order_type', ['order', 'shopping'])->default('order')->after('notes');
               $table->string('site_name',255)->nullable()->after('order_type');
               $table->string('site_url',255)->nullable()->after('site_name');
               $table->string('shipping_from_shop',255)->nullable()->after('site_url');
               $table->string('sales_tax',255)->nullable()->after('shipping_from_shop');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('orders', ['order_type', 'site_name', 'site_url', 'shipping_from_shop', 'sales_tax'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['order_type', 'site_name', 'site_url', 'shipping_from_shop', 'sales_tax']);
            });
        }
    }
}
