<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Requests\CustomerRequestSaveRequest;
use App\Http\Requests\CustomerRequestUpdateRequest;
use App\Http\Resources\CustomerRequestItemResource;
use App\Http\Resources\CustomerRequestResource;
use App\Models\CustomerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomerRequestController extends BaseController
{
    public function index(Request $request)
    {
//        Later create filter by this collection .
        $collection = CustomerRequest::orderBy('created_at', 'desc')->paginate(10);
        $filter = $collection->fi;
        return CustomerRequestResource::collection($collection);
    }

    public function store(CustomerRequestSaveRequest $request)
    {
        if (!$request->validated()) return $this->sendErrors('Validation failed');
        $data = $request->input();
        $customerRequest = CustomerRequest::create($data);
        // send email this customer with some  one message.
        return $this->sendResponse($customerRequest, 'you request create successfully ! Answer you get by email !');
    }

    public function show($id)
    {
        $customerRequest = CustomerRequest::where('id', $id)->firstOrFail();
        return new CustomerRequestItemResource($customerRequest);
    }

    public function update(CustomerRequestUpdateRequest $request, int $id)
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
            // need add job for send customer email
            return $this->sendResponse('Email send customer: ' . $customerRequest->name . ' for he email: ' . $customerRequest->email,
                'Successfully, you commit is published !');
        } else {
            return $this->sendErrors('Something wrong ! ' . PHP_EOL . ' Please try again. ');
        }
    }

    public function destroy($id)
    {
        if (Gate::denies('isAdmin')) return $this->sendErrors('Administrator only make update request !');

        $customerRequest = CustomerRequest::findOrFail($id);
        if ($customerRequest->status === CustomerRequest::RESOLVED and $customerRequest->comment) {
            // here add job (send email admins and customer)
            $customerRequest->destroy($id);
            return $this->sendResponse('Request delete successfully',
                'Customer request id: ' . $customerRequest->id . ' destroy, email\'s send customer and administrators');
        } else {
            return $this->sendErrors('Something wrong! Please try again later or resolved this request');
        }
    }

    public function searchByNameOrEmail($value)
    {
        return CustomerRequest::where('name', 'like', '%' . $value . '%')
            ->orWhere('email', 'like', '%' . $value . '%')->paginate(12);
    }
}
