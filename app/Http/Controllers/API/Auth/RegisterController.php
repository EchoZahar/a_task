<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\UserSignUpRequest;
use App\Models\User;

class RegisterController extends BaseController
{
    /**
     * Register new customer.
     * @route {POST} 'api/register'
     * @param UserSignUpRequest $request
     * @return mixed
     */
    public function signUp(UserSignUpRequest $request)
    {
        $credentials = $request->input();
        $credentials['password'] = bcrypt($request->input('password'));
        $user = User::create($credentials);
        if ($user) {
            $token = $user->createToken(config('app.name'), ['limited:token'])->plainTextToken;
            $response = ['user data: ' => $user, 'user token: ' => $token];
//            $log->writeSignUpLog($user, $token, $expired_at, $request->server('HTTP_USER_AGENT'), $request->ip());
            return $this->sendResponse($response, 'New user created  !');
        } else {
            return $this->sendErrors('unregistered', ['error' => 'Something wrong!']);
        }
    }
}
