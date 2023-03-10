<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    protected User $user;

    public function __construct()
    {
        $this->user = User::all()->first();
    }

    /**
     * Test the login.
     */
    public function test_login(): void
    {
        $response = $this->actingAs($this->user)->post('/login');
        $response->assertStatus(302);
    }

    /**
     * Test the target location after the login.
     */
    public function test_target_location_after_login(): void
    {
        $response = $this->actingAs($this->user)->post('/login');
        $response->assertLocation('http://localhost/dashboard');
    }
}
