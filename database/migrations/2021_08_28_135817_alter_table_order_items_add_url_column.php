<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableOrderItemsAddUrlColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('order_items', 'url')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->string('url',255)->nullable()->after('batteries');
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
        if (Schema::hasColumn('order_items', 'url')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('url');
            });
        }
    }
}
