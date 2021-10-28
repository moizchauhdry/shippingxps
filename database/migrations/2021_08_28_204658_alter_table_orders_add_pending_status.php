<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrdersAddPendingStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        if (Schema::hasColumn('orders', 'status')) {
            
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });

            Schema::table('orders', function (Blueprint $table) {
                $table->enum('status', ['pending', 'arrived', 'labeled', 'shipped', 'delivered', 'rejected'])
                    ->default('arrived')->after('tracking_number_out');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        if (Schema::hasColumn('orders', 'status')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('status');
            });
            
            Schema::table('orders', function (Blueprint $table) {
                $table->enum('status', ['arrived', 'labeled', 'shipped', 'delivered', 'rejected'])
                    ->default('arrived')->after('tracking_number_out');
            });
        }
    }
}
