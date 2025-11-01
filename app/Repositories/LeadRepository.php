<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    public function index()
    {
        return Lead::with('contact', 'salesPerson', 'pipeline')->paginate(10);
    }
}
