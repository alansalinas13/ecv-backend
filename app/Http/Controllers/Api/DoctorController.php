<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Doctor;
use App\Models\User;

class DoctorController extends Controller {
    // Listar doctores
    public function index() {
        return response()->json(
            Doctor::with(['user', 'city', 'hospital'])->orderBy('id', 'desc')->get()
        );
    }

    // Usuarios disponibles para crear perfil de doctor (solo admin)
    public function availableDoctorUsers() {
        $users = User::where('role', 2)
                     ->whereDoesntHave('doctor')
                     ->get(['id', 'name', 'email']);

        return response()->json($users);
    }

    // Crear doctor (solo admin)
    public function store(Request $request) {
        $validated = $request->validate([
            'user_id'     => ['required', 'exists:users,id'],
            'city_id'     => ['required', 'exists:cities,id'],
            'hospital_id' => ['required', 'exists:hospitals,id'],
            'specialty'   => ['required', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $user = User::find($validated['user_id']);

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        if ($user->role != 2) {
            return response()->json([
                'message' => 'El usuario seleccionado no tiene rol doctor'
            ], 400);
        }

        if ($user->doctor) {
            return response()->json([
                'message' => 'Ese usuario ya tiene un perfil de doctor'
            ], 400);
        }

        $doctor = Doctor::create($validated);

        return response()->json([
            'message' => 'Doctor creado correctamente',
            'doctor'  => $doctor->load(['user', 'city', 'hospital']),
        ], 201);
    }

    // Ver doctor
    public function show($id) {
        $doctor = Doctor::with(['user', 'city', 'hospital'])->find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        return response()->json($doctor);
    }

    // Actualizar doctor
    public function update(Request $request, $id) {
        $doctor = Doctor::with(['user', 'city', 'hospital'])->find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        $user = $request->user();

        $isAdmin = $user->role == 1;
        $isOwnerDoctor = $user->role == 2 && $doctor->user_id === $user->id;

        if (!$isAdmin && !$isOwnerDoctor) {
            return response()->json([
                'message' => 'No autorizado para editar este perfil'
            ], 403);
        }

        $validated = $request->validate([
            'city_id'     => ['required', 'exists:cities,id'],
            'hospital_id' => ['required', 'exists:hospitals,id'],
            'specialty'   => ['required', 'string', 'max:255'],
            'phone'       => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string'],
            'start_time'  => ['required', 'date_format:H:i'],
            'end_time'    => ['required', 'date_format:H:i', 'after:start_time'],
        ]);

        $doctor->update($validated);

        return response()->json([
            'message' => 'Doctor actualizado correctamente',
            'doctor'  => $doctor->fresh()->load(['user', 'city', 'hospital']),
        ]);
    }

    // Eliminar doctor (solo admin)
    public function destroy($id) {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        $doctor->delete();

        return response()->json([
            'message' => 'Doctor eliminado correctamente'
        ]);
    }
}
