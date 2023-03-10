<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    protected User $user;

    public function __construct()
    {
        $this->user = User::all()->first();
    }

    /**
     * Test the registration.
     */
    public function test_registration(): void
    {
        $response = $this->actingAs($this->user)->post('/register');
        $response->assertStatus(404);
    }
}
