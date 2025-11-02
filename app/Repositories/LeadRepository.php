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

    public function findContact($contact_id)
    {
        return Lead::findOrFail($contact_id);
    }

    public function store(array $data)
    {
        return Lead::create($data);
    }

    public function updateStatus(int $contact_id, string $status)
    {
        return Lead::where('contact_id', $contact_id)->update(['status' => $status]);
    }

    public function countSalesPersonWithStatusActive()
    {
        //return Lead::with('status','active')->co
    }


}
