<?php

namespace App\Repositories;

use App\Models\Lead;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\UserRepositoryInterface;

class LeadRepository implements LeadRepositoryInterface
{
    public function index()
    {
        $sales_person = auth()->user();
        return Lead::with('contact')->where('assigned_to', $sales_person->id)->paginate(10);
    }
}
