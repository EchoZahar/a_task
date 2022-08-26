<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\CustomerRequestSaveRequest;
use App\Http\Requests\CustomerRequestUpdateRequest;
use App\Http\Resources\CustomerRequestItemResource;
use App\Http\Resources\CustomerRequestResource;
use App\Jobs\NewCustomerRequestJob;
use App\Jobs\NotifyAdminsByDeleteCustomerRequestJob;
use App\Jobs\SendQuestionForCustomerJob;
use App\Models\CustomerRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Gate;

class CustomerRequestController extends BaseController
{
    /**
     * @OA\Info (
     *     title="Customers request test task, API swagger documentation",
     *     version="1.0.0",
     *     @OA\Contact(
     *          email="echo.zahar@gmail.com"
     *      )
     * )
     * @OA\Get (
     *     path="/api/requests",
     *     operationId=" ",
     *     tags={"Customer requests"},
     *     summary="Show list customer requests, with paginate 10 request for 1 page",
     *     @OA\Parameter (
     *        description="Filtering by statsus. Statuses only two first Active second Resolved",
     *        name="status",
     *        in="query",
     *        @OA\Schema (
     *            type="string",
     *        )
     *     ),
     *     @OA\Parameter (
     *        description="Pagination for page",
     *        name="page",
     *        in="query",
     *        @OA\Schema (
     *            type="integer",
     *        )
     *     ),
     *     @OA\Parameter (
     *         description="Get customer requests by date.",
     *         name="date",
     *         in="query",
     *         @OA\Schema (
     *             type="datetime",
     *          ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *      ),
     * )
     *
     * @route{GET} '/api/requests'
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
        return CustomerRequestResource::collection(CustomerRequest::filter($request)->orderBy('created_at', 'desc')->paginate(10));
    }

    /**
     * Save new customer request.
     * @OA\Post(
     *      path="api/requests/",
     *      operationId="storeProject",
     *      tags={"Customer requests"},
     *      summary="Store new project",
     *      description="Returns project data",
     *      @OA\RequestBody(
     *          required=true,
     *      ),
     *     @OA\Parameter (
     *        description="Inter your name required field.",
     *        name="name",
     *        required=true,
     *        in="query",
     *        @OA\Schema (
     *            type="string",
     *        )
     *     ),
     *     @OA\Parameter (
     *        description="Inter email required field.",
     *        name="email",
     *        required=true,
     *        in="query",
     *        @OA\Schema (
     *            type="email",
     *        )
     *     ),
     *     @OA\Parameter (
     *         description="Your message maximum length 500, required field.",
     *         name="message",
     *         required=true,
     *         in="query",
     *         @OA\Schema (
     *             type="text",
     *          ),
     *     ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful created new request or validation failed",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request"
     *      ),
     * )
     * @route {POST} '/api/requests'
     * @param CustomerRequestSaveRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequestSaveRequest $request): JsonResponse
    {
        if (!$request->validated()) return $this->sendErrors('Validation failed');
        $customerRequest = CustomerRequest::create($request->input());
        NewCustomerRequestJob::dispatch($customerRequest);
        return $this->sendResponse($customerRequest, 'you request create successfully ! Answer you get by email !');
    }

    /**
     * @OA\Get (
     *     path="/api/requests/{id}",
     *     operationId="show",
     *     tags={"Customer requests"},
     *     summary="Show customer request item by id",
     *     @OA\Parameter (
     *         name="id",
     *         description="return customer request item",
     *         required=true,
     *         in="path",
     *         @OA\Schema (
     *             type="integer"
     *         )
     *      ),
     *     @OA\Response (
     *         response=200,
     *         description="success"
     *     ),
     *     @OA\Response (
     *         response=404,
     *         description="Not found.",
     *     )
     * )
     * @route {GET} 'api/requests/{id}'
     * @param $id
     * @return CustomerRequestItemResource
     */
    public function show($id): CustomerRequestItemResource
    {
        return (new CustomerRequestItemResource(CustomerRequest::findOrFail($id)));
    }

    /**
     * @OA\Put (
     *     path="/api/requests/{id}",
     *     operationId="update",
     *     tags={"Customer requests"},
     *     summary="Update customer request only by admin.",
     *     security={
     *         {"app_id": {}},
     *     },
     *     description="Reply from admin",
     *     @OA\Parameter (
     *         name="id",
     *         description="ID customer request",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Parameter (
     *         name="status",
     *         description="set status: Resolved",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter (
     *         name="comment",
     *         description="Comment by admin",
     *         required=true,
     *         in="path",
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Successfully update, OK",
     *      ),
     *      @OA\Response (
     *         response=401,
     *         description="Unauthorized"
     *      ),
     *      @OA\Response (
     *         response=403,
     *         description="Forbidden"
     *      ),
     *      @OA\Response (
     *         response=404,
     *         description="Not found"
     *      ),
     *      @OA\Response (
     *         response=419,
     *         description="Authentication Timeout (Error CSRF) "
     *      ),
     *     @OA\Response (
     *         response=422,
     *         description="Unprocessable Entity "
     *      )
     * )
     * Answer for admin.
     * @route {PUT} 'api/requests/{id}'
     * @param CustomerRequestUpdateRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(CustomerRequestUpdateRequest $request, int $id): JsonResponse
    {
        if (Gate::denies('isAdmin')) {
            return $this->sendErrors('Administrator only make update request !');
        }
        $customerRequest = CustomerRequest::findOrFail($id);
        $data = $request->input();
        if ($data['comment'] and $data['status'] !== CustomerRequest::RESOLVED) {
            return $this->sendErrors('Please check status! Or send question for customer email: ' . $customerRequest->email);
        }
        $data['comment'] = $request->input('comment') . PHP_EOL . ' Admin: ' . $request->user()->name ?? '' . ', replied to you.';
        if ($customerRequest->update($data)) {
            SendQuestionForCustomerJob::dispatch($customerRequest, $request->user());
            return $this->sendResponse('Email send customer: ' . $customerRequest->name . ' for he email: ' . $customerRequest->email,
                'Successfully, you commit is published !');
        } else {
            return $this->sendErrors('Something wrong ! ' . PHP_EOL . ' Please try again.');
        }
    }

    /**
     * @OA\Delete (
     *      path="/api/requests/{id}",
     *      operationId="destroy",
     *      tags={"Customer requests"},
     *      summary="Deleting an entry by admin only",
     *      security={
     *         {"app_id": {}},
     *      },
     *     description="Remove customer request",
     *     @OA\Parameter (
     *         name="id",
     *         description="Введите id обращения для удаления",
     *         required=true,
     *         in="path",
     *         @OA\Schema (type="integer")
     *     ),
     *     @OA\Response (
     *         response=200,
     *         description="Successfuly deleted, OK"
     *      ),
     *     @OA\Response (
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response (
     *         response=403,
     *         description="Forbidden"
     *     ),
     *     @OA\Response (
     *         response=404,
     *         description="Customer request not found."
     *     )
     * )
     * Destroy customer request.
     * @route {DELETE} 'api/requests/{id}'
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        if (Gate::denies('isAdmin'))
            return $this->sendErrors('Something wrong! Please try again later or resolved this request');
        $customerRequest = CustomerRequest::findOrFail($id);
        if ($customerRequest->status === CustomerRequest::RESOLVED and $customerRequest->comment) {
            NotifyAdminsByDeleteCustomerRequestJob::dispatch($customerRequest, $request->user());
            $customerRequest->destroy($id);
            return $this->sendResponse('Request delete successfully',
                'Customer request id: ' . $customerRequest->id . ' destroy, email\'s send customer and administrators');
        }
    }

    /**
     * @OA\Get (
     *     path="/api/requests/search/{value}",
     *     operationId="search",
     *     tags={"Customer requests"},
     *     summary="Search by name or email customer request.",
     *     @OA\Parameter (
     *         name="value",
     *         description="return customer request items",
     *         required=true,
     *         in="path",
     *         @OA\Schema (
     *             type="string"
     *         )
     *      ),
     *     @OA\Response (
     *         response=200,
     *         description="success"
     *     )
     * )
     * Search by name or email customer.
     * @route {GET} 'api/requests/search/{value}'
     * @param $value
     * @return mixed
     */
    public function searchByNameOrEmail($value): mixed
    {
        return CustomerRequest::where('name', 'like', '%' . $value . '%')->orWhere('email', 'like', '%' . $value . '%')->paginate(12);
    }
}
