<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;

class AppointmentController extends Controller
{
    // Listar citas según rol
    public function index(Request $request)
    {
        $user = $request->user();

        // Usuario normal
        if ($user->role == 3) {
            return Appointment::with('doctor.user')
                              ->where('user_id', $user->id)
                              ->get();
        }

        // Doctor
        if ($user->role == 2) {
            $doctor = $user->doctor;

            return Appointment::with('user')
                              ->where('doctor_id', $doctor->id)
                              ->get();
        }

        // Admin
        return Appointment::with(['user', 'doctor.user'])->get();
    }

    // Crear cita (USER)
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role != 3) {
            return response()->json([
                'message' => 'Solo usuarios pueden agendar citas'
            ], 403);
        }

        $validated = $request->validate([
            'doctor_id' => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
        ]);

        // Validar que no haya duplicado (mismo doctor y hora)
        $exists = Appointment::where('doctor_id', $validated['doctor_id'])
                             ->where('appointment_date', $validated['appointment_date'])
                             ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'El doctor ya tiene una cita en ese horario'
            ], 400);
        }

        $appointment = Appointment::create([
            'user_id' => $user->id,
            'doctor_id' => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
        ]);

        return response()->json($appointment, 201);
    }

    // Ver cita
    public function show(Request $request, $id)
    {
        $appointment = Appointment::with(['user', 'doctor.user'])->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        return $appointment;
    }

    // Doctor cambia estado
    public function updateStatus(Request $request, $id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        $user = $request->user();

        // Solo doctor asignado
        if ($user->role != 2 || $appointment->doctor->user_id != $user->id) {
            return response()->json([
                'message' => 'No autorizado'
            ], 403);
        }
        // El doctor asignado puede marcar la cita como pendiente, confirmada, cancelada o completada.
        $validated = $request->validate([
            'status' => ['required', 'in:pending,confirmed,cancelled,completed']
        ]);

        $appointment->update([
            'status' => $validated['status']
        ]);

        return response()->json($appointment);
    }

}
