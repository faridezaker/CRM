<?php

namespace App\Services;

use App\Repositories\Interfaces\LeadRepositoryInterface;

class LeadService
{
    public function __construct(protected LeadRepositoryInterface $leadRepository)
    {

    }
}
