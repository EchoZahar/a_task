<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\UserSignUpRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class RegisterController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth Api"},
     *     summary="Register new customer",
     *     description="",
     *     @OA\RequestBody (
     *          required=true,
     *      ),
     *     @OA\Parameter (
     *         name="name",
     *         description="inter your name",
     *         required=true,
     *         in="path",
     *         @OA\Schema (type="string")
     *     ),
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
     *      @OA\Response(
     *          response=422,
     *          description="Bad request",
     *      ),
     * )
     * Register new customer.
     * @route {POST} 'api/register'
     * @param UserSignUpRequest $request
     * @return JsonResponse
     */
    public function signUp(UserSignUpRequest $request): JsonResponse
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
