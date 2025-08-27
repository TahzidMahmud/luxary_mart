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
        Schema::create('unit_translations', function (Blueprint $table) {
            $table->id();
            $table->integer('unit_id');
            $table->string('lang_key'); 
            $table->string('name'); 
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
        Schema::dropIfExists('unit_translations');
    }
};
