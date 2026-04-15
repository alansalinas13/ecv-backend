<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hospital;
use Illuminate\Http\Request;

class HospitalController extends Controller {
    // Listar hospitales
    public function index() {
        $hospitals = Hospital::with('city')
                             ->orderBy('name')
                             ->get();

        return response()->json($hospitals);
    }

    // Crear hospital
    public function store(Request $request) {
        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'name'    => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'lat'     => ['required', 'numeric', 'between:-90,90'],
            'lng'     => ['required', 'numeric', 'between:-180,180'],
        ]);

        $hospital = Hospital::create($validated);

        return response()->json([
            'message'  => 'Hospital creado correctamente',
            'hospital' => $hospital->load('city'),
        ], 201);
    }

    // Ver hospital
    public function show($id) {
        $hospital = Hospital::with('city')->find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital no encontrado'
            ], 404);
        }

        return response()->json($hospital);
    }

    // Actualizar hospital
    public function update(Request $request, $id) {
        $hospital = Hospital::find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital no encontrado'
            ], 404);
        }

        $validated = $request->validate([
            'city_id' => ['required', 'exists:cities,id'],
            'name'    => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'lat'     => ['required', 'numeric', 'between:-90,90'],
            'lng'     => ['required', 'numeric', 'between:-180,180'],
        ]);

        $hospital->update($validated);

        return response()->json([
            'message'  => 'Hospital actualizado correctamente',
            'hospital' => $hospital->fresh()->load('city'),
        ]);
    }

    // Eliminar hospital
    public function destroy($id) {
        $hospital = Hospital::find($id);

        if (!$hospital) {
            return response()->json([
                'message' => 'Hospital no encontrado'
            ], 404);
        }

        $hospital->delete();

        return response()->json([
            'message' => 'Hospital eliminado correctamente'
        ]);
    }
}
