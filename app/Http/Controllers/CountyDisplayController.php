<?php

namespace App\Http\Controllers;

use App\CountyData;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use DB;
use stdClass;

class CountyDisplayController extends Controller




{
    public function show(Request $request)
    {

function get_percentile($percentile, $array) {
    sort($array);
    $index = ($percentile/100) * count($array);
    if (floor($index) == $index) {
         $result = ($array[$index-1] + $array[$index])/2;
    }
    else {
        $result = $array[floor($index)];
    }
    return $result;
}


        $countyData = CountyData::orderBy('pop_2016','desc')->get();
        $maxIncome = CountyData::max('median_income');
        $maxPopulation = CountyData::max('pop_2016');
        $maxTotalDeaths = CountyData::max('total_deaths');
        $maxTotalChargeAmount = CountyData::max('total_charge_amount');
        $maxMedicarePerCapita = CountyData::max('medicare_per_capita');
        $maxNumHospices = CountyData::max('num_hospices');
        $maxPerCapitaIncome = CountyData::select(DB::raw('max(median_income/pop_2016) as per_capita_income'))->first();
        $maxPerCapitaIncome = $maxPerCapitaIncome->per_capita_income;
        $maxNumHospicesPerCapita = CountyData::select(DB::raw('max(num_hospices/pop_2016) as per_capita_hospices'))->first();
        $maxNumHospicesPerCapita = $maxNumHospicesPerCapita->per_capita_hospices;

        //calculate scoring method
        foreach($countyData as $key => $county) {
            $quintile = ceil(($key+1)/51);
            $county->score  = $quintile/5;
        }
        return view('welcome', ['countyData' => $countyData]);

    }
}


/*
($county->pop_2016/$maxPopulation)
                            + ($county->total_deaths/$maxTotalDeaths)
                            + ($county->total_charge_amount/$maxTotalChargeAmount)
                            + ($county->total_charge_amount/$maxTotalChargeAmount)
                            +


                            */
