<?php

namespace App\Mail;

use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifyAdminsByDeleteCustomerRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $customerRequest;

    public $admin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(CustomerRequest $customerRequest, User $user)
    {
        $this->customerRequest = $customerRequest;
        $this->admin = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Report about remove customer request #' . $this->customerRequest->id)->view('emails.setQuestionCustomer');
    }
}
