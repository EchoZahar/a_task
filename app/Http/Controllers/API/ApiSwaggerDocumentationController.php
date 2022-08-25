<?php

namespace App\Http\Controllers\API;

class ApiSwaggerDocumentationController
{
    /**
     * @OA\Info (
     *     title="Customers request test task, API swagger documentation",
     *     version="1.0.0",
     *     @OA\Contact(
     *          email="echo.zahar@gmail.com"
     *      )
     * )
     * @OA\Tag (
     *     name="Auth API",
     *     description="Auth methods: login, register, logout."
     * )
     * @OA\Tag (
     *     name="Customer requests",
     *     description="All customer make create request"
     * )
     *  * @OA\SecurityScheme (
     *     type="apiKey",
     *     in="header",
     *     name="X-APP-ID",
     *     securityScheme="X-APP-ID"
     * )
     */
}
