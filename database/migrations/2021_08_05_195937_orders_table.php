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
            
            // $table->integer('shipping_total')->default(0);  
            // $table->string('order_id',16)->nullable();

            $table->string('tracking_number_in',32)->nullable();            
            $table->string('tracking_number_out',32)->nullable();            
            $table->enum('status',['arrived','labeled','shipped','delivered','rejected'])->default('arrived');
            //pcakge
            $table->integer('package_weight')->default(0);
            $table->enum('weight_unit',['lb','kg'])->default('lb');
            $table->integer('package_length')->default(0);
            $table->integer('package_width')->default(0); 
            $table->integer('package_height')->default(0);  
            $table->enum('dim_unit',['in','cm'])->default('in');
            $table->integer('declared_value')->default(0);
            
            //sender
            /*
            $table->string('sender_fullname',32);                       
            $table->string('sender_address',255);
            $table->string('sender_email',96)->nullable();
            $table->string('sender_phone',96)->nullable();
            //receiver
            $table->string('receiver_fullname',32);                       
            $table->string('receiver_address',255);
            $table->string('receiver_email',96)->nullable();
            $table->string('receiver_phone',96)->nullable();
            */

            $table->text('notes')->nullable();
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
