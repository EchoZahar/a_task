<?php

namespace App\Jobs;

use App\Mail\NewCustomerRequestMail;
use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NewCustomerRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customerRequest;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CustomerRequest $customerRequest)
    {
        $this->customerRequest = $customerRequest;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $users = User::where('type', 'admin')->get();
        Mail::to($users)->send((new NewCustomerRequestMail($this->customerRequest))->from(env('MAIL_FROM_ADDRESS')));
    }
}
