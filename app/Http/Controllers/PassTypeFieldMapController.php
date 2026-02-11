<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PassTypeFieldMapController extends Controller
{
    /**
     * Display the pass-type field map.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'pass_type' => ['required', 'string'],
            'platform' => ['required', 'string', 'in:apple,google'],
        ]);

        $map = (array) config('pass-type-fields');
        $platformMap = $map[$validated['platform']] ?? null;
        $entry = $platformMap[$validated['pass_type']] ?? null;

        if (! $entry) {
            return response()->json(['message' => 'Unsupported pass type.'], 404);
        }

        return response()->json([
            'pass_type' => $validated['pass_type'],
            'platform' => $validated['platform'],
            'field_groups' => $entry['field_groups'] ?? [],
            'constraints' => $entry['constraints'] ?? [],
        ]);
    }
}
