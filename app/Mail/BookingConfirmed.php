<?php

namespace App\Mail;

use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmed extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Booking $booking)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Бронирование подтверждено',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking.confirmed',
            with: [
                'subject' => $this->envelope()->subject,
                'beginDate' => Carbon::parse($this->booking->begin_date)->format('d.m.Y'),
                'endDate' => Carbon::parse($this->booking->end_date)->format('d.m.Y'),
                'roomName' => $this->booking->room->name,
            ],
        );
    }
}
