<?php

namespace App\Http\Controllers;

use App\RecommendationMatrix;
use App\HospiceData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class HospiceDisplayController extends Controller




{
    public function show(Request $request)
    {
//phpinfo();die;
        $url = 'https://raw.githubusercontent.com/OpenDataDE/State-zip-code-GeoJSON/master/tx_texas_zip_codes_geo.min.json';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $tx_str = curl_exec($ch);
        curl_close($ch);
        //$tx_geojson = json_decode($tx_str);

        $tx_data = HospiceData::get();

        return view('welcome', ['tx_data' => $tx_data,
            'tx_geojson' => $tx_str,
        ]);

    }
}
