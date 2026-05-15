<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $clientPassword;

    public function __construct(Ticket $ticket, $clientPassword = null)
    {
        $this->ticket = $ticket;
        $this->clientPassword = $clientPassword;
    }

    public function build()
    {
        return $this->subject('Ticket SAV créé')
            ->view('emails.ticket_created');
    }
}