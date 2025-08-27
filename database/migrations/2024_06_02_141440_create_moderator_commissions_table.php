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
        Schema::create('moderator_commissions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('product_id');
            $table->integer('product_variation_id');
            $table->bigInteger('invoice_no');
            $table->double('total_amount')->default(0.00);
            $table->double('commission_rate')->default(0.00);
            $table->double('commission_amount')->default(0.00);
            $table->double('due_amount')->default(0.00);
            $table->string('status')->default('due')->comment('due/paid');
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
        Schema::dropIfExists('moderator_commissions');
    }
};
