<?php

namespace App\Mail;

use App\Models\ContactInquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactInquiryMail extends Mailable
{
    use Queueable, SerializesModels;

    public ContactInquiry $inquiry;
    public bool $isCustomerReceipt;

    /**
     * Create a new message instance.
     */
    public function __construct(ContactInquiry $inquiry, bool $isCustomerReceipt = false)
    {
        $this->inquiry = $inquiry;
        $this->isCustomerReceipt = $isCustomerReceipt;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = $this->isCustomerReceipt
            ? "⚡ ElectroFix Received: {$this->inquiry->subject} [{$this->inquiry->ticket_reference}]"
            : "🚨 [{$this->inquiry->ai_priority}] Contact Inquiry: {$this->inquiry->subject} - {$this->inquiry->name} ({$this->inquiry->area})";

        $priorityColor = match ($this->inquiry->ai_priority) {
            'EMERGENCY' => '#ef4444',
            'URGENT' => '#f97316',
            'HIGH' => '#ea580c',
            default => '#2563eb',
        };

        $htmlContent = "
        <!DOCTYPE html>
        <html>
        <head>
          <meta charset='utf-8'>
          <title>{$subject}</title>
          <style>
            body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 24px; }
            .card { max-width: 620px; margin: 0 auto; background: #ffffff; border-radius: 12px; border: 1px solid #e2e8f0; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
            .header { background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: #ffffff; padding: 24px; text-align: left; }
            .header h2 { margin: 0 0 6px 0; font-size: 22px; }
            .header p { margin: 0; font-size: 13px; color: #94a3b8; }
            .priority-badge { display: inline-block; padding: 4px 12px; border-radius: 9999px; font-size: 12px; font-weight: bold; background: {$priorityColor}; color: #ffffff; margin-top: 10px; }
            .content { padding: 24px; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px; background: #f8fafc; padding: 14px; border-radius: 8px; border: 1px solid #e2e8f0; }
            .info-item label { display: block; font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 2px; }
            .info-item span { font-size: 14px; font-weight: 600; color: #0f172a; }
            .message-box { background: #ffffff; border-left: 4px solid #2563eb; padding: 14px 16px; margin-bottom: 20px; background: #eff6ff; border-radius: 4px; }
            .ai-box { background: #fdf4ff; border: 1.5px solid #f0abfc; padding: 16px; border-radius: 8px; margin-bottom: 20px; }
            .ai-box h4 { margin: 0 0 8px 0; color: #86198f; font-size: 15px; display: flex; align-items: center; gap: 6px; }
            .ai-box p { margin: 0 0 6px 0; font-size: 13.5px; line-height: 1.5; color: #4a044e; }
            .footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 16px 24px; text-align: center; font-size: 12px; color: #64748b; }
          </style>
        </head>
        <body>
          <div class='card'>
            <div class='header'>
              <h2>⚡ ElectroFix Lucknow — Service & Dispatch Desk</h2>
              <p>Ticket Reference: <strong>{$this->inquiry->ticket_reference}</strong> • Lucknow Fleet Support</p>
              <div class='priority-badge'>Priority: {$this->inquiry->ai_priority}</div>
            </div>
            <div class='content'>
              <div class='info-grid'>
                <div class='info-item'>
                  <label>Customer Name</label>
                  <span>" . htmlspecialchars($this->inquiry->name) . "</span>
                </div>
                <div class='info-item'>
                  <label>Email Address</label>
                  <span>" . htmlspecialchars($this->inquiry->email) . "</span>
                </div>
                <div class='info-item'>
                  <label>Phone Number</label>
                  <span>" . htmlspecialchars($this->inquiry->phone ?: 'Not provided') . "</span>
                </div>
                <div class='info-item'>
                  <label>Locality / Zone</label>
                  <span>" . htmlspecialchars($this->inquiry->area ?: 'Lucknow') . "</span>
                </div>
              </div>

              <div class='message-box'>
                <label style='display:block; font-size: 11px; text-transform: uppercase; color: #1d4ed8; font-weight: 700; margin-bottom: 6px;'>Customer Message & Query</label>
                <div style='font-size: 14px; line-height: 1.6; color: #1e3a8a; white-space: pre-wrap;'>" . htmlspecialchars($this->inquiry->message) . "</div>
              </div>

              " . (!empty($this->inquiry->ai_diagnosis) ? "
              <div class='ai-box'>
                <h4>🤖 ElectroFix AI Agent Diagnosis & Dispatch Advisory</h4>
                <p>" . nl2br(htmlspecialchars($this->inquiry->ai_diagnosis)) . "</p>
                " . (!empty($this->inquiry->ai_recommended_service) ? "<p style='margin-top:8px;'><strong>Recommended Service:</strong> " . htmlspecialchars($this->inquiry->ai_recommended_service) . " (" . htmlspecialchars($this->inquiry->ai_estimated_cost ?: 'Standard Rates') . ")</p>" : "") . "
              </div>
              " : "") . "

              <p style='font-size: 13px; color: #475569; margin-top: 16px;'>
                " . ($this->isCustomerReceipt
                  ? "We have logged your request. Our Lucknow dispatch team typically responds within 15–30 minutes for urgent requests, or you can call our 24/7 hotline directly at <strong>+91 98123 45678</strong>."
                  : "Dispatch action required: Please review this inquiry in the ElectroFix dashboard and assign the nearest verified technician.") . "
              </p>
            </div>
            <div class='footer'>
              &copy; " . date('Y') . " ElectroFix Lucknow (ElectroLKO) • Powered by ElectroFix Autonomous AI Agent
            </div>
          </div>
        </body>
        </html>
        ";

        return $this->subject($subject)
            ->html($htmlContent);
    }
}
