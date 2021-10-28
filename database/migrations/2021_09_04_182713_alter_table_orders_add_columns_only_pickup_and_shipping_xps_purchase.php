<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrdersAddColumnsOnlyPickupAndShippingXpsPurchase extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (!Schema::hasColumns('orders', ['only_pickup', 'shipping_xps_purchase'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('only_pickup')->default(0)->after('sales_tax');
                $table->boolean('shipping_xps_purchase')->default(0)->after('only_pickup');
                $table->date('pickup_date')->nullable()->after('shipping_xps_purchase');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumns('orders', ['only_pickup', 'shipping_xps_purchase'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn(['only_pickup', 'shipping_xps_purchase']);
            });
        }
    }
}
