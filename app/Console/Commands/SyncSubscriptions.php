<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class SyncSubscriptions extends Command
{
    protected $signature = 'sofortpdf:sync-subscriptions';

    protected $description = 'Lokale Abonnement-Status mit Stripe synchronisieren';

    public function handle(): int
    {
        $stripe = new StripeClient(config('services.stripe.secret'));

        $subscriptions = Subscription::where('stripe_price_id', 'like', '%sofortpdf_%')
            ->whereNotNull('stripe_subscription_id')
            ->get();

        $synced = 0;
        $discrepancies = 0;

        foreach ($subscriptions as $subscription) {
            try {
                $stripeSubscription = $stripe->subscriptions->retrieve(
                    $subscription->stripe_subscription_id
                );

                $stripeStatus = $stripeSubscription->status;

                if ($subscription->status !== $stripeStatus) {
                    $oldStatus = $subscription->status;
                    $subscription->update(['status' => $stripeStatus]);
                    $discrepancies++;

                    $message = "sofortpdf:sync-subscriptions — Abweichung: Abonnement {$subscription->stripe_subscription_id} "
                        . "lokal: {$oldStatus}, Stripe: {$stripeStatus}. Lokal aktualisiert.";
                    $this->warn($message);
                    Log::warning($message);
                }

                $synced++;
            } catch (\Exception $e) {
                $errorMessage = "sofortpdf:sync-subscriptions — Fehler bei Abonnement {$subscription->stripe_subscription_id}: {$e->getMessage()}";
                $this->error($errorMessage);
                Log::error($errorMessage);
            }
        }

        $summary = "sofortpdf:sync-subscriptions — {$synced} Abonnement(s) geprüft, {$discrepancies} Abweichung(en) korrigiert.";
        $this->info($summary);
        Log::info($summary);

        return 0;
    }
}
