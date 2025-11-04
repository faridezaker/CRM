<?php

namespace Tests\Unit;

use Tests\TestCase;
use Mockery;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\LeadService;
use App\Models\Lead;
use App\Models\Contact;
use App\Models\SalesPerson;

class LeadStoreServiceTest extends TestCase
{
    protected $leadRepo;
    protected $contactRepo;
    protected $salesRepo;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leadRepo = Mockery::mock('App\Repositories\LeadRepository');
        $this->contactRepo = Mockery::mock('App\Repositories\ContactRepository');
        $this->salesRepo = Mockery::mock('App\Repositories\SalesPersonRepository');

        $this->service = new LeadService(
            $this->leadRepo,
            $this->contactRepo,
            $this->salesRepo
        );
    }

    public function test_it_returns_error_when_no_sales_person_available()
    {
        $this->salesRepo->shouldReceive('allActive')->once()->andReturn(collect([]));

        $result = $this->service->createLeadForContacts([
            'leads' => [['contact_id' => 1]]
        ]);

        $this->assertFalse($result['status']);
        $this->assertEquals('No sales persons available', $result['message']);
        $this->assertEquals(400, $result['code']);
    }

    public function test_it_creates_lead_successfully()
    {
        $contact = new Contact(['id' => 1]);
        $salesPerson = new SalesPerson(['id' => 10]);
        $lead = new Lead(['id' => 1, 'status' => 'active', 'pipeline' => 'Registered']);

        $this->salesRepo->shouldReceive('allActive')->andReturn(collect([$salesPerson]));
        $this->contactRepo->shouldReceive('find')->andReturn($contact);
        $this->leadRepo->shouldReceive('findContact')->andReturn(null);
        $this->leadRepo->shouldReceive('store')->andReturn($lead);

        Cache::shouldReceive('get')->andReturn(0);
        Cache::shouldReceive('put');

        DB::shouldReceive('beginTransaction');
        DB::shouldReceive('commit');

        $result = $this->service->createLeadForContacts([
            'leads' => [
                ['contact_id' => 1, 'marketing_code' => null],
            ],
        ]);

        $this->assertTrue($result['status']);
        $this->assertEquals('created lead successfully', $result['message']);
        $this->assertCount(1, $result['leads']);
    }
}
