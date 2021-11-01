<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class OrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('orders');
        
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('customer_id');
            $table->integer('warehouse_id');
            $table->integer('package_id');
            $table->string('tracking_number_in',32)->nullable();
            $table->string('tracking_number_out',32)->nullable();
            $table->enum('status',['arrived','labeled','shipped','delivered','rejected'])->default('arrived');
            $table->integer('package_weight')->default(0);
            $table->enum('weight_unit',['lb','kg'])->default('lb');
            $table->integer('package_length')->default(0);
            $table->integer('package_width')->default(0);
            $table->integer('package_height')->default(0);
            $table->enum('dim_unit',['in','cm'])->default('in');
            $table->integer('declared_value')->default(0);
            $table->text('notes')->nullable();
            $table->string('store_name')->nullable();
            $table->string('pickup_type')->nullable();
            $table->string('pickup_charges')->nullable();
            $table->boolean('is_changed')->nullable();
            $table->boolean('updated_by_admin')->nullable();
            $table->boolean('changes_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
