<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(public Appointment $appointment)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment->load(['user', 'doctor.user', 'doctor.city', 'doctor.hospital']);

        return (new MailMessage)
            ->subject('Nueva cita registrada')
            ->greeting('Hola ' . ($appointment->doctor->user->name ?? 'Doctor'))
            ->line('Se ha registrado una nueva cita con uno de tus pacientes.')
            ->line('Fecha y hora: ' . $appointment->appointment_date ?? '')
            ->line('Paciente: ' . ($appointment->user->name ?? 'No disponible'))
            ->line('Correo del paciente: ' . ($appointment->user->email ?? 'No disponible'))
            ->line('Teléfono del paciente: ' . ($appointment->user->phone ?? 'No disponible'))
            ->line('Hospital: ' . ($appointment->doctor->hospital->name ?? 'No definido'))
            ->line('Ciudad: ' . ($appointment->doctor->city->name ?? 'No definida'))
            ->line('Por favor, revisa el sistema para gestionar esta cita.');
    }
}
