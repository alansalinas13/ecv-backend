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

        $user = User::findOrFail($validated['user_id']);

        // valido que sea doctor (1 => admin, 2 => doctor, 3 => usr por defecto)
        if ($user->role != 2) {
            return response()->json([
                'message' => 'El usuario no tiene rol doctor'
            ], 400);
        }

        //validar que no tenga perfil ya
        if ($user->doctor) {
            return response()->json([
                'message' => 'El usuario ya tiene perfil de doctor'
            ], 400);
        }

        $doctor = Doctor::create($validated);

        return response()->json($doctor, 201);
    }

    // Ver doctor
    public function show($id) {
        $doctor = Doctor::find($id);

        if (!$doctor) {///validamos que exista el doctor enviado para ser actualizado
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        return Doctor::with('user')->findOrFail($id);
    }

    // Actualizar doctor
    public function update(Request $request, $id) {
        $doctor = Doctor::find($id);

        if (!$doctor) {///validamos que exista el doctor enviado para ser actualizado
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        $user = $request->user();

        // verifico que el doctor sea el que esta autenticado
        if ($doctor->user_id !== $user->id) {
            return response()->json([
                'message' => 'No autorizado para modificar este perfil'
            ], 403);
        }

        $validated = $request->validate([
            'specialty'   => ['required', 'string'],
            'phone'       => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ]);

        $doctor->update($validated);

        return response()->json($doctor);
    }

    // Eliminar doctor
    public function destroy($id) {
        $doctor = Doctor::find($id);

        if (!$doctor) {///validamos que exista el doctor enviado para ser actualizado
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor eliminado'
        ]);
    }
}
