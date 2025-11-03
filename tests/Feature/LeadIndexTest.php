<?php


use App\Models\Contact;
use App\Models\Lead;
use App\Models\SalesPerson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_get_their_leads(): void
    {
        $user = User::factory()->create([
            'email' => 'sales@example.com',
            'password' => bcrypt('password123'),
        ]);


        $salesPerson = SalesPerson::factory()->create([
            'user_id' => $user->id,
            'marketing_code' => '4562165',
        ]);


        $contact1 = Contact::factory()->create();
        $contact2 = Contact::factory()->create();


        Lead::factory()->create([
            'contact_id' => $contact1->id,
            'assigned_to' => $user->id,
            'status' => 'active',
            'pipeline' => 'Registered',
        ]);

        Lead::factory()->create([
            'contact_id' => $contact2->id,
            'assigned_to' => $user->id,
            'status' => 'active',
            'pipeline' => 'Follow up',
        ]);


        $otherUser = User::factory()->create();
        $otherSalesPerson = SalesPerson::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $otherContact = Contact::factory()->create();
        Lead::factory()->create([
            'contact_id' => $otherContact->id,
            'assigned_to' => $otherUser->id,
        ]);


        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/leads');


        $response->assertStatus(200)
            ->assertJsonStructure([
                'lead' => [
                    '*' => [
                        'id',
                        'status',
                        'pipeline',
                        'contact' => [
                            'id',
                            'name',
                            'email',
                        ],
                        'created_at',
                    ],
                ],
                'message',
            ])
            ->assertJsonCount(2, 'lead')
            ->assertJson([
                'message' => 'Return of leads related to sales person successfully',
            ]);
    }


    public function test_unauthenticated_user_cannot_get_leads(): void
    {
        $response = $this->getJson('/api/v1/leads');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }


    public function test_user_with_no_leads_gets_empty_array(): void
    {

        $user = User::factory()->create([
            'email' => 'sales@example.com',
            'password' => bcrypt('password123'),
        ]);


        SalesPerson::factory()->create([
            'user_id' => $user->id,
            'marketing_code' => '4562165',
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson('/api/v1/leads');

        $response->assertStatus(200)
            ->assertJsonCount(0, 'lead')
            ->assertJson([
                'message' => 'Return of leads related to sales person successfully',
            ]);
    }

    public function test_only_returns_leads_for_authenticated_sales_person(): void
    {

        $user1 = User::factory()->create();
        $salesPerson1 = SalesPerson::factory()->create([
            'user_id' => $user1->id,
        ]);


        $user2 = User::factory()->create();
        $salesPerson2 = SalesPerson::factory()->create([
            'user_id' => $user2->id,
        ]);

        $contact1 = Contact::factory()->create();
        $contact2 = Contact::factory()->create();


        Lead::factory()->create([
            'contact_id' => $contact1->id,
            'assigned_to' => $user1->id,
        ]);


        Lead::factory()->create([
            'contact_id' => $contact2->id,
            'assigned_to' => $user2->id,
        ]);

        $response = $this->actingAs($user1, 'api')
            ->getJson('/api/v1/leads');


        $response->assertStatus(200)
            ->assertJsonCount(1, 'lead');
    }
}
