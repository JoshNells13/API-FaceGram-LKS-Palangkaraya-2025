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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follower_id',20)->constrained('users')->onDelete('cascade');
            $table->foreignId('following_id',20)->constrained('users')->onDelete('cascade');
            $table->tinyInteger('is_accepted')->default(0); 
            $table->timestamps();
    
            $table->unique(['following_id','follower_id']);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
