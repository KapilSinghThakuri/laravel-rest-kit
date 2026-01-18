<?php

declare(strict_types=1);
// tests/Feature/ExceptionHandlingTest.php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test API route returns JSON on exception
     */
    public function test_api_route_returns_json_on_validation_error(): void
    {
        // API request without required fields
        $response = $this->postJson('/api/v1/users', []);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Validation failed',
            ]);

        // Verify it's JSON
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    /**
     * Test web route uses Laravel's default redirect on validation error
     */
    public function test_web_route_redirects_back_on_validation_error(): void
    {
        // Web form submission without required fields
        $response = $this->post('/profile/update', []);

        // Should redirect back (Laravel's default behavior)
        $response->assertRedirect();
        $response->assertSessionHasErrors();

        // Should NOT be JSON
        $this->assertNotEquals('application/json', $response->headers->get('Content-Type'));
    }

    /**
     * Test API route returns JSON on authentication error
     */
    public function test_api_route_returns_json_on_auth_error(): void
    {
        $response = $this->getJson('/api/v1/admin/settings');

        $response->assertStatus(401)
            ->assertJson([
                'success' => false,
                'message' => 'Unauthenticated',
            ]);
    }

    /**
     * Test web route redirects to login on authentication error
     */
    public function test_web_route_redirects_to_login_on_auth_error(): void
    {
        $response = $this->get('/admin/settings');

        // Should redirect to login (Laravel's default behavior)
        $response->assertRedirect('/login');

        // Should NOT be JSON
        $this->assertNotEquals('application/json', $response->headers->get('Content-Type'));
    }

    /**
     * Test API 404 returns JSON
     */
    public function test_api_404_returns_json(): void
    {
        $response = $this->getJson('/api/v1/nonexistent');

        $response->assertStatus(404)
            ->assertJson([
                'success' => false,
                'message' => 'API endpoint not found',
            ]);
    }

    /**
     * Test web 404 returns HTML
     */
    public function test_web_404_returns_html(): void
    {
        $response = $this->get('/nonexistent');

        $response->assertStatus(404);

        // Should be HTML, not JSON
        $this->assertStringContainsString('text/html', $response->headers->get('Content-Type'));
    }

    /**
     * Test AJAX request returns JSON (even from web route)
     */
    public function test_ajax_request_returns_json(): void
    {
        $response = $this->post('/profile/update', [], [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
            ]);
    }
}
