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
        Schema::create('product_variation_stocks', function (Blueprint $table) {
            $table->id();
            $table->integer('product_variation_id');
            $table->integer('warehouse_id')->nullable();
            $table->integer('stock_qty');
            $table->integer('created_by')->nullable(); 
            $table->timestamps();
            $table->integer('updated_by')->nullable(); 
            $table->softDeletes();
            $table->integer('deleted_by')->nullable(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variation_stocks');
    }
};
