<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Crypt;

class RecoverPassword extends Mailable
{
    protected $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $token = $this->token;
        return $this->view('mailing.recover_password', [
            'token' => $token
        ]);
    }
}
