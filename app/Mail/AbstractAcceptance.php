<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbstractAcceptance extends Mailable
{
    use Queueable, SerializesModels;

    public const REPLY_TO = 'secretariat@cughlima2027.org';

    private $subjectLine;
    private $bodyHtml;
    private $pdfPath;
    private $pdfFilename;

    public function __construct(string $subjectLine, string $bodyHtml, string $pdfPath, string $pdfFilename)
    {
        $this->subjectLine = $subjectLine;
        $this->bodyHtml = $bodyHtml;
        $this->pdfPath = $pdfPath;
        $this->pdfFilename = $pdfFilename;
    }

    public function build()
    {
        return $this
            ->subject($this->subjectLine)
            ->replyTo(self::REPLY_TO)
            ->html($this->bodyHtml)
            ->attach($this->pdfPath, ['as' => $this->pdfFilename, 'mime' => 'application/pdf']);
    }
}
