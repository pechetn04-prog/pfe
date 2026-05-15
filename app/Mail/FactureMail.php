<?php

namespace App\Mail;

use App\Models\Facture;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Queueable;

class FactureMail extends Mailable
{
    use Queueable, SerializesModels;

    public $facture;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    public function build()
    {
        $directory = storage_path('app/public/factures');
        $pdfPath = $directory . '/facture-'.$this->facture->id.'.pdf';

        if (!file_exists($pdfPath)) {
            $this->facture->load('ticket.intervention.pieces');
            $company = \App\Models\CompanySetting::first();
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('factures.pdf', [
                'facture' => $this->facture,
                'company' => $company
            ]);

            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
            $pdf->save($pdfPath);
        }

        return $this->subject('Votre facture SAV')
            ->view('emails.facture')
            ->attach($pdfPath);
    }
}