<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate as Gate;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
         'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('isAdmin', function (User $user) {
            return  $user->type === 'admin' ? Response::allow() : Response::deny('You must be an administrator.');
        });

        $gate->define('isCustomer', fn (User $user) => $user->type === 'customer');
    }
}
