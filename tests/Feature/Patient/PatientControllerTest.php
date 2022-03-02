<?php

namespace Tests\Feature\Patient;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PatientControllerTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function test_patient_can_register()
    {
        $this->postJson(route('patients.store'), [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->email,
            'password' => 'password'
        ])
            ->assertOk();

        $this->assertDatabaseCount('patients', 1);
    }

    public function test_patient_can_login_with_email_and_password()
    {
        $patient = Patient::factory()->create();

        $this->postJson(route('patients.login'), [
            'email' => $patient->email,
            'password' => 'password'
        ])
            ->assertOk();

    }

    public function test_a_patient_can_create_appointment_request_for_an_appointment_with_a_doctor()
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();

        $this->actingAs($patient)
            ->postJson(route('appointment_requests.store'), [
                'doctor_id' => $doctor->id,
                'appointment_at' => now()->format('Y-m-d H:i:s'),
                'status' => 0
            ])
            ->assertOk();

        $this->assertDatabaseCount('appointment_requests', 1);
    }

    public function test_a_patient_can_view_all_of_appointment_request_made_by_him()
    {
        $patient = Patient::factory()->create();

        $doctorA = Doctor::factory()->create();
        $doctorB = Doctor::factory()->create();

        AppointmentRequest::factory()
            ->for($patient)
            ->for($doctorA)
            ->create();

        AppointmentRequest::factory()
            ->for($patient)
            ->for($doctorB)
            ->create();

        $this->actingAs($patient)
            ->getJson(route('appointment_requests.index'))
            ->assertOk()
            ->assertJsonCount(2);
    }

    public function test_a_patient_can_accept_a_reschedule_appointment_made_by_a_doctor()
    {
        $patient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();

        $appointmentRequest = AppointmentRequest::factory()
            ->for($patient)
            ->for($doctor)
            ->create();

        $appointmentRequest->update([
            'accepted_at' => now()
        ]);

        $appointment = Appointment::factory()
            ->for($doctor)
            ->for($patient)
            ->create();

        $response = $this->actingAs($patient)
            ->postJson(route('appointments.accept_rescheduled_appointment', [
                'appointment' => $appointment
            ]))->assertOk();

        $this->assertDatabaseHas('appointments', [
            'appointment_rescheduled_accepted_at' => $response->json()['appointment_rescheduled_accepted_at']
        ]);

    }

    public function test_a_patient_cannot_accept_a_reschedule_appointment_made_by_a_doctor_that_does_not_belong_to_him()
    {
        $patient = Patient::factory()->create();
        $unAuthorizedPatient = Patient::factory()->create();
        $doctor = Doctor::factory()->create();

        $appointmentRequest = AppointmentRequest::factory()
            ->for($patient)
            ->for($doctor)
            ->create();

        $appointmentRequest->update([
            'accepted_at' => now()
        ]);

        $appointment = Appointment::factory()
            ->for($doctor)
            ->for($patient)
            ->create();

        $this->actingAs($unAuthorizedPatient)
            ->postJson(route('appointments.accept_rescheduled_appointment', [
                'appointment' => $appointment
            ]))->assertForbidden();
    }

}
