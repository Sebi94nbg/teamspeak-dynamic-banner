<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    protected User $user;

    public function __construct()
    {
        $this->user = User::all()->first();
    }

    /**
     * Test GET route response without authentication
     */
    public function test_get_route_response_without_authentication(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertStatus(302);
        $response->assertLocation(route('login'));
    }

    /**
     * Test GET '/' response with authentication
     */
    public function test_get_slash_response_with_authentication(): void
    {
        $response = $this->actingAs($this->user)->get('/');
        $response->assertStatus(302);
        $response->assertLocation(route('dashboard'));
    }
}
