<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class IncomingExpiredNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new message instance.
     */
    public function __construct($incoming)
    {
        $this->data = $incoming;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Incoming Expired Notification Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.incoming-emails.index',
            with: [
                'incoming' => $this->data
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // Hanya tambahkan attachment jika ada
        if (!empty($this->data['attachment']) && $this->data['attachment'] !== 'N/A') {
            $filePath = public_path('storage/incomings/file/' . $this->data['attachment']);
            if (file_exists($filePath)) {
                return [
                    Attachment::fromPath($filePath)
                        ->as('incoming-' . ($this->data['code'] ?? 'document') . '.pdf')
                        ->withMime('application/pdf'),
                ];
            }
        }

        return [];
    }
}
