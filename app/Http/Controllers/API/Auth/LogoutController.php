<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\API\BaseController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class LogoutController extends BaseController
{
    /**
     * Logout app
     * @route {POST} 'api/logout'
     */
    public function logout(): JsonResponse
    {
        auth()->user()->currentAccessToken()->delete();
        return $this->sendResponse('Token removed successfully', 'Bay, see you later !');
    }
}
