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


    //show drug results
    public function display() {
        //show drug results on a standalone page

    }


    //gets api key from config file
    public function getOpenFdaApiKey(){
        return config('values.openFdaApiKey');
    }


    //get drug results
    public function getDrugs(Request $request) {
        //build api query here

        $query = self::$api_url . self::$endPoint['drugsFDA'] . "api_key=" . self::getOpenFdaApiKey() . '&search=openfda.brand_name:' . $request->drugName . '+openfda.generic_name:' . $request->drugName . '&limit=' . $request->count;

        
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


        $data = $json->results;
        
        
        return view('drugs.search-results',['drugs' => $data]);
    }


}
