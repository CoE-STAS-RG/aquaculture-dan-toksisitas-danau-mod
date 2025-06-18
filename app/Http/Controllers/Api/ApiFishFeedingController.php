<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\FishFeeding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApiFishFeedingController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $feedings = FishFeeding::where('user_id', $userId)
            ->latest()
            ->get();

        $chartData = FishFeeding::where('user_id', $userId)
            ->whereNotNull('fish_weight')
            ->orderBy('feeding_time')
            ->get();

        $labels = $chartData->pluck('feeding_time')
            ->map(fn($d) => Carbon::parse($d)->format('d M'))
            ->toArray();

        $weights = $chartData->pluck('fish_weight')->toArray();

        return response()->json([
            'feedings' => $feedings,
            'chart' => [
                'labels' => $labels,
                'weights' => $weights,
                'raw' => $chartData,
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'fish_name' => 'required|string|max:255',
            'feed_type' => 'required|string|max:255',
            'feeding_time' => 'required|date',
            'feed_weight' => 'required|numeric',
            'fish_weight' => 'nullable|numeric',
            'fish_count' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $feeding = FishFeeding::create([
            'user_id' => Auth::id(),
            'fish_name' => $request->fish_name,
            'feed_type' => $request->feed_type,
            'feeding_time' => $request->feeding_time,
            'feed_weight' => $request->feed_weight,
            'fish_weight' => $request->fish_weight,
            'fish_count' => $request->fish_count,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'message' => 'Feeding data successfully added.',
            'feeding' => $feeding
        ], 201);
    }
}
