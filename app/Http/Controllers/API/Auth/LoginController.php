<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\UserSignInRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * Login customer.
     * @route {POST} 'api/login'
     * @param UserSignInRequest $request
     * @return JsonResponse
     */
    public function signIn(UserSignInRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken(config('app.name'), ['limited:token'])->plainTextToken;
//            Later need add logger service and send verify email
//              ($user, $token, $request->server('HTTP_USER_AGENT'), $request->ip())
            $response = [
                'user' => $user,
                'token' => $token,
            ];
            return $this->sendResponse($response, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
