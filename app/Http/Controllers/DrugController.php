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
    static function getOpenFdaApiKey() {
        return config('values.openFdaApiKey');
    }


    //query builder
    public function stitchDrugQuery($dataToSearch, string $searchMethod, string $endPointKey) {

        //marketing_status attempts to exclude
        $query = self::$api_url . self::$endPoint[$endPointKey] . "api_key=" . $this->getOpenFdaApiKey();
        $discludeDiscontinued =  '&search=products.marketing_status:(Prescription+OR+Over-the-counter)+AND+';

        switch ($searchMethod) {
            //search by drug name
            case 'byName':
                $query .= $discludeDiscontinued . 'products.brand_name:' . $dataToSearch->drugName . '&limit=100' . '&sort=application_number:asc';
                break;
            //search by application num and product num
            case 'byApplicationNumProductNum':
                $query .= $discludeDiscontinued . 'application_number:"' . $dataToSearch->application_num . '"+AND+products.product_number:' . $dataToSearch->product_num . '&limit=1' . '&sort=application_number:asc';
                break;
            //search by indications and usage
            case 'byProductLabeling':
                $query .=  '&search=' . $dataToSearch->drugName . '+AND+count=indications_and_usage' . '&limit=1';
                break;
            case 'byNDC';
                $query .= '&search=' . $dataToSearch->drugName . '&limit=1';
            //search by adverse events
            case 'byAdverseEvents':
                $query .= '&search=' . $dataToSearch->drugName . '+AND+count=patient.reaction.reactionmeddrapt.exact' . '&limit=10';
                break;
        }

        return $query;

    }


    //execute the given query and store results to cache
    public function getAndCacheDrugs($query) {

        //Create unique cache key by turning query url into md5 hash
        $cacheKey = 'drugs.' . md5($query);

        $json = Cache::remember($cacheKey, 3600, function () use ($query) {

            //Return object
            return json_decode(Http::get($query)->getBody());
        });

        return $json;

    }

    //get drug results by name
    public function getDrugsByName(Request $request) {
        //build api query here

        $query = $this->stitchDrugQuery($request, 'byName', 'drugsFDA');


        $data = $this->getAndCacheDrugs($query);

        return $data;
    }


    //get drug results by application number & product number
    public function getIndividualDrugData($request, $searchMethod, $endPointKey) {
        //build api query here

        $query = $this->stitchDrugQuery($request, $searchMethod, $endPointKey);

        $data = $this->getAndCacheDrugs($query);

        return $data;
    }



    //show drug results
    public function showDrug(Request $request) {

        $data = $this->getIndividualDrugData($request, 'byApplicationNumProductNum', 'drugsFDA');

        if (isset($data->error->code) == 'NOT_FOUND') {
            return redirect('/')->with('message', "Product not found! If this issue persists please contact the support department.");
        }

        $iteration = 0;

        foreach($data->results[0]->products as $product){
            if($product->product_number === $request->product_num){
                unset($data->results[0]->products);
                $data->results[0]->products[] = $product;
                break;
            }
        }

        $labelingData = $this->getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');


        return view('drugs.show',['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }


    public function showDrugsSearch(Request $request) {

        $data = $this->getDrugsByName($request);

        $this->ensureDrugDataIsValid($data);

        // if (count($data->results) === 1 && count($data->results[0]->products) === 1) {
        //     return view('drugs.show', ['drug' => $data->results[0]])
        // }

        // dd($data->results);

        return view('drugs.search-results', ['drugs' => $data->results]);
    }

    protected function ensureDrugDataIsValid($dataResults) {
        if (isset($data->error->code) === 'NOT_FOUND') {
            return redirect('/')->with('message', "No matches found! Please check your spelling and try again.")
                ->withInput();
        }

        return;
    }
}




