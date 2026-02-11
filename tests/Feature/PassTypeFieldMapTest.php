<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PassTypeFieldMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_pass_type_field_map(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('passes.fieldMap.index', [
            'pass_type' => 'generic',
            'platform' => 'apple',
        ]));

        $response->assertOk();
        $response->assertJsonFragment([
            'pass_type' => 'generic',
            'platform' => 'apple',
        ]);
        $this->assertContains('header', $response->json('field_groups'));
        $this->assertContains('primary', $response->json('field_groups'));
    }

    public function test_it_returns_404_for_unknown_pass_type(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(route('passes.fieldMap.index', [
            'pass_type' => 'unknown',
            'platform' => 'apple',
        ]));

        $response->assertNotFound();
    }
}
