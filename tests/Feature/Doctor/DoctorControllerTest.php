<?php

namespace Tests\Feature\Doctor;

use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\CheckupNote;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

    public function test_a_doctor_can_add_a_checkup_note_for_an_appointment_that_is_completed()
    {
        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

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

        $this->actingAs($doctor)->postJson(route('checkup_notes.store', [
            'appointment' => $appointment
        ]), [
            'notes' => $this->faker->paragraph(1),
            'prescription' => $this->faker->paragraph(1),
//            'patient_id'
        ])
            ->assertOk();

    }

    public function test_a_doctor_can_add_files_for_a_checkup_note_during_or_after_the_appointment_with_the_patient()
    {
        Storage::fake('local');

        $doctor = Doctor::factory()->create();
        $patient = Patient::factory()->create();

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

        $checkupNote = CheckupNote::factory()
            ->for($appointment)
            ->create();

        $files = [
            UploadedFile::fake()->create('an_important_doc_file.doc'),
            UploadedFile::fake()->create('an_important_pdf_file.pdf'),
            UploadedFile::fake()->create('an_important_png_file.jpg'),
            UploadedFile::fake()->create('an_important_jpg_file.png'),
        ];

        $response = $this->actingAs($doctor)
            ->postJson(route('checkup_notes.store_files', [
                'checkupNote' => $checkupNote
            ]), [
                'files' => $files
            ]);

        $response->assertOk();

        foreach ($files as $file) {
            Storage::disk('local')->assertExists("checkup_notes/" . $file->hashName());
        }

    }
}
