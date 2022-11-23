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

    //fallback page
    public function trySearchingForMedication() {
        return redirect('/')->with('message', "Oops! Something unexpected happened. Try searching for a medication here!");
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

            return json_decode(Http::get($query)->getBody());
        });

        return $json;

    }

    //get drug results by application number & product number
    public function getIndividualDrugData($request, $searchMethod, $endPointKey) {

        $query = $this->stitchDrugQuery($request, $searchMethod, $endPointKey);

        $data = $this->getAndCacheDrugs($query);

        return $data;
    }



    //show drug results
    public function showDrug(Request $request) {

        $data = $this->getIndividualDrugData($request, 'byApplicationNumProductNum', 'drugsFDA');


        if(! $this->ensureDrugDataIsValid($data)) {
            return $this->returnNoMatchesFound();
        };


        $this->ensure_returned_data_is_the_correct_product($data, $request->product_num);


        $labelingData = $this->getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');


        return view('drugs.show',['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }

    //show drugs search page and process search
    public function showDrugsSearch(Request $request) {

        $data = $this->getIndividualDrugData($request, 'byName', 'drugsFDA');


        if(! $this->ensureDrugDataIsValid($data)) {
            return $this->returnNoMatchesFound();
        };


        if ($this->ensureOnlyOneProductReturned($data)) {
            return $this->skip_search_results_page_if_only_one_result($data);
        }


        return view('drugs.search-results', ['drugs' => $data->results]);

    }


    //method to help get drug label info when search only returns 1 result
    public function skip_search_results_page_if_only_one_result($data) {

        $labelingData = $this->getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');

        return view('drugs.show', ['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }




    //utility functions below
    //-----------------------------------------------------------------

    private function ensureDrugDataIsValid($dataResults) {

        if (isset($dataResults->error->code)){
            return false;
        }

        return true;

    }


    private function returnNoMatchesFound(){
        return redirect('/')->with('message', "No Medications found! Please check your spelling and try again.")
        ->withInput();
    }


    private function ensureOnlyOneProductReturned($dataResults) {

        if(count($dataResults->results) === 1 && count($dataResults->results[0]->products) === 1) {
            return true;
        }

        return false;

    }

    private function ensure_returned_data_is_the_correct_product(&$dataResults, $productNumber) {

        $result = array_values(array_filter($dataResults->results[0]->products, function ($product) use ($productNumber) {
            return $product->product_number === $productNumber;
        }));

        unset($dataResults->results[0]->products);

        $dataResults->results[0]->products = $result;

        return;

    }

}




