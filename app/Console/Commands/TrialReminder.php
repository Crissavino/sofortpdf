<?php

namespace App\Console\Commands;

use App\Mail\TrialEndingMail;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class TrialReminder extends Command
{
    protected $signature = 'sofortpdf:trial-reminder';

    protected $description = 'Erinnerungs-E-Mail an Benutzer senden, deren Testzeitraum morgen endet';

    public function handle(): int
    {
        $tomorrow = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        $subscriptions = Subscription::where('stripe_price_id', 'like', '%sofortpdf_%')
            ->where('status', 'trialing')
            ->whereBetween('trial_ends_at', [$tomorrow, $tomorrowEnd])
            ->with('user')
            ->get();

        $sent = 0;

        foreach ($subscriptions as $subscription) {
            $user = $subscription->user;

            if (!$user) {
                continue;
            }

            Mail::to($user->email)->send(new TrialEndingMail($user));
            $sent++;
        }

        $message = "sofortpdf:trial-reminder — {$sent} Erinnerungs-E-Mail(s) gesendet.";
        $this->info($message);
        Log::info($message);

        return 0;
    }
}
