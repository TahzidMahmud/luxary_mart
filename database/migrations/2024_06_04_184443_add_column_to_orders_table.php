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
        Schema::table('orders', function (Blueprint $table) {
            $table->text('order_receiving_date')->after('tracking_url')->nullable();
            $table->text('order_shipment_date')->after('order_receiving_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('orders', function (Blueprint $table) {
                $columns = ['order_receiving_date', 'order_shipment_date'];
                $table->dropColumn($columns);
            });
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
};
