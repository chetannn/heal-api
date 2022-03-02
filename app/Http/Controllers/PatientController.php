<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PatientController extends Controller
{
    public function store(Request $request): Response
    {
        $validated = $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'password' => 'required',
            'email' => ['required', 'email']
        ]);

        return response(Patient::create($validated));
    }

    public function login(): Response
    {
        request()->validate([
            'email' => ['required', 'email'],
            'password' => 'required'
        ]);

        $user = Patient::where('email', request('email'))->first();

        if (!$user || !Hash::check(request('password'), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response([
            'token' => $user->createToken(str()->random(40))->plainTextToken,
            'user' => $user
        ]);
    }

    public function storeAppointmentRequest(): Response
    {
        $validated = request()->validate([
            'doctor_id' => 'required',
            'appointment_at' => ['required', 'date']
        ]);

        $appointmentRequest = auth()->user()->appointmentRequests()->create([
            'doctor_id' => $validated['doctor_id'],
            'appointment_at' => $validated['appointment_at']
        ]);

        return response($appointmentRequest);
    }

    public function getAppointmentRequests(): Response
    {
        return response(auth()->user()->appointmentRequests()->get());
    }


    public function acceptRescheduledAppointment(Appointment $appointment): Response
    {
        abort_if($appointment->patient_id !== auth()->id(), 403);

        $appointment->update([
            'appointment_rescheduled_accepted_at' => now()
        ]);

        return response($appointment->fresh());
    }

}
