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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique();
            $table->foreignId('electrician_id')->nullable()->constrained('electricians')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_address');
            $table->string('area')->nullable();
            $table->string('service_type');
            $table->string('urgency')->default('standard'); // standard, high, emergency
            $table->string('time_slot')->nullable();
            $table->text('notes')->nullable();
            $table->string('status')->default('confirmed'); // pending, confirmed, completed, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
