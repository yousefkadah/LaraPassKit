<?php

namespace App\Http\Controllers;

use App\Http\Requests\PassTypeSampleRequest;
use App\Models\MediaLibraryAsset;
use App\Models\PassTypeSample;
use App\Services\PassTypeSampleService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class PassTypeSampleController extends Controller
{
    /**
     * Display a listing of pass type samples.
     */
    public function index(Request $request, PassTypeSampleService $service)
    {
        $validated = $request->validate([
            'pass_type' => ['nullable', 'string'],
            'platform' => ['nullable', 'string', 'in:apple,google'],
            'source' => ['nullable', 'string', 'in:system,user,all'],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $perPage = $validated['per_page'] ?? 25;
        $samples = $service->listForUser((int) $request->user()->id, $validated, $perPage);

        $imageIds = $samples->getCollection()->flatMap(function (PassTypeSample $sample) {
            return collect(Arr::wrap($sample->images))->filter(fn ($value) => is_string($value));
        })->unique()->values();

        $assets = $imageIds->isEmpty()
            ? collect()
            : MediaLibraryAsset::query()->whereIn('id', $imageIds)->get()->keyBy('id');

        $samples->setCollection(
            $samples->getCollection()->map(function (PassTypeSample $sample) use ($assets) {
                $images = Arr::wrap($sample->images);
                $resolved = collect($images)->map(function ($value) use ($assets) {
                    if (is_string($value)) {
                        return $assets->get($value) ?? $value;
                    }

                    return $value;
                });

                $sample->images = $resolved->toArray();

                return $sample;
            })
        );

        return response()->json($samples);
    }

    /**
     * Store a newly created user sample.
     */
    public function store(PassTypeSampleRequest $request, PassTypeSampleService $service)
    {
        $this->authorize('create', PassTypeSample::class);

        $sample = $service->createForUser(
            (int) $request->user()->id,
            $request->validated(),
        );

        return response()->json($sample, 201);
    }

    /**
     * Update the specified user sample.
     */
    public function update(Request $request, PassTypeSample $sample)
    {
        $this->authorize('update', $sample);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $sample->update($validated);

        return response()->json($sample);
    }

    /**
     * Remove the specified user sample.
     */
    public function destroy(Request $request, PassTypeSample $sample)
    {
        $this->authorize('delete', $sample);

        $sample->delete();

        return response()->noContent();
    }
}
