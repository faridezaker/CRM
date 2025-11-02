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

    public function MarcetingCode(int $marketing_code)
    {
        return SalesPerson::where('marketing_code', $marketing_code)->first();
    }

}
