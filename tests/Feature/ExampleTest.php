<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * The root path has no content of its own — it geo-redirects the
     * visitor to a locale prefix (IP → locale, falling back to the
     * configured default). So it answers 302, never 200.
     *
     * @return void
     */
    public function test_root_redirects_to_a_supported_locale()
    {
        $response = $this->get('/');

        $response->assertStatus(302);

        $supported = config('locales.supported', ['en']);
        $target = parse_url($response->headers->get('Location'), PHP_URL_PATH) ?? '';

        $this->assertContains(
            trim($target, '/'),
            $supported,
            "Root redirected to [{$target}], which is not a supported locale."
        );
    }
}
