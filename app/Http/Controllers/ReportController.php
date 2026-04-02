<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\ReportStat;
use App\Models\ReportSection;
use App\Models\AnalysisItem;
use App\Models\ChartData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use App\Models\Client;
use Illuminate\Support\Facades\Redirect;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // Superadmin sees all reports, admin sees only their own
        $query = Report::with(['user:id,name', 'clients:id,name,institution']);
        if (!$user->isSuperAdmin()) {
            $query->where('user_id', $user->id);
        }

        return Inertia::render('Reports/Index', [
            'reports' => $query->latest()->get(),
            'canManageAll' => $user->isSuperAdmin(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Create', [
            'clients' => Client::where('status', 'Active')->get(['id', 'name', 'institution'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'alert_text' => 'nullable|string',
            'report_date' => 'required|date',
            'classification' => 'required|string',
            'usd_idr_rate' => 'nullable|numeric',
            'brent_oil_price' => 'nullable|numeric',
            'asumsi_icp' => 'nullable|numeric',
            'asumsi_kurs' => 'nullable|numeric',
            'status' => 'required|in:Draft,Published,Archived',
            'conclusion_html' => 'nullable|string',
            'stats' => 'array',
            'sections' => 'array',
            'client_ids' => 'nullable|array',
            'client_ids.*' => 'exists:t_clients,id',
        ]);

        // Auto-assign current user
        $validated['user_id'] = $request->user()->id;

        DB::transaction(function () use ($validated, $request) {
            $report = Report::create($validated);

            // Handle Client Linking
            if ($request->has('client_ids')) {
                $report->clients()->sync($request->input('client_ids'));
            }

            // Handle Stats
            if ($request->has('stats')) {
                foreach ($request->input('stats') as $stat) {
                    $report->stats()->create($stat);
                }
            }

            // Handle Sections
            if ($request->has('sections')) {
                foreach ($request->input('sections') as $sec) {
                    $section = $report->sections()->create([
                        'section_num' => $sec['section_num'],
                        'title' => $sec['title'],
                        'badge_text' => $sec['badge_text'],
                        'content_html' => $sec['content_html'],
                    ]);

                    // Handle nested Analysis Items
                    if (isset($sec['analysis_items'])) {
                        foreach ($sec['analysis_items'] as $item) {
                            $section->analysis_items()->create($item);
                        }
                    }

                    // Handle nested Charts
                    if (isset($sec['charts'])) {
                        foreach ($sec['charts'] as $chart) {
                            $section->charts()->create($chart);
                        }
                    }
                }
            }
        });

        return Redirect::route('dashboard')->with('success', 'Report created successfully.');
    }

    public function show($uuid)
    {
        $report = Report::where('uuid', $uuid)
            ->with(['user:id,name', 'stats', 'sections.analysis_items', 'sections.charts'])
            ->firstOrFail();

        return Inertia::render('Reports/View', [
            'report' => $report
        ]);
    }

    public function edit(Request $request, Report $reports_admin)
    {
        // Authorization: only owner or superadmin
        if (!$request->user()->canManageReport($reports_admin)) {
            abort(403, 'You are not authorized to edit this report.');
        }

        return Inertia::render('Reports/Edit', [
            'report' => $reports_admin->load(['stats', 'sections.analysis_items', 'sections.charts', 'clients:id']),
            'clients' => Client::where('status', 'Active')->get(['id', 'name', 'institution'])
        ]);
    }

    public function update(Request $request, Report $reports_admin)
    {
        // Authorization: only owner or superadmin
        if (!$request->user()->canManageReport($reports_admin)) {
            abort(403, 'You are not authorized to update this report.');
        }

        $validated = $request->validate([
            'title' => 'required|string',
            'subtitle' => 'nullable|string',
            'alert_text' => 'nullable|string',
            'report_date' => 'required|date',
            'classification' => 'required|string',
            'usd_idr_rate' => 'nullable|numeric',
            'brent_oil_price' => 'nullable|numeric',
            'asumsi_icp' => 'nullable|numeric',
            'asumsi_kurs' => 'nullable|numeric',
            'status' => 'required|in:Draft,Published,Archived',
            'conclusion_html' => 'nullable|string',
            'stats' => 'array',
            'sections' => 'array',
            'client_ids' => 'nullable|array',
            'client_ids.*' => 'exists:t_clients,id',
        ]);

        DB::transaction(function () use ($validated, $request, $reports_admin) {
            $reports_admin->update($validated);

            // Handle Client Linking
            if ($request->has('client_ids')) {
                $reports_admin->clients()->sync($request->input('client_ids'));
            }

            // Sync Stats (Delete and Recreate for simplicity)
            $reports_admin->stats()->delete();
            if ($request->has('stats')) {
                foreach ($request->input('stats') as $stat) {
                    $reports_admin->stats()->create($stat);
                }
            }

            // Sync Sections
            // Cascade delete handles analysis_items and charts via FK constraints
            $reports_admin->sections()->each(function ($section) {
                $section->analysis_items()->delete();
                $section->charts()->delete();
                $section->delete();
            });

            if ($request->has('sections')) {
                foreach ($request->input('sections') as $sec) {
                    $section = $reports_admin->sections()->create([
                        'section_num' => $sec['section_num'],
                        'title' => $sec['title'],
                        'badge_text' => $sec['badge_text'],
                        'content_html' => $sec['content_html'],
                    ]);

                    if (isset($sec['analysis_items'])) {
                        foreach ($sec['analysis_items'] as $item) {
                            $section->analysis_items()->create($item);
                        }
                    }

                    if (isset($sec['charts'])) {
                        foreach ($sec['charts'] as $chart) {
                            $section->charts()->create($chart);
                        }
                    }
                }
            }
        });

        return Redirect::route('dashboard')->with('success', 'Report updated successfully.');
    }

    public function destroy(Request $request, Report $reports_admin)
    {
        // Authorization: only owner or superadmin
        if (!$request->user()->canManageReport($reports_admin)) {
            abort(403, 'You are not authorized to delete this report.');
        }

        $reports_admin->delete();
        return redirect()->route('dashboard')->with('success', 'Report deleted successfully.');
    }
}
