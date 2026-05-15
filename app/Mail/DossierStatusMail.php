<?php

namespace App\Mail;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TicketStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $subjectLine;
    public $messageBody;
    public $actionUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Ticket $ticket, $subjectLine, $messageBody, $actionUrl = null)
    {
        $this->ticket = $ticket;
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
        $this->actionUrl = $actionUrl ?? route('client.suivi.public', $ticket->id);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
            ->view('emails.ticket_status');
    }
}
