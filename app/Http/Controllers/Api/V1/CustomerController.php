<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Domains\Customer\Repositories\CustomerRepositoryInterface;
use App\Http\Requests\CreateCustomerRequest;
use App\Http\Resources\CustomerResource;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerRepositoryInterface $customers
    ) {
        //
    }

    public function store(CreateCustomerRequest $request)
    {
        $customer = $this->customers->create(
            $request->validated()
        );

        return (new CustomerResource($customer))
            ->response()
            ->setStatusCode(201);
    }
}
