<?php

namespace Tests\Feature\Doctor;

use App\Models\AppointmentRequest;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DoctorControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_a_doctor_can_view_all_the_appointment_requests_made_by_patients()
    {
        $authorizedDoctor = Doctor::factory()->create();
        $doctor2 = Doctor::factory()->create();

        $patientA = Patient::factory()->create();
        $patientB = Patient::factory()->create();
        $patientC = Patient::factory()->create();

        AppointmentRequest::factory()
            ->for($patientA)
            ->for($authorizedDoctor)
            ->create();

        AppointmentRequest::factory()
            ->for($patientB)
            ->for($authorizedDoctor)
            ->create();

        AppointmentRequest::factory()
            ->for($patientC)
            ->for($authorizedDoctor)
            ->create();

        AppointmentRequest::factory()
            ->for($patientA)
            ->for($doctor2)
            ->create();

        $this->actingAs($authorizedDoctor)->getJson(route('patients_appointment_requests.index'))
            ->assertOk()
            ->assertJsonCount(3);

    }

    public function test_a_doctor_can_accept_appointment_request_made_by_a_patient()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

        $appointmentRequest = AppointmentRequest::factory()
            ->for($doctor)
            ->for($patient)
            ->create();

//       $appointmentRequest->update([
//           'accepted_at' => now()->format('Y-m-d H:i:s')
//       ]);
//
//       $appointment = Appointment::factory()
//            ->for($doctor)
//           ->for($patient)
//           ->create();

        $this->actingAs($doctor)->postJson(route('appointments.store', [
            'appointmentRequest' => $appointmentRequest
        ]), [
            'patient_id' => $patient->id
        ])
            ->assertOk();

        $appointmentRequest->refresh();

        $this->assertDatabaseHas('appointment_requests',
            [
                "id" => $appointmentRequest->id,
                "accepted_at" => $appointmentRequest->accepted_at
            ]);

    }
}
