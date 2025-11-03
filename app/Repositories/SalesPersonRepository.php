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

    public function MarketingCode(string $marketing_code)
    {
        return SalesPerson::where('marketing_code', $marketing_code)->first();
    }

    public function allActive()
    {
        return SalesPerson::all();
    }
}
