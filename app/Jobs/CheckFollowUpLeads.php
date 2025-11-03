<?php

namespace App\Jobs;

use App\Models\Contact;
use App\Models\Lead;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckFollowUpLeads implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $contacts = Contact::where('created_at', '<=', Carbon::now()->subHours(24))->get();

        foreach ($contacts as $contact) {
            $activeLead = Lead::where('contact_id', $contact->id)->where('status', 'active')->first();

            if ($activeLead && $activeLead->pipeline === 'registered') {

                $activeLead->update(['status' => 'archived']);

                Lead::create([
                    'contact_id' => $contact->id,
                    'assigned_to' => $activeLead->assigned_to,
                    'pipeline'=>'Follow up',
                    'status' => 'active',
                ]);

            }
        }
    }
}
