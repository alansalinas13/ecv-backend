<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;

class CityController extends Controller {
    public function index() {
        return response()->json(
            City::orderBy('name')->get()
        );
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities,name'],
        ]);

        $city = City::create($validated);

        return response()->json([
            'message' => 'Ciudad creada correctamente',
            'city'    => $city,
        ], 201);
    }

    public function update(Request $request, $id) {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'message' => 'Ciudad no encontrada'
            ], 404);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:cities,name,'.$city->id],
        ]);

        $city->update($validated);

        return response()->json([
            'message' => 'Ciudad actualizada correctamente',
            'city'    => $city,
        ]);
    }

    public function destroy($id) {
        $city = City::find($id);

        if (!$city) {
            return response()->json([
                'message' => 'Ciudad no encontrada'
            ], 404);
        }

        $city->delete();

        return response()->json([
            'message' => 'Ciudad eliminada correctamente'
        ]);
    }
}
