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
    Schema::create('fish_feedings', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('fish_name');
        $table->string('feed_type');
        $table->time('feeding_time');
        $table->float('feed_weight'); // gram
        $table->float('fish_weight')->nullable(); // optional
        $table->integer('fish_count')->nullable(); // optional
        $table->text('notes')->nullable();
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fish_feedings');
    }
};
