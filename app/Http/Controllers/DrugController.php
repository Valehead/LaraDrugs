<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class DrugController extends Controller
{
    static $api_url = "https://api.fda.gov/drug/";

    static $endPoint = [
        'adverseEvents' => 'event.json?',
        'productLabeling' => 'label.json?',
        'NDC' => 'ndc.json?',
        'recallEnforcement' => 'enforcement.json?',
        'drugsFDA' => 'drugsfda.json?'
        ];



    //index page
    public function index() {
        return view('drugs.index');
    }


    //gets api key from config file
    public function getOpenFdaApiKey(){
        return config('values.openFdaApiKey');
    }



    //query builder
    public function stitchDrugQuery(Request $request, String $searchMethod){

        $query = self::$api_url . self::$endPoint['drugsFDA'] . "api_key=" . self::getOpenFdaApiKey() . '&search=';

        switch($searchMethod){
            case 'byName':
                $query .= 'openfda.brand_name:' . $request->drugName . '+openfda.generic_name:' . $request->drugName . '&limit=' . $request->count . '&sort=application_number:asc';
                break;
            case 'byNDC':
                $query .= 'openfda.product_ndc:"' . $request->product_ndc . '"&limit=1' . '&sort=application_number:asc';
                break;
        };

        // dd($query);
        return $query;

    }



    //execute the given query and store results to cache
    public function getDrugs($query){
        //Create unique cache key by turning query url into md5 hash
        $cacheKey = 'drugs.'.md5($query);

        $json = Cache::remember($cacheKey, 3600, function () use ($query) {

            //Return object
            return json_decode(Http::get($query)->getBody());
        });


        if(isset($json->error->code) == 'NOT_FOUND'){
            return redirect('/')->with('message', "No matches found! Please check your spelling and try again.")
            ->withInput();
        }


        return $json->results;

    }

    //get drug results by name
    public function getDrugsByName(Request $request) {
        //build api query here

        $query = self::stitchDrugQuery($request, 'byName');


        $data = self::getDrugs($query);


        return $data;
    }



    //get drug results by name
    public function getDrugByNDC(Request $request) {
        //build api query here

        $query = self::stitchDrugQuery($request, 'byNDC');


        $data = self::getDrugs($query);


        return $data;
    }



    public function showDrugsSearch(Request $request){

        $data = self::getDrugsByName($request);

        return view('drugs.search-results',['drugs' => $data]);
    }




    //show drug results
    public function showDrug(Request $request) {

        $data = self::getDrugByNDC($request);

        return view('drugs.show',['drug' => $data]);

    }

}
