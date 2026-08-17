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
            if (!Schema::hasColumn('links', 'post_type')) {
                $table->string('post_type')->nullable()->after('username');
            }
            if (!Schema::hasColumn('links', 'caption')) {
                $table->text('caption')->nullable()->after('post_type');
            }
            if (!Schema::hasColumn('links', 'post_date')) {
                $table->timestamp('post_date')->nullable()->after('caption');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['post_type', 'caption', 'post_date']);
        });
    }
};
