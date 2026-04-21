<?php

declare(strict_types=1);

namespace Domains\Customer\Repositories;

use App\Models\Customer;

interface CustomerRepositoryInterface
{
    public function find(int $id): ?Customer;

    public function create(array $data): Customer;
}
