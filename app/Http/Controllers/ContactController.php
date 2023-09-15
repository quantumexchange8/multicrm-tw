<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Contact');
    }

    public function store(): string
    {
        request()->validate([
            'name' => ['required', 'min:2'],
            'email' => ['required', 'email'],
            'phone_number' => ['required'],
            'message' => ['required'],
        ]);

        return "Contact message was sent";
    }
}
