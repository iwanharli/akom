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
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'reports' => Report::latest()->get()
        ]);
    }

    public function create()
    {
        return Inertia::render('Reports/Create');
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
        ]);

        DB::transaction(function () use ($validated, $request) {
            $report = Report::create($validated);

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

        return redirect()->route('dashboard')->with('success', 'Report created successfully.');
    }

    public function show($uuid)
    {
        $report = Report::where('uuid', $uuid)
            ->with(['stats', 'sections.analysis_items', 'sections.charts'])
            ->firstOrFail();

        return Inertia::render('Reports/View', [
            'report' => $report
        ]);
    }

    public function edit(Report $report_admin) // Route model binding uses -admin because of resource name
    {
        return Inertia::render('Reports/Edit', [
            'report' => $report_admin->load(['stats', 'sections.analysis_items', 'sections.charts'])
        ]);
    }

    public function update(Request $request, Report $report_admin)
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
        ]);

        DB::transaction(function () use ($validated, $request, $report_admin) {
            $report_admin->update($validated);

            // Sync Stats (Delete and Recreate for simplicity)
            $report_admin->stats()->delete();
            if ($request->has('stats')) {
                foreach ($request->input('stats') as $stat) {
                    $report_admin->stats()->create($stat);
                }
            }

            // Sync Sections
            $report_admin->sections()->delete(); // Warning: This also deletes children via cascade if setup
            if ($request->has('sections')) {
                foreach ($request->input('sections') as $sec) {
                    $section = $report_admin->sections()->create([
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

        return redirect()->route('dashboard')->with('success', 'Report updated successfully.');
    }

    public function destroy(Report $report_admin)
    {
        $report_admin->delete();
        return redirect()->route('dashboard')->with('success', 'Report deleted successfully.');
    }
}
