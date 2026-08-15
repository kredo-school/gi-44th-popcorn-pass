<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PurchaseConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Reservation $reservation,
        public ?string $ticketUrl = null,
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject(
                "Booking Confirmed - {$this->reservation->reservation_reference}"
            )
            ->view('emails.purchase-confirmation');
    }
}
