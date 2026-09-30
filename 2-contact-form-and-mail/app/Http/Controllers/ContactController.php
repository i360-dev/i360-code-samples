<?php

namespace App\Http\Controllers;

use App\Models\ContactSubmission;
use App\Services\MicrosoftGraphMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    /**
     * Display the contact page.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Handle contact form submission.
     */
    public function submit(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company'      => 'required|string|max:255',
            'email'        => 'required|email|max:255',
            'phone'        => 'nullable|string|max:30',
            'company_size' => 'nullable|string|in:1-10,10-25,25-50,50-100,100+',
            'interest'     => 'nullable|string|in:security-assessment,managed-it,ai-consulting,telecom,development,second-opinion,other',
            'message'      => 'nullable|string|max:5000',
        ]);

        // Honeypot — if the hidden 'website' field is filled, it's a bot
        if ($request->filled('website')) {
            return redirect()->route('contact.thanks');
        }

        // Timing check — reject if submitted too fast (under 3 seconds)
        if ($request->has('_form_loaded') && (time() - (int) $request->input('_form_loaded')) < 3) {
            return redirect()->route('contact.thanks');
        }

        // Store submission
        $submission = ContactSubmission::create($validated);

        // Send notification email to Insight360 team
        $this->sendNotificationEmail($submission);

        // Send confirmation email to the submitter
        $this->sendConfirmationEmail($submission);

        // Redirect to thank you
        return redirect()->route('contact.thanks');
    }

    /**
     * Display the thank you page.
     */
    public function thanks()
    {
        return view('contact-thanks');
    }

    /**
     * Send internal notification email.
     */
    protected function sendNotificationEmail(ContactSubmission $submission): void
    {
        try {
            $interestLabel = $submission->interest_label;

            $body = "New contact form submission from {$submission->name}\n\n";
            $body .= "Name: {$submission->name}\n";
            $body .= "Company: {$submission->company}\n";
            $body .= "Email: {$submission->email}\n";
            $body .= "Phone: " . ($submission->phone ?: 'Not provided') . "\n";
            $body .= "Company Size: " . ($submission->company_size ?: 'Not specified') . "\n";
            $body .= "Interest: {$interestLabel}\n";
            $body .= "Message: " . ($submission->message ?: 'No message provided') . "\n\n";
            $body .= "Submitted: {$submission->created_at->format('M j, Y g:i A')}\n";
            $body .= "---\nManage this lead at: " . url('/admin/contacts/' . $submission->id);

            app(MicrosoftGraphMailService::class)->send(
                to: config('insight360.notification_email', 'hello@i360-llc.com'),
                subject: "[New Lead] {$submission->name} — {$submission->company} ({$interestLabel})",
                body: nl2br(e($body)),
                replyTo: $submission->email,
                replyToName: $submission->name,
            );
        } catch (\Exception $e) {
            Log::error('Failed to send contact notification email: ' . $e->getMessage());
            // Don't fail the submission if email fails
        }
    }

    /**
     * Send confirmation email to the person who submitted.
     */
    protected function sendConfirmationEmail(ContactSubmission $submission): void
    {
        try {
            $body = "Hi {$submission->name},\n\n";
            $body .= "Thank you for reaching out to Insight360. We've received your request and someone from our team will be in touch within one business day.\n\n";
            $body .= "If you need immediate assistance, you can call us directly at (330) 983-4663.\n\n";
            $body .= "— The Insight360 Team\n";
            $body .= "www.i360-llc.com\n";

            app(MicrosoftGraphMailService::class)->send(
                to: $submission->email,
                subject: "We received your request — Insight360",
                body: nl2br(e($body)),
            );
        } catch (\Exception $e) {
            Log::error('Failed to send contact confirmation email: ' . $e->getMessage());
        }
    }
}
