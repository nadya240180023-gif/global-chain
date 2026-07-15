<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use App\Models\RiskScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WatchlistController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // Fetch user's watchlisted countries
        $watchlistIds = Watchlist::where('user_id', $userId)->pluck('country_id');
        
        $watchlistCountries = Country::whereIn('id', $watchlistIds)
            ->with(['riskScores' => function ($q) {
                $q->orderBy('recorded_at', 'desc');
            }])
            ->get()
            ->map(function ($country) {
                $latest = $country->riskScores->first();
                return [
                    'id' => $country->id,
                    'name' => $country->name,
                    'code' => $country->code,
                    'flag' => $country->flag,
                    'capital' => $country->capital,
                    'currency_code' => $country->currency_code,
                    'risk_score' => $latest ? $latest->total_score : null,
                    'risk_level' => $latest ? $latest->risk_level : 'Unknown',
                ];
            });

        return view('watchlist.index', compact('watchlistCountries'));
    }

    public function toggle(Country $country)
    {
        $userId = Auth::id();
        $watchlist = Watchlist::where('user_id', $userId)
            ->where('country_id', $country->id)
            ->first();

        if ($watchlist) {
            $watchlist->delete();
            $status = 'removed';
            $msg = "{$country->name} berhasil dihapus dari daftar pantauan Anda.";
        } else {
            Watchlist::create([
                'user_id' => $userId,
                'country_id' => $country->id,
            ]);
            $status = 'added';
            $msg = "{$country->name} berhasil ditambahkan ke daftar pantauan Anda.";
        }

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $status,
                'message' => $msg
            ]);
        }

        return redirect()->back()->with('success', $msg);
    }
}
