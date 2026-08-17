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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->foreignId('kategori_konten_id')->constrained('kategori_konten');
            $table->foreignId('kategori_creator_id')->constrained('kategori_creator');

            $table->string('url', 500);
            $table->string('username')->nullable();
            $table->string('platform', 50)->nullable();
            $table->text('caption')->nullable();
            $table->date('tanggal_upload')->nullable();

            $table->bigInteger('views')->default(0);
            $table->bigInteger('likes')->default(0);
            $table->bigInteger('comments')->default(0);
            $table->bigInteger('saves')->default(0);
            $table->bigInteger('shares')->default(0);
            $table->bigInteger('reposts')->default(0);

            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->decimal('saw_score', 8, 4)->default(0);
            $table->string('status_scraping', 50)->default('Belum Diproses');
            $table->timestamp('updated_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
