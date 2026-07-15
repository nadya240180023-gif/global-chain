<?php

namespace App\Http\Controllers;

use App\Models\Port;
use App\Models\Country;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $countryId = $request->query('country_id');

        $query = Port::with('country');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('code', 'like', '%' . $search . '%');
            });
        }

        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        $ports = $query->get();
        $countries = Country::orderBy('name')->get();

        // Convert to mapped array for Leaflet markers
        $mapPorts = $ports->map(function ($p) {
            return [
                'name' => $p->name,
                'code' => $p->code,
                'latitude' => floatval($p->latitude),
                'longitude' => floatval($p->longitude),
                'country_name' => $p->country->name,
                'country_code' => $p->country->code,
            ];
        });

        return view('ports.index', compact('ports', 'countries', 'mapPorts', 'search', 'countryId'));
    }
}
