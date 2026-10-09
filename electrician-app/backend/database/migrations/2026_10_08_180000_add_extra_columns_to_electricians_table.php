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
        Schema::table('electricians', function (Blueprint $table) {
            $table->string('specialization')->nullable()->after('area');
            $table->string('experience')->nullable()->default('6+ Years')->after('specialization');
            $table->decimal('rating', 3, 2)->default(4.85)->after('experience');
            $table->unsignedInteger('completed_jobs')->default(120)->after('rating');
            $table->string('starting_price')->default('₹149')->after('completed_jobs');
            $table->string('badge')->default('Govt Certified Pro')->after('starting_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('electricians', function (Blueprint $table) {
            $table->dropColumn([
                'specialization',
                'experience',
                'rating',
                'completed_jobs',
                'starting_price',
                'badge',
            ]);
        });
    }
};
