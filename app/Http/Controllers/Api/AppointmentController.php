<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Notifications\AppointmentCreatedNotification;

class AppointmentController extends Controller {
    // Listar citas según rol
    public function index(Request $request) {
        $user = $request->user();

        // Usuario normal
        if ($user->role == 3) {
            return response()->json(
                Appointment::with(['doctor.user', 'doctor.city', 'doctor.hospital'])
                           ->where('user_id', $user->id)
                           ->get()
            );
        }

        // Doctor
        if ($user->role == 2) {
            $doctor = $user->doctor;

            return response()->json(
                Appointment::with(['user', 'doctor.city', 'doctor.hospital'])
                           ->where('doctor_id', $doctor->id)
                           ->get()
            );
        }

        // Admin
        return response()->json(
            Appointment::with(['user', 'doctor.user', 'doctor.city', 'doctor.hospital'])
                       ->get()
        );
    }

    // Crear cita (USER)
    public function store(Request $request) {
        $user = $request->user();

        if ($user->role != 3) {
            return response()->json([
                'message' => 'Solo usuarios pueden agendar citas'
            ], 403);
        }

        $validated = $request->validate([
            'doctor_id'        => ['required', 'exists:doctors,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
        ]);

        $doctor = Doctor::find($validated['doctor_id']);

        if (!$doctor) {
            return response()->json([
                'message' => 'Doctor no encontrado'
            ], 404);
        }

        $appointmentTime = date('H:i', strtotime($validated['appointment_date']));

        if ($doctor->start_time && $doctor->end_time) {
            if ($appointmentTime < $doctor->start_time || $appointmentTime > $doctor->end_time) {
                return response()->json([
                    'message' => 'La cita está fuera del horario de atención del doctor'
                ], 400);
            }
        }

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
            'user_id'          => $user->id,
            'doctor_id'        => $validated['doctor_id'],
            'appointment_date' => $validated['appointment_date'],
        ]);

        $appointment->load(['user', 'doctor.user', 'doctor.city', 'doctor.hospital']);

        if ($appointment->doctor?->user) {
            $appointment->doctor->user->notify(new AppointmentCreatedNotification($appointment));
        }

        return response()->json($appointment, 201);
    }

    // Ver cita
    public function show(Request $request, $id) {
        $appointment = Appointment::with(['user', 'doctor.user', 'doctor.city', 'doctor.hospital'])->find($id);

        if (!$appointment) {
            return response()->json([
                'message' => 'Cita no encontrada'
            ], 404);
        }

        return response()->json($appointment);
    }

    // Doctor cambia estado
    public function updateStatus(Request $request, $id) {
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
