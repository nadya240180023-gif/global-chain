<?php

use Illuminate\Support\Facades\Route;
use App\Models\Country;
use App\Models\Port;
use App\Models\NewsCache;
use App\Models\ExchangeRate;
use App\Models\RiskScore;
use Illuminate\Http\Request;

// 1. GET /api/countries
Route::get('/countries', function () {
    $countries = Country::with(['riskScores' => function ($q) {
        $q->orderBy('created_at', 'desc');
    }])->get();

    return response()->json($countries->map(function ($c) {
        $latestScore = $c->riskScores->first();
        return [
            'id' => $c->id,
            'name' => $c->name,
            'code' => $c->code,
            'capital' => $c->capital,
            'region' => $c->region,
            'flag' => $c->flag,
            'population' => $c->population,
            'latitude' => $c->latitude,
            'longitude' => $c->longitude,
            'risk_score' => $latestScore ? $latestScore->total_score : null,
            'risk_level' => $latestScore ? $latestScore->risk_level : 'Unknown',
        ];
    }));
});

// 2. GET /api/risk
Route::get('/risk', function (Request $request) {
    $code = $request->query('code');
    $countryId = $request->query('country_id');

    $query = Country::query();
    if ($countryId) {
        $query->where('id', $countryId);
    } elseif ($code) {
        $query->where('code', strtoupper($code));
    } else {
        return response()->json(['error' => 'Country ID or code is required'], 400);
    }

    $country = $query->first();
    if (!$country) {
        return response()->json(['error' => 'Country not found'], 404);
    }

    $latestScore = RiskScore::where('country_id', $country->id)
        ->orderBy('recorded_at', 'desc')
        ->first();

    return response()->json([
        'country' => $country->name,
        'code' => $country->code,
        'risk_score' => $latestScore ? $latestScore->total_score : 20,
        'risk_level' => $latestScore ? $latestScore->risk_level : 'Low',
        'breakdown' => [
            'weather' => $latestScore ? $latestScore->weather_score : 20,
            'inflation' => $latestScore ? $latestScore->inflation_score : 20,
            'currency' => $latestScore ? $latestScore->currency_score : 20,
            'news' => $latestScore ? $latestScore->news_score : 20,
        ]
    ]);
});

// 3. GET /api/ports
Route::get('/ports', function (Request $request) {
    $code = $request->query('code');
    $query = Port::with('country');
    if ($code) {
        $query->whereHas('country', function ($q) use ($code) {
            $q->where('code', strtoupper($code));
        });
    }

    $ports = $query->get();
    return response()->json($ports->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'code' => $p->code,
            'country' => $p->country->name,
            'country_code' => $p->country->code,
            'latitude' => floatval($p->latitude),
            'longitude' => floatval($p->longitude),
        ];
    }));
});

// 4. GET /api/news
Route::get('/news', function (Request $request) {
    $code = $request->query('code');
    $query = NewsCache::with('country');
    if ($code) {
        $query->whereHas('country', function ($q) use ($code) {
            $q->where('code', strtoupper($code));
        });
    }

    $news = $query->orderBy('published_at', 'desc')->get();
    return response()->json($news->map(function ($n) {
        return [
            'id' => $n->id,
            'country' => $n->country ? $n->country->name : 'Global',
            'title' => $n->title,
            'description' => $n->description,
            'source' => $n->source,
            'published_at' => $n->published_at,
            'category' => $n->category,
            'sentiment' => $n->sentiment,
            'sentiment_score' => floatval($n->sentiment_score),
        ];
    }));
});

// 5. GET /api/currency
Route::get('/currency', function (Request $request) {
    $code = $request->query('code');
    $query = ExchangeRate::with('country');
    if ($code) {
        $query->whereHas('country', function ($q) use ($code) {
            $q->where('code', strtoupper($code));
        });
    }

    $rates = $query->orderBy('recorded_at', 'desc')->get();
    return response()->json($rates->map(function ($r) {
        return [
            'id' => $r->id,
            'country' => $r->country->name,
            'base' => $r->base_currency,
            'target' => $r->target_currency,
            'exchange_rate' => floatval($r->exchange_rate),
            'recorded_at' => $r->recorded_at,
        ];
    }));
});
