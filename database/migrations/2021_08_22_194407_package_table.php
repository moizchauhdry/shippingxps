<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PackageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
                
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_no',16)->unique();
            $table->integer('customer_id');
            $table->integer('warehouse_id'); 
            $table->integer('package_handler_id')->nullable();
            $table->enum('status',['open','filled','labeled','shipped','delivered'])->default('open');
            
            //pcakge
            $table->integer('package_weight')->default(0);
            $table->enum('weight_unit',['lb','kg'])->default('lb');
            $table->integer('package_length')->default(0);
            $table->integer('package_width')->default(0); 
            $table->integer('package_height')->default(0);  
            $table->enum('dim_unit',['in','cm'])->default('in');
            $table->integer('declared_value')->default(0);
            $table->integer('shipping_total')->default(0);
            $table->enum('package_type',['merchandise','gift'])->default('merchandise');
            $table->integer('address_book_id')->default(0);
            $table->string('carrier_code')->nullable();
            $table->string('service_code')->nullable();
            $table->string('package_type_code')->nullable();
            $table->string('service_label')->nullable();
            $table->string('currency')->nullable();
            $table->string('tracking_number_out')->nullable();
            $table->boolean('consolidation_request')->default(false);
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
        Schema::dropIfExists('packages');
    }
}
