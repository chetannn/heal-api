<?php

namespace App\Http\Controllers\Doctor;

use App\Models\Doctor;

class StoreController
{
    public function __invoke()
    {
        request()->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => ['required', 'email']
        ]);

        return Doctor::create(request()->all());
    }
}
