<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Client;
use Illuminate\Support\Facades\Redirect;

class ClientRegistrationController extends Controller
{
    /**
     * Show the dedicated client registration form.
     */
    public function create()
    {
        return Inertia::render('Auth/RegisterClient');
    }

    /**
     * Store the client registration application.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:t_clients,email',
        ], [
            'email.unique' => 'This email address has already submitted an application.'
        ]);

        Client::create([
            'name' => $request->name,
            'institution' => $request->institution,
            'email' => $request->email,
            'status' => 'Inactive', // Required admin approval for safety since it's a public form
        ]);

        return Redirect::route('client.register')->with('success', 'Application submitted successfully! Our administrative team will review your details.');
    }
}

