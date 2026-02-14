<?php

namespace Tests\Feature\Tiers;

use App\Models\AppleCertificate;
use App\Models\GoogleCredential;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TierGatesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user cannot request production unless requirements are met.
     */
    public function testUserCannotRequestProductionWithoutRequirements(): void
    {
        $user = User::factory()->approved()->create([
            'tier' => 'Email_Verified',
        ]);

        $response = $this->actingAs($user)->postJson('/api/tier/request-production');

        $response->assertStatus(422);
    }

    /**
     * Test unapproved users cannot access account settings.
     */
    public function testUnapprovedUserCannotAccessAccountSettings(): void
    {
        $user = User::factory()->pending()->create([
            'tier' => 'Email_Verified',
        ]);

        $response = $this->actingAs($user)->getJson('/api/account');

        $response->assertForbidden();
    }

    /**
     * Test user can request production only when both certs exist.
     */
    public function testUserCanRequestProductionWhenConfigured(): void
    {
        $user = User::factory()->approved()->create([
            'tier' => 'Verified_And_Configured',
        ]);

        AppleCertificate::factory()->create(['user_id' => $user->id]);
        GoogleCredential::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/api/tier/request-production');

        $response->assertSuccessful();
    }

    /**
     * Test user cannot go live unless in Production tier.
     */
    public function testUserCannotGoLiveWhenNotProduction(): void
    {
        $user = User::factory()->approved()->create([
            'tier' => 'Verified_And_Configured',
        ]);

        $response = $this->actingAs($user)->postJson('/api/tier/go-live');

        $response->assertStatus(422);
    }
}
