<?php

namespace App\Http\Controllers;

use App\Models\ROM;
use Illuminate\Http\Request;

class RomController extends Controller
{
    public function index()
    {
        return response()->json(Rom::all(), 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'feature' => 'required|string',
            'date_build' => 'required|date',
            'new_date_updated' => 'nullable|date',
        ]);

        $rom = Rom::create($validated);

        return response()->json([
            'message' => 'ROM created successfully!',
            'data' => $rom
        ], 201);
    }

    public function show($id)
    {
        $rom = Rom::find($id);

        if (!$rom) {
            return response()->json(['message' => 'ROM not found'], 404);
        }

        return response()->json($rom, 200);
    }

    public function update(Request $request, $id)
    {
        $rom = Rom::find($id);

        if (!$rom) {
            return response()->json(['message' => 'ROM not found'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'feature' => 'sometimes|string',
            'date_build' => 'sometimes|date',
            'new_date_updated' => 'nullable|date',
        ]);

        $rom->update($validated);

        return response()->json([
            'message' => 'ROM updated successfully!',
            'data' => $rom
        ], 200);
    }

    public function destroy($id)
    {
        $rom = Rom::find($id);

        if (!$rom) {
            return response()->json(['message' => 'ROM not found'], 404);
        }

        $rom->delete();

        return response()->json(['message' => 'ROM deleted successfully!'], 200);
    }
}
