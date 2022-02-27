<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class DoctorController extends Controller
{
    public function getAppointmentRequests() : Response
    {
        return \response(auth()->user()->appointmentRequests()->get());
    }
}
