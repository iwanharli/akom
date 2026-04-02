<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use App\Models\Client;
use Illuminate\Support\Facades\Redirect;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'ilike', '%' . $search . '%')
                  ->orWhere('institution', 'ilike', '%' . $search . '%')
                  ->orWhere('email', 'ilike', '%' . $search . '%');
            });
        }

        $clients = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
            'filters' => $request->only('search')
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:t_clients,email',
            'status' => 'required|in:Active,Inactive',
        ]);

        Client::create($request->all());

        return Redirect::back()->with('success', 'Client added successfully.');
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'institution' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:t_clients,email,' . $client->id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $client->update($request->all());

        return Redirect::back()->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return Redirect::back()->with('success', 'Client deleted successfully.');
    }

    public function toggleStatus(Client $client)
    {
        $client->status = $client->status === 'Active' ? 'Inactive' : 'Active';
        $client->save();

        return Redirect::back()->with('success', 'Client status updated.');
    }
}

