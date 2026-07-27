<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use App\Models\RiskScore;
use App\Models\WeatherData;
use Illuminate\Http\Request;

class ShippingEstimatorController extends Controller
{
    public function index(Request $request)
    {
        // Get all ports from DB
        $dbPorts = Port::with('country')->get()->map(function ($p) {
            return [
                'id' => $p->id,
                'name' => $p->name,
                'code' => $p->code,
                'latitude' => floatval($p->latitude),
                'longitude' => floatval($p->longitude),
                'country_id' => $p->country_id,
                'country_name' => optional($p->country)->name ?? 'Unknown',
                'country_code' => optional($p->country)->code ?? '',
            ];
        })->toArray();

        // Standard global ports fallback list if DB doesn't have enough
        $globalPorts = $this->getGlobalPortData();
        
        // Merge them nicely
        $dbCodes = array_column($dbPorts, 'code');
        $supplemental = array_filter($globalPorts, fn($p) => !in_array($p['code'], $dbCodes));
        
        $allPorts = array_merge($dbPorts, array_values($supplemental));

        // Sort alphabetically by name
        usort($allPorts, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        // Check if user submitted calculation
        $result = null;
        $originPort = null;
        $destPort = null;
        $speed = floatval($request->input('speed', 20)); // in knots

        if ($request->filled('origin') && $request->filled('destination')) {
            $originId = $request->input('origin');
            $destId = $request->input('destination');

            // Find selected ports
            foreach ($allPorts as $p) {
                if ($p['code'] === $originId || (is_numeric($originId) && isset($p['id']) && $p['id'] == $originId)) {
                    $originPort = $p;
                }
                if ($p['code'] === $destId || (is_numeric($destId) && isset($p['id']) && $p['id'] == $destId)) {
                    $destPort = $p;
                }
            }

            if ($originPort && $destPort) {
                $result = $this->performCalculation($originPort, $destPort, $speed);
            }
        }

        return view('ports.estimator', compact('allPorts', 'result', 'originPort', 'destPort', 'speed'));
    }

    private function performCalculation($portA, $portB, $speedKnots)
    {
        // 1. Distance Calculation (Haversine Formula)
        $earthRadiusKm = 6371;
        $latFrom = deg2rad($portA['latitude']);
        $lonFrom = deg2rad($portA['longitude']);
        $latTo = deg2rad($portB['latitude']);
        $lonTo = deg2rad($portB['longitude']);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        $distanceKm = $angle * $earthRadiusKm;
        $distanceNauticalMiles = $distanceKm * 0.539957;

        // 2. Base Travel Time (hours & days)
        // 1 knot = 1 Nautical Mile per hour
        $baseHours = $speedKnots > 0 ? $distanceNauticalMiles / $speedKnots : 0;
        $baseDays = $baseHours / 24;

        // 3. Fetch Country Details for Risk & Weather delays
        $countryA = null;
        $countryB = null;

        if (isset($portA['country_id'])) {
            $countryA = Country::find($portA['country_id']);
        } else {
            $countryA = Country::where('code', $portA['country_code'])->first();
        }

        if (isset($portB['country_id'])) {
            $countryB = Country::find($portB['country_id']);
        } else {
            $countryB = Country::where('code', $portB['country_code'])->first();
        }

        $delayDays = 0;

        // --- Weather Risk Delay ---
        $weatherDelay = 0;
        $weatherInfo = [];
        $weatherReasons = [];

        foreach ([['port' => $portA, 'country' => $countryA, 'label' => 'Asal'], ['port' => $portB, 'country' => $countryB, 'label' => 'Tujuan']] as $item) {
            $country = $item['country'];
            $label = $item['label'];
            if ($country) {
                $weather = WeatherData::where('country_id', $country->id)->orderBy('recorded_at', 'desc')->first();
                if ($weather) {
                    $weatherInfo[$label] = $weather;
                    // Add delays based on weather
                    $cond = strtolower($weather->weather_condition);
                    if (str_contains($cond, 'storm') || str_contains($cond, 'thunderstorm') || str_contains($cond, 'bad') || $weather->wind_speed > 25) {
                        $added = 1.5;
                        $weatherDelay += $added;
                        $weatherReasons[] = "Cuaca buruk (" . $weather->weather_condition . ", angin " . $weather->wind_speed . " km/h) di pelabuhan {$label} ({$item['port']['name']}) menyebabkan penundaan sandar kapal.";
                    } elseif (str_contains($cond, 'rain') || $weather->wind_speed > 15) {
                        $added = 0.5;
                        $weatherDelay += $added;
                        $weatherReasons[] = "Kondisi hujan/angin di pelabuhan {$label} ({$item['port']['name']}) menghambat aktivitas bongkar muat kargo.";
                    }
                }
            }
        }
        if (empty($weatherReasons)) {
            $weatherReasons[] = "Kondisi cuaca di pelabuhan asal maupun tujuan terpantau aman dan kondusif untuk pelayaran.";
        }
        $delayDays += $weatherDelay;

        // --- Country/Geopolitical Risk Delay ---
        $riskDelay = 0;
        $riskInfo = [];
        $riskReasons = [];

        foreach ([['port' => $portA, 'country' => $countryA, 'label' => 'Asal'], ['port' => $portB, 'country' => $countryB, 'label' => 'Tujuan']] as $item) {
            $country = $item['country'];
            $label = $item['label'];
            if ($country) {
                $risk = RiskScore::where('country_id', $country->id)->orderBy('recorded_at', 'desc')->first();
                if ($risk) {
                    $riskInfo[$label] = $risk;
                    if ($risk->total_score >= 7.0) {
                        $added = 2.0;
                        $riskDelay += $added;
                        $riskReasons[] = "Tingkat risiko keamanan tinggi di negara {$label} ({$country->name}, skor: {$risk->total_score}) memperketat inspeksi kepabeanan.";
                    } elseif ($risk->total_score >= 5.0) {
                        $added = 1.0;
                        $riskDelay += $added;
                        $riskReasons[] = "Tingkat risiko keamanan sedang di negara {$label} ({$country->name}, skor: {$risk->total_score}) meningkatkan pengawasan otoritas setempat.";
                    }
                }
            }
        }
        if (empty($riskReasons)) {
            $riskReasons[] = "Wilayah negara asal dan tujuan berada dalam zona risiko rendah/stabil, proses bea cukai berjalan normal.";
        }
        $delayDays += $riskDelay;

        // --- Port Congestion Delay (Simulated/Based on Port Code hash)
        $congestionDelay = 0;
        $congestionReasons = [];
        $h = crc32($portA['code'] . $portB['code']) % 10;
        if ($h >= 8) {
            $congestionDelay = 2.5;
            $congestionReasons[] = "Kepadatan lalu lintas kapal (port congestion) sangat tinggi pada rute/pelabuhan terpilih, memperpanjang waktu tunggu antrean sandar.";
        } elseif ($h >= 5) {
            $congestionDelay = 1.0;
            $congestionReasons[] = "Kepadatan lalu lintas kapal sedang di pelabuhan tujuan, menyebabkan sedikit antrean sandar.";
        } else {
            $congestionDelay = 0.2;
            $congestionReasons[] = "Operasional pelabuhan berjalan lancar dengan antrean minimum kapal sandar.";
        }
        $delayDays += $congestionDelay;

        // 4. Total Travel Time
        $totalDays = $baseDays + $delayDays;
        $totalHours = $totalDays * 24;

        $daysOnly = floor($totalDays);
        $hoursOnly = round(($totalDays - $daysOnly) * 24);
        if ($hoursOnly >= 24) {
            $daysOnly += 1;
            $hoursOnly = 0;
        }

        $baseDaysOnly = floor($baseDays);
        $baseHoursOnly = round(($baseDays - $baseDaysOnly) * 24);
        if ($baseHoursOnly >= 24) {
            $baseDaysOnly += 1;
            $baseHoursOnly = 0;
        }

        $delayDaysOnly = floor($delayDays);
        $delayHoursOnly = round(($delayDays - $delayDaysOnly) * 24);
        if ($delayHoursOnly >= 24) {
            $delayDaysOnly += 1;
            $delayHoursOnly = 0;
        }

        return [
            'distance_km' => round($distanceKm, 2),
            'distance_nm' => round($distanceNauticalMiles, 2),
            'base_days' => round($baseDays, 2),
            'delay_days' => round($delayDays, 2),
            'total_days' => round($totalDays, 2),
            'total_hours' => round($totalHours, 1),
            'days_only' => $daysOnly,
            'hours_only' => $hoursOnly,
            'base_days_only' => $baseDaysOnly,
            'base_hours_only' => $baseHoursOnly,
            'delay_days_only' => $delayDaysOnly,
            'delay_hours_only' => $delayHoursOnly,
            'weather_reasons' => $weatherReasons,
            'weather_delay_days' => $weatherDelay,
            'risk_reasons' => $riskReasons,
            'risk_delay_days' => $riskDelay,
            'congestion_reasons' => $congestionReasons,
            'congestion_delay_days' => $congestionDelay,
            'weather_info' => $weatherInfo,
            'risk_info' => $riskInfo,
        ];
    }

    private function getGlobalPortData(): array
    {
        return [
            ['name'=>'Port of Shanghai','code'=>'CNSHA','latitude'=>30.6264,'longitude'=>122.0645,'country_name'=>'China','country_code'=>'CN'],
            ['name'=>'Port of Singapore','code'=>'SGSGP','latitude'=>1.2740,'longitude'=>103.8010,'country_name'=>'Singapore','country_code'=>'SG'],
            ['name'=>'Port of Hamburg','code'=>'DEHAM','latitude'=>53.5394,'longitude'=>9.9782,'country_name'=>'Germany','country_code'=>'DE'],
            ['name'=>'Tanjung Priok','code'=>'IDTPP','latitude'=>-6.0988,'longitude'=>106.8910,'country_name'=>'Indonesia','country_code'=>'ID'],
            ['name'=>'Port of Sydney','code'=>'AUSYD','latitude'=>-33.8608,'longitude'=>151.2136,'country_name'=>'Australia','country_code'=>'AU'],
            ['name'=>'Port of Los Angeles','code'=>'USLAX','latitude'=>33.7288,'longitude'=>-118.2620,'country_name'=>'United States','country_code'=>'US'],
            ['name'=>'Port of Tokyo','code'=>'JPTYO','latitude'=>35.6260,'longitude'=>139.7820,'country_name'=>'Japan','country_code'=>'JP'],
            ['name'=>'Port of London','code'=>'GBLON','latitude'=>51.5034,'longitude'=>0.0538,'country_name'=>'United Kingdom','country_code'=>'GB'],
            ['name'=>'Port of Mumbai','code'=>'INBOM','latitude'=>18.9333,'longitude'=>72.8333,'country_name'=>'India','country_code'=>'IN'],
            ['name'=>'Port of Busan','code'=>'KRBSN','latitude'=>35.0958,'longitude'=>128.9764,'country_name'=>'South Korea','country_code'=>'KR'],
            ['name'=>'Port Klang','code'=>'MYPKG','latitude'=>3.0000,'longitude'=>101.4000,'country_name'=>'Malaysia','country_code'=>'MY'],
            ['name'=>'Laem Chabang Port','code'=>'THLCB','latitude'=>13.0870,'longitude'=>100.8800,'country_name'=>'Thailand','country_code'=>'TH'],
            ['name'=>'Port of Ho Chi Minh','code'=>'VNHCM','latitude'=>10.7769,'longitude'=>106.6890,'country_name'=>'Vietnam','country_code'=>'VN'],
            ['name'=>'Port of Jeddah','code'=>'SAJED','latitude'=>21.4858,'longitude'=>39.1925,'country_name'=>'Saudi Arabia','country_code'=>'SA'],
            ['name'=>'Port of Jebel Ali','code'=>'AEJEA','latitude'=>24.9852,'longitude'=>55.0660,'country_name'=>'UAE','country_code'=>'AE'],
            ['name'=>'Port of Mersin','code'=>'TRMER','latitude'=>36.8000,'longitude'=>34.6333,'country_name'=>'Turkey','country_code'=>'TR'],
            ['name'=>'Port of Le Havre','code'=>'FRLEH','latitude'=>49.4883,'longitude'=>0.1069,'country_name'=>'France','country_code'=>'FR'],
            ['name'=>'Port of Rotterdam','code'=>'NLRTM','latitude'=>51.9225,'longitude'=>4.4792,'country_name'=>'Netherlands','country_code'=>'NL'],
            ['name'=>'Port of Genoa','code'=>'ITGOA','latitude'=>44.4056,'longitude'=>8.9463,'country_name'=>'Italy','country_code'=>'IT'],
            ['name'=>'Port of Barcelona','code'=>'ESBCN','latitude'=>41.3851,'longitude'=>2.1734,'country_name'=>'Spain','country_code'=>'ES'],
            ['name'=>'Port of Gothenburg','code'=>'SEGOT','latitude'=>57.7072,'longitude'=>11.9668,'country_name'=>'Sweden','country_code'=>'SE'],
            ['name'=>'Port of Novorossiysk','code'=>'RUNVS','latitude'=>44.7239,'longitude'=>37.7663,'country_name'=>'Russia','country_code'=>'RU'],
            ['name'=>'Port of Santos','code'=>'BRSSZ','latitude'=>-23.9500,'longitude'=>-46.3389,'country_name'=>'Brazil','country_code'=>'BR'],
            ['name'=>'Port of Vancouver','code'=>'CAVAN','latitude'=>49.2827,'longitude'=>-123.1207,'country_name'=>'Canada','country_code'=>'CA'],
            ['name'=>'Port of Veracruz','code'=>'MXVER','latitude'=>19.2000,'longitude'=>-96.1333,'country_name'=>'Mexico','country_code'=>'MX'],
            ['name'=>'Port of Durban','code'=>'ZADUR','latitude'=>-29.8587,'longitude'=>31.0218,'country_name'=>'South Africa','country_code'=>'ZA'],
            ['name'=>'Port of Lagos','code'=>'NGLOS','latitude'=>6.4531,'longitude'=>3.3958,'country_name'=>'Nigeria','country_code'=>'NG'],
            ['name'=>'Port of Alexandria','code'=>'EGALX','latitude'=>31.2001,'longitude'=>29.9187,'country_name'=>'Egypt','country_code'=>'EG'],
        ];
    }
}
