<?php

namespace App\Services;

use App\Repositories\Interfaces\ContactRepositoryInterface;
use App\Repositories\Interfaces\LeadRepositoryInterface;
use App\Repositories\Interfaces\SalesPersonRepositoryInterface;
use App\Repositories\SalesPersonRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

class LeadService
{
    public function __construct(protected LeadRepositoryInterface $leadRepository, protected ContactRepositoryInterface $contactRepository, protected SalesPersonRepositoryInterface $salesPersonRepository)
    {

    }

    public function index($userId)
    {
        return $this->leadRepository->index($userId);
    }

    public function createLeadForContacts(array $data)
    {

        DB::beginTransaction();

        try {
            $salesPeople = $this->salesPersonRepository->allActive();
            if ($salesPeople->isEmpty()) {
                return [
                    'status' => false,
                    'message'=> 'No sales persons available',
                    'code'=> 400
                ];
            }

            $countSalesPeople = count($salesPeople);

            $lastIndex = Cache::get('last_assigned_sales_index', 0);
            $createdLeads = [];

            foreach ($data['leads'] as $lead) {
                $contact = $this->contactRepository->find($lead['contact_id']);

                if (!$contact) {
                    return [
                        'status' => false,
                        'message' => 'Contact not found'
                    ];
                }

                $existingLead = $this->leadRepository->findContact($lead['contact_id']);

                if ($existingLead) {
                    $this->leadRepository->updateStatus($lead['contact_id'], 'archived');
                }

                if (!empty($lead['marketing_code'])) {
                    $salesPerson = $this->salesPersonRepository->MarketingCode($lead['marketing_code']);

                    if (!$salesPerson) {
                        return [
                            'status' => false,
                            'message' => 'Marketing code not found'
                        ];
                    }

                    $lead['assigned_to'] = $salesPerson->id;
                }else {
                    $lead['assigned_to'] = $salesPeople[$lastIndex]->id;
                }

                $lead['contact_id'] = $contact->id;
                $lead['pipeline'] = 'Registered';
                $lead = $this->leadRepository->store($lead);
                $createdLeads[] = $lead;

                $lastIndex++;
                if ($lastIndex >= $countSalesPeople) {
                    $lastIndex = 0;
                }
                Cache::put('last_assigned_sales_index', $lastIndex);

            }
            DB::commit();

            return [
                'status' => true,
                'leads' => $createdLeads,
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
