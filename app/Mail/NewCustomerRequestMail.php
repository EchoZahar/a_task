<?php

namespace App\Mail;

use App\Models\CustomerRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewCustomerRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerRequest;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CustomerRequest $customerRequest)
    {
        $this->customerRequest = $customerRequest;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New customer request #' . $this->customerRequest->id)->view('emails.newRequest');
    }
}
