<?php

namespace App\Services;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\SalesPersonRepositoryInterface;
use App\Repositories\SalesPersonRepository;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class LeadService
{
    public function __construct(protected LeadRepositoryInterface $leadRepository, protected ContactRepositoryInterface $contactRepository, protected SalesPersonRepositoryInterface $salesPersonRepository)
    {

    }

    public function index()
    {
        return $this->leadRepository->index();
    }

    public function createLeadForContact($data)
    {

      DB::beginTransaction();

        try {
        $contact = $this->contactRepository->find($data['contact_id']);

        if (!$contact) {
            return [
                'status' => false,
                'message' => 'Contact not found'
            ];
        }

        $existingLead = $this->leadRepository->findContact($data['contact_id']);

        if ($existingLead) {
            $this->leadRepository->updateStatus($data['contact_id'], 'archived');
        }

        if (!empty($data['marketing_code'])) {
            $salesPerson = $this->salesPersonRepository->MarcetingCode($data['marketing_code']);

            if (!$salesPerson) {
                return [
                    'status' => false,
                    'message' => 'Marketing code not found'
                ];
            }

            $data['assigned_to'] = $salesPerson->id;
        }

        $data['pipeline'] = 'Registered';
        $lead = $this->leadRepository->store($data);
            DB::commit();

            return [
                'status' => true,
                'lead' => $lead,
                'message' => 'created lead successfully'
            ];
        } catch (Exception $e) {
            DB::rollBack();

            return [
                'error' => true,
                'message' => $e->getMessage(),
            ];
        }
    }


}
