<?php

namespace Tests\Feature;

use App\Models\PassTypeSample;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PassTypeSamplesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_lists_samples_scoped_to_user_and_system(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $systemSample = PassTypeSample::create([
            'owner_user_id' => null,
            'source' => 'system',
            'name' => 'System Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => null,
            'fields' => ['description' => 'System'],
            'images' => [
                'icon' => 'asset-1',
                'logo' => 'asset-2',
                'strip' => 'asset-3',
                'thumbnail' => 'asset-4',
                'background' => 'asset-5',
                'footer' => 'asset-6',
            ],
        ]);

        $userSample = PassTypeSample::create([
            'owner_user_id' => $user->id,
            'source' => 'user',
            'name' => 'User Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => null,
            'fields' => ['description' => 'User'],
            'images' => [
                'icon' => 'asset-7',
                'logo' => 'asset-8',
                'strip' => 'asset-9',
                'thumbnail' => 'asset-10',
                'background' => 'asset-11',
                'footer' => 'asset-12',
            ],
        ]);

        PassTypeSample::create([
            'owner_user_id' => $otherUser->id,
            'source' => 'user',
            'name' => 'Other Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => null,
            'fields' => ['description' => 'Other'],
            'images' => [
                'icon' => 'asset-13',
                'logo' => 'asset-14',
                'strip' => 'asset-15',
                'thumbnail' => 'asset-16',
                'background' => 'asset-17',
                'footer' => 'asset-18',
            ],
        ]);

        $response = $this->actingAs($user)->getJson(route('passes.samples.index', [
            'source' => 'all',
        ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($systemSample->id));
        $this->assertTrue($ids->contains($userSample->id));
    }

    public function test_it_filters_by_pass_type_and_platform(): void
    {
        $user = User::factory()->create();

        PassTypeSample::create([
            'owner_user_id' => null,
            'source' => 'system',
            'name' => 'Base Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => null,
            'fields' => ['description' => 'Base'],
            'images' => [
                'icon' => 'asset-1',
                'logo' => 'asset-2',
                'strip' => 'asset-3',
                'thumbnail' => 'asset-4',
                'background' => 'asset-5',
                'footer' => 'asset-6',
            ],
        ]);

        $platformSample = PassTypeSample::create([
            'owner_user_id' => null,
            'source' => 'system',
            'name' => 'Apple Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => 'apple',
            'fields' => ['description' => 'Apple'],
            'images' => [
                'icon' => 'asset-7',
                'logo' => 'asset-8',
                'strip' => 'asset-9',
                'thumbnail' => 'asset-10',
                'background' => 'asset-11',
                'footer' => 'asset-12',
            ],
        ]);

        $response = $this->actingAs($user)->getJson(route('passes.samples.index', [
            'pass_type' => 'generic',
            'platform' => 'apple',
        ]));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($platformSample->id));
        $this->assertTrue($ids->count() >= 2);
    }

    public function test_user_can_create_and_delete_sample(): void
    {
        $user = User::factory()->create();

        $payload = [
            'name' => 'My Sample',
            'description' => 'Example',
            'pass_type' => 'generic',
            'platform' => 'apple',
            'fields' => ['description' => 'Test'],
            'images' => [
                'icon' => 'asset-1',
                'logo' => 'asset-2',
                'strip' => 'asset-3',
                'thumbnail' => 'asset-4',
                'background' => 'asset-5',
                'footer' => 'asset-6',
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('passes.samples.store'), $payload);

        $response->assertCreated();
        $sampleId = $response->json('id');

        $this->assertDatabaseHas('pass_type_samples', [
            'id' => $sampleId,
            'owner_user_id' => $user->id,
        ]);

        $deleteResponse = $this->actingAs($user)->deleteJson(route('passes.samples.destroy', [
            'sample' => $sampleId,
        ]));

        $deleteResponse->assertNoContent();
        $this->assertDatabaseMissing('pass_type_samples', ['id' => $sampleId]);
    }

    public function test_user_cannot_delete_other_user_sample(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $sample = PassTypeSample::create([
            'owner_user_id' => $otherUser->id,
            'source' => 'user',
            'name' => 'Other Sample',
            'description' => null,
            'pass_type' => 'generic',
            'platform' => null,
            'fields' => ['description' => 'Other'],
            'images' => [
                'icon' => 'asset-1',
                'logo' => 'asset-2',
                'strip' => 'asset-3',
                'thumbnail' => 'asset-4',
                'background' => 'asset-5',
                'footer' => 'asset-6',
            ],
        ]);

        $response = $this->actingAs($user)->deleteJson(route('passes.samples.destroy', [
            'sample' => $sample->id,
        ]));

        $response->assertForbidden();
    }
}
