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
     * All customer requests
     * @route{GET} 'api/requests'
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request): ResourceCollection
    {
//        Later create filter by this collection.
        $collection = CustomerRequest::orderBy('created_at', 'desc')->paginate(10);
        return CustomerRequestResource::collection($collection);
    }

    /**
     * Save new customer request.
     * @route {POST} 'api/requests'
     * @param CustomerRequestSaveRequest $request
     * @return JsonResponse
     */
    public function store(CustomerRequestSaveRequest $request): JsonResponse
    {
        if (!$request->validated()) return $this->sendErrors('Validation failed');
        $data = $request->input();
        $customerRequest = CustomerRequest::create($data);
        NewCustomerRequestJob::dispatch($customerRequest);
        return $this->sendResponse($customerRequest, 'you request create successfully ! Answer you get by email !');
    }

    /**
     * @route {POST} 'api/requests/{id}'
     * @param $id
     * @return CustomerRequestItemResource
     */
    public function show($id):CustomerRequestItemResource
    {
        $customerRequest = CustomerRequest::where('id', $id)->firstOrFail();
        return new CustomerRequestItemResource($customerRequest);
    }

    /**
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
        $user = $request->user();
        $data = $request->input();
        if ($data['comment'] and $data['status'] !== CustomerRequest::RESOLVED) {
            return $this->sendErrors('Please check status! Or send question for customer email: ' . $customerRequest->email);
        }
        $data['comment'] = $request->input('comment') . PHP_EOL . ' Admin: ' . $user->name . ', replied to you.';
        if ($customerRequest->update($data)) {
            SendQuestionForCustomerJob::dispatch($customerRequest, $request->user());
            return $this->sendResponse('Email send customer: ' . $customerRequest->name . ' for he email: ' . $customerRequest->email,
                'Successfully, you commit is published !');
        } else {
            return $this->sendErrors('Something wrong ! ' . PHP_EOL . ' Please try again. ');
        }
    }

    /**
     * Destroy customer request.
     * @route {DELETE} 'api/requests/{id}'
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        if (Gate::denies('isAdmin')) return $this->sendErrors('Administrator only make update request !');
        $customerRequest = CustomerRequest::findOrFail($id);
        if ($customerRequest->status === CustomerRequest::RESOLVED and $customerRequest->comment) {
            NotifyAdminsByDeleteCustomerRequestJob::dispatch($customerRequest, $request->user());
            $customerRequest->destroy($id);
            return $this->sendResponse('Request delete successfully',
                'Customer request id: ' . $customerRequest->id . ' destroy, email\'s send customer and administrators');
        } else {
            return $this->sendErrors('Something wrong! Please try again later or resolved this request');
        }
    }

    /**
     * Search by name or email customer.
     * @route {GET} 'api/requests/search/{value}'
     * @param $value
     * @return mixed
     */
    public function searchByNameOrEmail($value): mixed
    {
        return CustomerRequest::where('name', 'like', '%' . $value . '%')
            ->orWhere('email', 'like', '%' . $value . '%')->paginate(12);
    }
}
