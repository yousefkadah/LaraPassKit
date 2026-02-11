<?php

namespace App\Http\Controllers;

use App\Http\Requests\MediaLibraryAssetRequest;
use App\Models\MediaLibraryAsset;
use App\Services\MediaLibraryService;
use Illuminate\Http\Request;

class MediaLibraryAssetController extends Controller
{
    /**
     * Display a listing of the media library assets.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'source' => ['nullable', 'string', 'in:system,user,all'],
            'slot' => ['nullable', 'string', 'in:icon,logo,strip,thumbnail,background,footer'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $userId = (int) $request->user()->id;
        $source = $validated['source'] ?? 'all';
        $perPage = $validated['per_page'] ?? 25;

        $query = MediaLibraryAsset::query();

        if ($source === 'system') {
            $query->where('source', 'system');
        } elseif ($source === 'user') {
            $query->where('source', 'user')->where('owner_user_id', $userId);
        } else {
            $query->where(function ($builder) use ($userId): void {
                $builder
                    ->where('source', 'system')
                    ->orWhere(function ($nested) use ($userId): void {
                        $nested->where('source', 'user')->where('owner_user_id', $userId);
                    });
            });
        }

        if (! empty($validated['slot'])) {
            $query->where('slot', $validated['slot']);
        }

        $assets = $query->latest()->paginate($perPage)->withQueryString();

        return response()->json($assets);
    }

    /**
     * Store a newly created media asset.
     */
    public function store(MediaLibraryAssetRequest $request, MediaLibraryService $mediaLibraryService)
    {
        $this->authorize('create', MediaLibraryAsset::class);
        set_time_limit(900);

        $validated = $request->validated();
        $userId = (int) $request->user()->id;

        $asset = $mediaLibraryService->store(
            $request->file('image'),
            $validated['slot'] ?? null,
            $userId,
        );

        return response()->json($asset, 201);
    }

    /**
     * Remove the specified media asset.
     */
    public function destroy(Request $request, MediaLibraryAsset $asset, MediaLibraryService $mediaLibraryService)
    {
        $this->authorize('delete', $asset);

        $mediaLibraryService->delete($asset);

        return response()->noContent();
    }
}
