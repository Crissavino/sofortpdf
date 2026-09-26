<?php

namespace Tests\Feature;

use App\Http\Controllers\Api\PaymentController;
use App\Http\Middleware\ResolveVad;
use App\Models\GoogleAdsDetail;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use ReflectionMethod;
use Tests\TestCase;

/**
 * utm_content carries the Google Ads ad group ID ({adgroupid} in the account
 * tracking template). It has to survive the whole path: URL → session →
 * google_ads_details, otherwise the BO can only attribute by campaign.
 */
class UtmContentAttributionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // google_ads_details lives in the shared `avocode` DB, which this repo
        // doesn't migrate. Point the default connection at an in-memory sqlite
        // and build just that table, so the insert never touches a real DB.
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);

        Schema::create('google_ads_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_id');
            $table->string('gclid')->nullable();
            $table->string('utm_source')->nullable();
            $table->string('utm_medium')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->boolean('converted')->default(false);
            $table->timestamp('converted_at')->nullable();
            $table->timestamps();
        });
    }

    private function captureUtmParams(Request $request): void
    {
        $method = new ReflectionMethod(ResolveVad::class, 'captureUtmParams');
        $method->setAccessible(true);
        $method->invoke($this->app->make(ResolveVad::class), $request);
    }

    public function test_it_captures_utm_content_from_the_url(): void
    {
        $this->captureUtmParams(
            Request::create('/?utm_source=google&utm_medium=cpc&utm_campaign=123&utm_content=456&utm_term=foo&gclid=abc')
        );

        $this->assertSame('456', session('utm_params.utm_content'));
        $this->assertSame('123', session('utm_params.utm_campaign'));
    }

    public function test_it_rescues_utm_content_from_the_referer(): void
    {
        $this->captureUtmParams(Request::create('/api/payment/create-customer', 'POST', [], [], [], [
            'HTTP_REFERER' => 'https://sofortpdf.com/?utm_source=google&utm_medium=cpc&utm_campaign=123&utm_content=456&utm_term=foo&gclid=abc',
        ]));

        $this->assertSame('456', session('utm_params.utm_content'));
    }

    public function test_save_google_ads_details_persists_utm_content(): void
    {
        $method = new ReflectionMethod(PaymentController::class, 'saveGoogleAdsDetails');
        $method->setAccessible(true);
        $method->invoke($this->app->make(PaymentController::class), [
            'utm_source' => 'google',
            'utm_medium' => 'cpc',
            'utm_campaign' => '123',
            'utm_content' => '456',
            'utm_term' => 'foo',
            'gclid' => 'abc',
        ], 99);

        $detail = GoogleAdsDetail::where('customer_id', 99)->firstOrFail();
        $this->assertSame('456', $detail->utm_content);
        $this->assertSame('123', $detail->utm_campaign);
    }
}
