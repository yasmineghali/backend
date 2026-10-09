<?php

namespace App\Console\Commands;

use App\Models\Contact;
use Illuminate\Console\Command;

class PruneContacts extends Command
{
    protected $signature = 'contacts:prune';

    protected $description = 'Erase old IP addresses from contact requests and delete requests past the retention period';

    public function handle(): int
    {
        $scrubbed = Contact::where('created_at', '<', now()->subDays(config('privacy.contact_ip_days')))
            ->where(fn ($q) => $q->whereNotNull('ip_address')->orWhereNotNull('user_agent'))
            ->update(['ip_address' => null, 'user_agent' => null]);

        $deleted = Contact::where('created_at', '<', now()->subDays(config('privacy.contact_retention_days')))
            ->delete();

        $this->info("Erased IP/user agent on {$scrubbed} contacts; deleted {$deleted} expired contacts.");

        return self::SUCCESS;
    }
}
