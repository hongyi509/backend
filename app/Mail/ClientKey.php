<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientKey extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function build()
    {
        return $this->subject('Confirmation d\'achat')->markdown('emails.client.key');
    }
}
