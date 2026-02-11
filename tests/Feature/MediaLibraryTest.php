<?php

namespace Tests\Feature;

use App\Models\MediaLibraryAsset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_upload_media_asset(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $file = UploadedFile::fake()->image('icon.png', 128, 128);

        $response = $this->actingAs($user)->postJson(route('passes.media.assets.store'), [
            'image' => $file,
            'slot' => 'icon',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('media_library_assets', [
            'owner_user_id' => $user->id,
            'source' => 'user',
            'slot' => 'icon',
        ]);
    }

    public function test_it_lists_system_and_user_assets_only(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $systemAsset = MediaLibraryAsset::create([
            'owner_user_id' => null,
            'source' => 'system',
            'slot' => 'icon',
            'path' => 'samples/icon.png',
            'url' => 'https://example.test/icon.png',
            'width' => 128,
            'height' => 128,
            'mime' => 'image/png',
            'size_bytes' => 123,
        ]);

        $userAsset = MediaLibraryAsset::create([
            'owner_user_id' => $user->id,
            'source' => 'user',
            'slot' => 'logo',
            'path' => 'user/logo.png',
            'url' => 'https://example.test/logo.png',
            'width' => 128,
            'height' => 128,
            'mime' => 'image/png',
            'size_bytes' => 456,
        ]);

        MediaLibraryAsset::create([
            'owner_user_id' => $otherUser->id,
            'source' => 'user',
            'slot' => 'logo',
            'path' => 'other/logo.png',
            'url' => 'https://example.test/other-logo.png',
            'width' => 128,
            'height' => 128,
            'mime' => 'image/png',
            'size_bytes' => 789,
        ]);

        $response = $this->actingAs($user)->getJson(route('passes.media.assets.index'));

        $response->assertOk();
        $ids = collect($response->json('data'))->pluck('id');

        $this->assertTrue($ids->contains($systemAsset->id));
        $this->assertTrue($ids->contains($userAsset->id));
    }

    public function test_user_cannot_delete_other_users_asset(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $asset = MediaLibraryAsset::create([
            'owner_user_id' => $otherUser->id,
            'source' => 'user',
            'slot' => 'logo',
            'path' => 'other/logo.png',
            'url' => 'https://example.test/other-logo.png',
            'width' => 128,
            'height' => 128,
            'mime' => 'image/png',
            'size_bytes' => 789,
        ]);

        $response = $this->actingAs($user)->deleteJson(route('passes.media.assets.destroy', [
            'asset' => $asset->id,
        ]));

        $response->assertForbidden();
    }
}
