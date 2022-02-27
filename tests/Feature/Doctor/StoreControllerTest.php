<?php

namespace Tests\Feature\Doctor;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;


    public function test_create_new_doctor()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson('/api/doctor', [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email
        ])
            ->assertCreated();

        $this->assertDatabaseCount('doctors', 1);
    }
}
