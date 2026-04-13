<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;

class DoctorController extends Controller {
    // Listar doctores
    public function index() {
        return Doctor::with('user')->get();
    }

    // Crear doctor (ADMIN)
    public function store(Request $request) {
        $validated = $request->validate([
            'user_id'     => ['required', 'exists:users,id'],
            'specialty'   => ['required', 'string'],
            'phone'       => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $doctor = Doctor::create($validated);

        return response()->json($doctor, 201);
    }

    // Ver doctor
    public function show($id) {
        return Doctor::with('user')->findOrFail($id);
    }

    // Actualizar doctor
    public function update(Request $request, $id) {
        $doctor = Doctor::findOrFail($id);

        $doctor->update($request->only([
            'specialty',
            'phone',
            'description'
        ]));

        return response()->json($doctor);
    }

    // Eliminar doctor
    public function destroy($id) {
        $doctor = Doctor::findOrFail($id);
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor eliminado'
        ]);
    }
}
