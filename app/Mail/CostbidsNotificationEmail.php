<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;
use App\Models\CostBids;

class CostbidsNotificationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $costbid;
    protected $filename; // Tambahkan properti untuk nama file

    /**
     * Create a new message instance.
     */
    public function __construct(CostBids $costbid, $filename)
    {
        $this->costbid = $costbid; // Ubah penamaan variabel untuk konsistensi
        $this->filename = $filename;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Costbids Notification Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.costbids-emails.index',
            with: [
                'costbid' => $this->costbid // Pastikan nama variabel sesuai dengan view
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
        $filePath = public_path('storage/bids/analysis/' . $this->filename);
        if (file_exists($filePath)) {
            return [
                Attachment::fromPath($filePath)
                    ->as('costbids' . $this->costbid->code . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
