<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param AppointmentRequest $appointmentRequest
     * @return Response
     */
    public function store(AppointmentRequest $appointmentRequest): Response
    {
        abort_if($appointmentRequest->doctor_id !== auth()->id(), 403);

        $validated = request()->validate([
            'patient_id' => 'required'
        ]);

        $appointmentRequest->update([
            'accepted_at' => now()
        ]);

        $appointment = auth()->user()->appointments()->create([
            'patient_id' => $validated['patient_id']
        ]);

        return response($appointment);
    }

    /**
     * Display the specified resource.
     *
     * @param Appointment $appointment
     * @return Response
     */
    public function show(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Appointment $appointment
     * @return Response
     */
    public function update(Request $request, Appointment $appointment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Appointment $appointment
     * @return Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
}
