<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * On 2026-10-07 customer 72329 paid a €0.69 trial at 09:49, was shown the
 * payment modal again at 09:51 by a cached tool page, and ended up with four
 * accounts, four trials and four €39.90 subscriptions for one entitlement.
 *
 * Two defects had to line up:
 *   1. the client gated on a boolean baked into cacheable HTML, so it still
 *      said "unpaid" after she paid  → /api/entitlement answers live now;
 *   2. the server's duplicate guard keyed on the typed email, so it could not
 *      fire for an address we had never seen → it keys on the session now.
 *
 * These tests pin both. The expensive one is
 * test_entitled_session_typing_a_brand_new_email_is_not_charged: that is the
 * exact shape of charges 2, 3 and 4.
 */
class AlreadyEntitledNoSecondChargeTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // customers/payments live in the shared `avocode` DB, which this repo
        // doesn't migrate. Same approach as UtmContentAttributionTest: an
        // in-memory sqlite with just the columns these paths touch, so a test
        // can never reach a real database.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->string('language')->nullable();
            $table->string('country')->nullable();
            $table->string('ip')->nullable();
            $table->unsignedInteger('website_id')->nullable();
            $table->string('remember_token')->nullable();
            $table->dateTime('last_time_connected')->nullable();
            $table->tinyInteger('came_from_ads')->nullable();
            $table->dateTime('create_time')->nullable();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->unsignedInteger('payment_status_id')->nullable();
            $table->dateTime('create_time')->nullable();
        });
    }

    private function entitledCustomer(string $email): Customer
    {
        $customer = Customer::create([
            'email'      => $email,
            'first_name' => 'Roxana',
            'last_name'  => 'Diaconu',
            'website_id' => (int) config('services.bo.website_id'),
        ]);

        // payment_status_id = 2 is what pay-trial sets, and what
        // hasSofortpdfSubscription() reads.
        Payment::create([
            'customer_id'       => $customer->id,
            'payment_status_id' => 2,
        ]);

        return $customer->refresh();
    }

    /** The regression that cost the money. */
    public function test_entitled_session_typing_a_brand_new_email_is_not_charged(): void
    {
        $paid = $this->entitledCustomer('roxmaria2002@yahoo.co.uk');

        $response = $this->actingAs($paid)->postJson('/api/payment/create-customer', [
            'email'             => 'roxmaria2013@gmail.com', // never seen before
            'full_name'         => 'Diaconu Roxana Maria',
            'payment_method_id' => 'pm_card_visa',
        ]);

        $response->assertOk()
            ->assertJson([
                'success'          => true,
                'already_entitled' => true,
                'customer_id'      => $paid->id,
            ]);

        // No second account, and nothing new to charge.
        $this->assertSame(1, Customer::count(), 'a second account was opened for an entitled session');
        $this->assertSame(1, Payment::count(), 'a second payment row was created');
        $this->assertDatabaseMissing('customers', ['email' => 'roxmaria2013@gmail.com']);
    }

    public function test_entitled_session_retyping_its_own_email_is_not_charged(): void
    {
        $paid = $this->entitledCustomer('roxmaria2002@yahoo.co.uk');

        $this->actingAs($paid)->postJson('/api/payment/create-customer', [
            'email'             => 'roxmaria2002@yahoo.co.uk',
            'full_name'         => 'Diaconu Roxana Maria',
            'payment_method_id' => 'pm_card_visa',
        ])->assertOk()->assertJson(['already_entitled' => true]);

        $this->assertSame(1, Payment::count());
    }

    /**
     * Guest typing a subscribed account's email: still a hard stop, but it has
     * to offer a way out. The dead end is what pushed her to a new address.
     */
    public function test_guest_using_a_subscribed_email_gets_a_login_url(): void
    {
        $this->entitledCustomer('roxmaria2002@yahoo.co.uk');

        $response = $this->postJson('/api/payment/create-customer', [
            'email'             => 'roxmaria2002@yahoo.co.uk',
            'full_name'         => 'Diaconu Roxana Maria',
            'payment_method_id' => 'pm_card_visa',
        ]);

        $response->assertStatus(409)
            ->assertJson(['success' => false, 'error' => ['code' => 'already_subscribed']]);

        $this->assertNotEmpty($response->json('login_url'), 'the 409 must carry somewhere to go');
    }

    public function test_entitlement_endpoint_answers_live_and_is_never_cached(): void
    {
        $guest = $this->getJson('/api/entitlement');
        $guest->assertOk()->assertJson(['subscribed' => false]);
        $this->assertStringContainsString('no-store', $guest->headers->get('Cache-Control'));

        $paid = $this->entitledCustomer('roxmaria2002@yahoo.co.uk');
        $this->actingAs($paid)->getJson('/api/entitlement')
            ->assertOk()
            ->assertJson(['subscribed' => true]);
    }

    /** A customer with no successful payment still has to be asked to pay. */
    public function test_unpaid_customer_is_not_treated_as_entitled(): void
    {
        $customer = Customer::create([
            'email'      => 'nopago@example.com',
            'website_id' => (int) config('services.bo.website_id'),
        ]);
        Payment::create(['customer_id' => $customer->id, 'payment_status_id' => 3]);

        $this->actingAs($customer)->getJson('/api/entitlement')
            ->assertOk()
            ->assertJson(['subscribed' => false]);
    }
}
