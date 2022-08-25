<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\UserSignInRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/login",
     *     tags={"Auth Api"},
     *     summary="login",
     *     description="",
     *     @OA\RequestBody (
     *          required=true,
     *      ),
     *     @OA\Parameter (
     *         name="email",
     *         description="inter your email",
     *         required=true,
     *         in="path",
     *         @OA\Schema (type="email")
     *     ),
     *      @OA\Parameter (
     *         name="password",
     *         description="your password min lenght 8 symbols",
     *         required=true,
     *         in="path",
     *         @OA\Schema (type="password")
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfuly, OK",
     *      ),
     *     @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     *     @OA\Response(
     *          response=419,
     *          description="Authentication Timeout (Error CSRF)",
     *      ),
     *     @OA\Response(
     *          response=422,
     *          description="Bad request",
     *      ),
     * )
     * Login method.
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
            $response = [
                'user'          => $user,
                'token'         => $token,
                'user_agent'    => $request->server('HTTP_USER_AGENT'),
                'ip address'    => $request->ip()
            ];
            return $this->sendResponse($response, 'User signed in');
        } else {
            return $this->sendError('Unauthorised.', ['error' => 'Unauthorised']);
        }
    }
}
