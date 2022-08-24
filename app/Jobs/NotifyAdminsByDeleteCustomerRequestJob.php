<?php

namespace App\Jobs;

use App\Mail\NotifyAdminsByDeleteCustomerRequestMail;
use App\Mail\SetQuestionForCustomerRequestMail;
use App\Models\CustomerRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class NotifyAdminsByDeleteCustomerRequestJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $customerRequest;
    public $admin;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(CustomerRequest $customerRequest, User $user)
    {
        $this->customerRequest = $customerRequest;
        $this->admin = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $admins = User::where('type', 'admin')->get();
            Mail::to($admins)
                ->send((new NotifyAdminsByDeleteCustomerRequestMail($this->customerRequest, $this->admin))
                    ->from($this->admin->email));
    }
}
