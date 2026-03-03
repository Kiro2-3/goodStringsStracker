<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardThemeTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_returns_ok_and_has_theme_script(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();

        // our inline script comment should be present
        $response->assertSee('preferred theme early injection');

        // the toggle button markup includes title attribute
        $response->assertSee('Toggle theme');
    }
}
