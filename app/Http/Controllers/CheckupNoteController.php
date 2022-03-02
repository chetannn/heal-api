<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\CheckupNote;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class CheckupNoteController extends Controller
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
     *
     * @param Request $request
     * @return Response
     */
    public function store(Appointment $appointment, Request $request): Response
    {
        abort_if($appointment->doctor_id !== auth()->id(), 403);

        $validated = $request->validate([
            'prescription' => 'required'
        ]);

        $note = auth()->user()->checkupNotes()->create([
            'patient_id' => $appointment->patient_id,
            'appointment_id' => $appointment->id,
            'prescription' => $validated['prescription'],
            'notes' => $request->get('notes')
        ]);

        return response($note);
    }

    public function storeFiles(CheckupNote $checkupNote, Request $request): Response
    {
        $request->validate([
            'files' => ['required'],
            'files.*' => ['file', 'mimes:jpg,png,pdf,docx,doc', 'max:512000']
        ]);

        $files = $request->file('files');

        foreach ($files as $file) {

            $path = Storage::disk('local')->putFileAs("checkup_notes", $file, $file->hashName());

            $checkupNote->files()->create([
                'path' => $path,
                "extension" => $file->getClientOriginalExtension(),
                "size" => $file->getSize()
            ]);
        }

        return response([$checkupNote->refresh()->files()->get()]);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\CheckupNote $checkupNote
     * @return Response
     */
    public function show(CheckupNote $checkupNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param \App\Models\CheckupNote $checkupNote
     * @return Response
     */
    public function update(Request $request, CheckupNote $checkupNote)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\CheckupNote $checkupNote
     * @return Response
     */
    public function destroy(CheckupNote $checkupNote)
    {
        //
    }
}
