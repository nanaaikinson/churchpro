<?php

namespace App\Mail;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountVerificationMail extends Mailable
{
  use Queueable, SerializesModels;

  private string $emailTemplate = 'emails.account-verification';
  private string $emailSubject = 'Complete Your Account Creation - Verification Code Inside';

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(
    private readonly User $user,
    private readonly string $code,
    private readonly bool $resend = false
  ) {
    if ($this->resend) {
      $this->emailTemplate = 'emails.resend-verification';
      $this->emailSubject = 'Resend Verification Code - Complete Your Account Creation';
    }
  }

  /**
   * Get the message envelope.
   *
   * @return \Illuminate\Mail\Mailables\Envelope
   */
  public function envelope(): Envelope
  {
    return new Envelope(
      from: new Address(
        address: config('chsync.emails.info'),
        name: config('app.name'),
      ),
      subject: $this->emailSubject,
    );
  }

  /**
   * Get the message content definition.
   *
   * @return \Illuminate\Mail\Mailables\Content
   */
  public function content(): Content
  {
    return new Content(
      view: $this->emailTemplate,
      with: [
        'name' => $this->user->name,
        'code' => $this->code
      ],
    );
  }

  /**
   * Get the attachments for the message.
   *
   * @return array
   */
  public function attachments(): array
  {
    return [];
  }
}
