<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\User;
use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Calculate statistics based on role
        if ($user->isSuperAdmin()) {
            $totalReports = Report::count();
            $publishedReports = Report::where('status', 'Published')->count();
            $totalUsers = User::count();
        } else {
            $totalReports = Report::where('user_id', $user->id)->count();
            $publishedReports = Report::where('user_id', $user->id)->where('status', 'Published')->count();
            $totalUsers = 0; // Admins don't see users
        }

        // Clients stats (Visible to both superadmin and admin)
        $totalClients = Client::count();

        return Inertia::render('Dashboard/Index', [
            'stats' => [
                'total_reports' => $totalReports,
                'published_reports' => $publishedReports,
                'draft_reports' => $totalReports - $publishedReports,
                'total_users' => $totalUsers,
                'total_clients' => $totalClients
            ],
            'canManageUsers' => $user->isSuperAdmin()
        ]);
    }
}
