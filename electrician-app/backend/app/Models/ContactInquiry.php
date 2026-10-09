<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactInquiry extends Model
{
    use HasFactory;

    protected $table = 'contact_inquiries';

    protected $fillable = [
        'ticket_reference',
        'name',
        'email',
        'phone',
        'area',
        'subject',
        'message',
        'ai_diagnosis',
        'ai_priority',
        'ai_recommended_service',
        'ai_estimated_cost',
        'channel',
        'status',
        'email_sent',
    ];
}
