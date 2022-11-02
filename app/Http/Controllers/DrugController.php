<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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


    static $api_key =  "api_key=ApASi6a1Uzs5ageWsCTL6uaiSux0PCgFYs6k0VnS";
    

    //index page
    public function index() {
        return view('drugs.index');
    }


    //show drug results
    public function display() {
        //show drug results on a standalone page

    }


    //get drug results
    public function getDrugs(Request $request) {
        //build api query here

        $query = self::$api_url . self::$endPoint['drugsFDA'] . self::$api_key . '&search=openfda.brand_name:' . $request->drugName . '+openfda.generic_name:' . $request->drugName . '&limit=' . $request->count;

        
        $response = Http::get($query);

        
        $data = json_decode($response->getBody());
        
        
        
        // dd($data->results);

        return view('drugs.search-results',$data->results);

    }


}
