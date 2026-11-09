<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class LandingPageController extends Controller
{
    /**
     * Display the modern landing page inspired by the reference design.
     */
    public function index(Request $request)
    {
        $setting = Setting::first() ?? new Setting();
        
        $roomsQuery = Room::query()->with('activeTenant');

        // Optional filtering from landing search bar
        if ($request->filled('location') && $request->location !== 'all') {
            $roomsQuery->where('location', $request->location);
        }
        if ($request->filled('floor') && $request->floor !== 'all') {
            $roomsQuery->where('floor', $request->floor);
        }
        if ($request->filled('type') && $request->type !== 'all') {
            $roomsQuery->where('type', 'like', '%' . $request->type . '%');
        }
        if ($request->filled('budget') && $request->budget !== 'all') {
            if ($request->budget === 'under15') {
                $roomsQuery->where('price', '<=', 1500000);
            } elseif ($request->budget === 'over15') {
                $roomsQuery->where('price', '>', 1500000);
            }
        }

        $allRooms = Room::orderBy('number')->get();
        $popularRooms = Room::orderBy('price', 'desc')->take(3)->get();
        $availableCount = Room::where('status', 'available')->count();
        $totalRooms = Room::count();
        $branches = $setting->branches;

        return view('landing', compact(
            'setting',
            'branches',
            'allRooms',
            'popularRooms',
            'availableCount',
            'totalRooms'
        ));
    }

    /**
     * Store incoming survey booking from landing page (via AJAX).
     */
    public function submitSurvey(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'job' => 'nullable|string|max:100',
            'branch' => 'nullable|string|max:100',
            'room' => 'nullable|string|max:150',
            'date' => 'nullable|string|max:50',
            'time' => 'nullable|string|max:50',
            'method' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:5000',
        ]);

        $surveyDate = null;
        if (!empty($validated['date'])) {
            try {
                $surveyDate = \Carbon\Carbon::parse($validated['date'])->format('Y-m-d');
            } catch (\Throwable $e) {
                $surveyDate = null;
            }
        }

        $survey = \App\Models\SurveyRequest::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'] ?? null,
            'job' => $validated['job'] ?? 'Mahasiswa',
            'branch' => $validated['branch'] ?? 'Lokasi 1',
            'room' => $validated['room'] ?? null,
            'survey_date' => $surveyDate,
            'survey_time' => $validated['time'] ?? '14:00',
            'survey_method' => $validated['method'] ?? 'Datang Langsung',
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
            'is_read' => false,
        ]);

        // Kirim notifikasi otomatis ke Gmail Admin dan Owner
        try {
            app(\App\Services\EmailNotificationService::class)->sendNewSurveyNotification($survey);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Failed notifying staff of new survey: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Permohonan survey lokasi berhasil dicatat dan notifikasi telah dikirim ke Dashboard Pengelola.',
            'survey' => $survey,
        ]);
    }
}

