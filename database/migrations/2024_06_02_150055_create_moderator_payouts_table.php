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
        Schema::create('moderator_payouts', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->integer('paid_by_user_id');
            $table->double('paid_amount')->default(0.00);
            $table->string('payment_method')->default('cash')->comment('cash/online_payment');
            $table->string('status')->default('full')->comment('full/partial');
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
        Schema::dropIfExists('moderator_payouts');
    }
};
