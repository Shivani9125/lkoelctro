<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'electrician_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'area',
        'service_type',
        'urgency',
        'time_slot',
        'notes',
        'status',
    ];

    /**
     * Relationship: An electric service booking belongs to an Electrician.
     */
    public function electrician()
    {
        return $this->belongsTo(Electrician::class);
    }
}
