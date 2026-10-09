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
        Schema::create('contact_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_reference')->unique();
            $table->string('name');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('area')->nullable()->default('Lucknow');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->text('ai_diagnosis')->nullable();
            $table->string('ai_priority')->default('NORMAL');
            $table->string('ai_recommended_service')->nullable();
            $table->string('ai_estimated_cost')->nullable();
            $table->string('channel')->default('web_form');
            $table->string('status')->default('received');
            $table->boolean('email_sent')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_inquiries');
    }
};
