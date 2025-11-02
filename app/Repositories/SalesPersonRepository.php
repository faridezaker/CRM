<?php

namespace App\Repositories;

use App\Models\SalesPerson;
use App\Repositories\Interfaces\SalesPersonRepositoryInterface;

class SalesPersonRepository implements SalesPersonRepositoryInterface
{
    public function create(array $data): SalesPerson
    {
        return SalesPerson::create($data);
    }

}
