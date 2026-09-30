<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Transactional email on the institution's HTML template (§6.5).
 * Texts come from lang/{locale}/emails.php and can be edited in the
 * back-office (Contents & translations → emails).
 */
class BrandedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $template  key in emails.php (e.g. "submission_received")
     * @param  array<string, string>  $replace  placeholders (:reference, :name…)
     * @param  array<string, string>  $details  label => value summary table
     */
    public function __construct(
        public string $template,
        public array $replace = [],
        public array $details = [],
        public ?string $actionUrl = null,
        public ?string $replyToAddress = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __("emails.{$this->template}.subject", $this->replace),
            replyTo: $this->replyToAddress ? [$this->replyToAddress] : [],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.branded',
            text: 'emails.branded-text',
            with: [
                'heading' => __("emails.{$this->template}.heading", $this->replace),
                'body' => __("emails.{$this->template}.body", $this->replace),
                'next' => trans()->has("emails.{$this->template}.next") ? __("emails.{$this->template}.next", $this->replace) : null,
                'action' => trans()->has("emails.{$this->template}.action") ? __("emails.{$this->template}.action") : null,
                'actionUrl' => $this->actionUrl,
                'details' => $this->details,
            ],
        );
    }
}
