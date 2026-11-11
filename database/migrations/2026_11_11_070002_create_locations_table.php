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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. IDTPP, NLRTM, SGSIN, EGSCA, DEFRA
            $table->string('name'); // Tanjung Priok, Port of Rotterdam, etc.
            $table->string('type')->default('SEAPORT'); // SEAPORT, AIRPORT, CANAL, INLAND_HUB
            $table->string('country');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
