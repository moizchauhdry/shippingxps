<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Change02InPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->string('sq_customer_id', 100)->nullable();
            $table->json('sq_customer_response')->nullable();
            $table->string('sq_card_id', 100)->nullable();
            $table->json('sq_card_response')->nullable();
            $table->string('sq_payment_id', 100)->nullable();
            $table->json('sq_payment_response')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('sq_customer_id');
            $table->dropColumn('sq_customer_response');
            $table->dropColumn('sq_card_id');
            $table->dropColumn('sq_card_response');
            $table->dropColumn('sq_payment_id');
            $table->dropColumn('sq_payment_response');
        });
    }
}
