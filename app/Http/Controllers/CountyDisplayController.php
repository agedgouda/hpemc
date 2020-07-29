<?php

namespace App\Http\Controllers;

use App\CountyData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;

class CountyDisplayController extends Controller




{
    public function show(Request $request)
    {
        $countyData = CountyData::get();

//echo json_encode($countyData);die;
        return view('welcome', ['countyData' => $countyData]);

    }
}
