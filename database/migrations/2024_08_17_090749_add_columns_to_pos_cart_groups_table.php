<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pos_cart_groups', function (Blueprint $table) {
            $table->integer('customer_id')->nullable();
            $table->integer('shipping_address_id')->nullable();
            $table->double('discount')->default(0.00);
            $table->double('shipping')->default(0.00);
            $table->double('advance')->default(0.00);
            $table->string('payment_method')->nullable();
            $table->text('order_receiving_date')->nullable();
            $table->text('order_shipment_date')->nullable();
            $table->text('note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('pos_cart_groups', function (Blueprint $table) {
                $columns = [
                    'customer_id',
                    'shipping_address_id',
                    'discount',
                    'shipping',
                    'advance',
                    'payment_method',
                    'order_receiving_date',
                    'order_shipment_date',
                    'note'
                ];
                $table->dropColumn($columns);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
};
