<?php

namespace App\Repositories\Interfaces;

interface SalesPersonRepositoryInterface {

    public function Create(array $data);

    public function MarcetingCode(int $marketing_code);
}
