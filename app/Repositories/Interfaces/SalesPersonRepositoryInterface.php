<?php

namespace App\Repositories\Interfaces;

interface SalesPersonRepositoryInterface {

    public function Create(array $data);

    public function MarketingCode(string $marketing_code);

    public function allActive();

}
