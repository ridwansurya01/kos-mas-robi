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

        $allRooms = $roomsQuery->orderBy('number')->get();
        $popularRooms = Room::orderBy('price', 'desc')->take(3)->get();
        $availableCount = Room::where('status', 'available')->count();
        $totalRooms = Room::count();

        return view('landing', compact(
            'setting',
            'allRooms',
            'popularRooms',
            'availableCount',
            'totalRooms'
        ));
    }
}

