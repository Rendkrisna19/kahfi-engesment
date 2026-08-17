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
        Schema::table('links', function (Blueprint $table) {
            $table->bigInteger('prev_views')->nullable()->after('reposts');
            $table->bigInteger('prev_likes')->nullable()->after('prev_views');
            $table->bigInteger('prev_comments')->nullable()->after('prev_likes');
            $table->bigInteger('prev_shares')->nullable()->after('prev_comments');
            $table->bigInteger('prev_saves')->nullable()->after('prev_shares');
            $table->decimal('prev_engagement_rate', 5, 2)->nullable()->after('engagement_rate');
            $table->decimal('prev_saw_score', 8, 4)->nullable()->after('saw_score');
            $table->timestamp('last_rescraped_at')->nullable()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn([
                'prev_views',
                'prev_likes',
                'prev_comments',
                'prev_shares',
                'prev_saves',
                'prev_engagement_rate',
                'prev_saw_score',
                'last_rescraped_at',
            ]);
        });
    }
};
