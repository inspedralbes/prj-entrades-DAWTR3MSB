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
        Schema::create('seients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('esdeveniment_id')->constrained('esdeveniments')->onDelete('cascade');
            $table->integer('fila');
            $table->integer('numero');
            $table->string('estat')->default('disponible'); // disponible, reservat, venut
            $table->decimal('preu', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seients');
    }
};
