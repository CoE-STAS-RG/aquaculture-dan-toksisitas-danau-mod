<?php

namespace App\Http\Controllers;

use App\Models\FishFeeding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FishFeedingController extends Controller
{
    public function index()
    {
        try {
            $feedings = FishFeeding::where('user_id', Auth::id())->latest()->get();

            $chartData = FishFeeding::where('user_id', Auth::id())
                ->whereNotNull('fish_weight')
                ->orderBy('feeding_time')
                ->get();

            $labels = $chartData->pluck('feeding_time')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toArray();
            $weights = $chartData->pluck('fish_weight')->toArray();

            return view('fish-feedings.index', compact('feedings','labels','weights','chartData'));
        } catch (\Exception $e) {
            \Log::error('Error loading fish feedings: ' . $e->getMessage());
            return view('fish-feedings.index', [
                'feedings' => collect(),
                'labels' => [],
                'weights' => [],
                'chartData' => collect()
            ]);
        }
    }

    public function create()
    {
        try {
            $chartData = FishFeeding::where('user_id', Auth::id())
                ->whereNotNull('fish_weight')
                ->orderBy('feeding_time')
                ->get();

            $labels = $chartData->pluck('feeding_time')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d M'))->toArray();
            $weights = $chartData->pluck('fish_weight')->toArray();
            $feedings = FishFeeding::where('user_id', Auth::id())->latest()->get();

            return view('fish-feedings.create', compact('feedings','labels','weights','chartData'));
        } catch (\Exception $e) {
            \Log::error('Error loading fish feeding form: ' . $e->getMessage());
            return redirect()->route('index-fish')->with('error', 'Gagal memuat halaman.');
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'fish_name' => 'required|string|max:255',
                'feed_type' => 'required|string|max:255',
                'feeding_time' => 'required',
                'feed_weight' => 'required|numeric',
                'fish_weight' => 'nullable|numeric',
                'fish_count' => 'nullable|integer',
                'notes' => 'nullable|string',
            ]);

            FishFeeding::create([
                'user_id' => Auth::id(),
                'fish_name' => $request->fish_name,
                'feed_type' => $request->feed_type,
                'feeding_time' => $request->feeding_time,
                'feed_weight' => $request->feed_weight,
                'fish_weight' => $request->fish_weight,
                'fish_count' => $request->fish_count,
                'notes' => $request->notes,
            ]);

            return redirect()->route('index-fish')->with('success', 'Data berhasil ditambahkan!');
        } catch (\Exception $e) {
            \Log::error('Error creating fish feeding: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan data. Silakan coba lagi.');
        }
    }
}
