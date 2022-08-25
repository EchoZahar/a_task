<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;

class LogoutController extends BaseController
{
    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"Auth Api"},
     *     summary="Logout",
     *     description="",
     *     @OA\Parameter (
     *         name="token",
     *         description="request user token and remove him",
     *         required=true,
     *         in="path",
     *         @OA\Schema (type="string")
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="Successfuly, OK",
     *      ),
     *     @OA\Response(
     *          response=401,
     *          description="unauthorized",
     *      ),
     *     @OA\Response(
     *          response=419,
     *          description="Authentication Timeout (Error CSRF)",
     *      ),
     * )
     * Logout method
     * @route {POST} 'api/logout'
     */
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->sendResponse('Token removed successfully', 'Bay, see you later !');
    }
}
