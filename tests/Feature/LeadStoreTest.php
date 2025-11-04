<?php

use App\Models\Contact;
use App\Models\Lead;
use App\Models\SalesPerson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_lead_with_valid_data(): void
    {

        $contact = Contact::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);


        $user = User::factory()->create();
        $salesPerson = SalesPerson::factory()->create([
            'user_id' => $user->id,
            'marketing_code' => 'ABC123',
        ]);

        $payload = [
            'leads' => [
                [
                    'contact_id' => $contact->id,
                    'marketing_code' => 'ABC123',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
            ]);


        $this->assertDatabaseHas('leads', [
            'contact_id' => $contact->id,
            'assigned_to' => $salesPerson->id,
            'status' => 'active',
            'pipeline' => 'Registered',
        ]);
    }


    public function test_can_create_lead_without_marketing_code(): void
    {

        $user = User::factory()->create();
        SalesPerson::factory()->create([
            'user_id' => $user->id,
        ]);

        $contact = Contact::factory()->create();

        $payload = [
            'leads' => [
                [
                    'contact_id' => $contact->id,
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);

        $response->assertStatus(201);


        $this->assertDatabaseHas('leads', [
            'contact_id' => $contact->id,
            'status' => 'active',
        ]);
    }


    public function test_validation_fails_when_contact_id_is_missing(): void
    {
        $payload = [
            'leads' => [
                [
                    'marketing_code' => 'ABC123',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leads.0.contact_id']);
    }


    public function test_validation_fails_when_contact_does_not_exist(): void
    {
        $payload = [
            'leads' => [
                [
                    'contact_id' => 99999, // Non-existent contact
                    'marketing_code' => 'ABC123',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leads.0.contact_id']);
    }


    public function test_validation_fails_when_marketing_code_does_not_exist(): void
    {
        $contact = Contact::factory()->create();

        $payload = [
            'leads' => [
                [
                    'contact_id' => $contact->id,
                    'marketing_code' => 'INVALID_CODE',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leads.0.marketing_code']);
    }


    public function test_validation_fails_when_leads_array_is_missing(): void
    {
        $response = $this->postJson('/api/v1/leads', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leads']);
    }


    public function test_validation_fails_when_leads_is_not_an_array(): void
    {
        $response = $this->postJson('/api/v1/leads', [
            'leads' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['leads']);
    }


    public function test_can_create_multiple_leads(): void
    {
        $contact1 = Contact::factory()->create();
        $contact2 = Contact::factory()->create();
        $user = User::factory()->create();
        $salesPerson = SalesPerson::factory()->create([
            'user_id' => $user->id,
            'marketing_code' => 'XYZ789',
        ]);

        $payload = [
            'leads' => [
                [
                    'contact_id' => $contact1->id,
                    'marketing_code' => 'XYZ789',
                ],
                [
                    'contact_id' => $contact2->id,
                    'marketing_code' => 'XYZ789',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);


        $response->assertStatus(201);


        $this->assertDatabaseHas('leads', ['contact_id' => $contact1->id]);
        $this->assertDatabaseHas('leads', ['contact_id' => $contact2->id]);
    }


    public function test_creating_lead_for_existing_contact_archives_old_lead(): void
    {
        $contact = Contact::factory()->create();
        $user = User::factory()->create();
        $salesPerson = SalesPerson::factory()->create([
            'user_id' => $user->id,
            'marketing_code' => 'ABC123',
        ]);


        $initialLead = Lead::factory()->create([
            'contact_id' => $contact->id,
            'assigned_to' => $salesPerson->id,
            'status' => 'active',
        ]);


        $payload = [
            'leads' => [
                [
                    'contact_id' => $contact->id,
                    'marketing_code' => 'ABC123',
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/leads', $payload);


        $response->assertStatus(201);


        $this->assertDatabaseHas('leads', [
            'id' => $initialLead->id,
            'status' => 'archived',
        ]);


        $this->assertDatabaseHas('leads', [
            'contact_id' => $contact->id,
            'status' => 'active',
        ]);
    }


}
