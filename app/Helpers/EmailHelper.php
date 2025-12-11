<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendEmail')) {
    /**
     * Global function to send emails.
     *
     * @param string $to Recipient email address
     * @param string $subject Email subject
     * @param string $body Email body (HTML or plain text)
     * @param array $attachments Optional attachments (array of file paths)
     * @param string $from Optional sender email (defaults to config)
     * @return bool Success status
     */
    function sendEmail($to, $subject, $body, $attachments = [], $from = null) {
        try {
            // Email configuration: Pull from Laravel's mail config or custom settings
            $fromEmail = $from ?: config('mail.from.address', 'noreply@example.com');
            $fromName = config('mail.from.name', 'Your App Name');

            // Use Laravel's Mail facade to send the email
            Mail::send([], [], function ($message) use ($to, $subject, $body, $attachments, $fromEmail, $fromName) {
                $message->to($to)
                         ->subject($subject)
                         ->from($fromEmail, $fromName)
                         ->setBody($body, 'text/html'); // HTML body

                // Add attachments if provided
                foreach ($attachments as $attachment) {
                    $message->attach($attachment);
                }
            });

            return true; // Success
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Email sending failed: ' . $e->getMessage());
            return false; // Failure
        }
    }
}
