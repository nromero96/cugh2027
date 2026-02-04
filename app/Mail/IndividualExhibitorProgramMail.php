<?php

namespace App\Mail;

use App\Models\Inscription;
use App\Models\Program;
use App\Models\User;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

//log
use Illuminate\Support\Facades\Log;

class IndividualExhibitorProgramMail extends Mailable
{
    use Queueable, SerializesModels;

    public $inscription;
    public $user;
    public $programs;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($inscription, $user)
    {
        $this->user = $user;
        $this->inscription = $inscription;

        // Obtener los programas relacionados con esta inscripción
        $this->programs = Program::where('insc_id', $inscription->id)->get();

        //log $this->programs
        Log::info($this->programs);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $subject = $this->user->name . ' ' . $this->user->lastname . ' (' . $this->user->country . '): SU AGENDA PERSONALIZADA EN CUGH 2027.';
        return $this->view('emails.individual_exhibitor_program')
        ->subject($subject)
        ->attach('https://pdfobject.com/pdf/sample.pdf', [
            'as' => 'Programa-General-CUGH-2027-010424.pdf',
            'mime' => 'application/pdf',
        ]);
    
    }
}
