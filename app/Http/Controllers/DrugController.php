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
    public function index()
    {
        return view('drugs.index');
    }


    //gets api key from config file
    public function getOpenFdaApiKey()
    {
        return config('values.openFdaApiKey');
    }


    //query builder
    public function stitchDrugQuery($request, string $searchMethod, string $endPointKey)
    {

        //marketing_status attempts to exclude
        $query = self::$api_url . self::$endPoint[$endPointKey] . "api_key=" . self::getOpenFdaApiKey();
        $discludeDiscontinued =  '&search=products.marketing_status:(Prescription+OR+Over-the-counter)+AND+';

        switch ($searchMethod) {
            case 'byName':
                $query .= $discludeDiscontinued . 'products.brand_name:' . $request->drugName . '&limit=' . $request->count . '&sort=application_number:asc';
                break;

            case 'byApplicationNumProductNum':
                $query .= $discludeDiscontinued . 'application_number:"' . $request->application_num . '"+AND+products.product_number:' . $request->product_num . '&limit=1' . '&sort=application_number:asc';
                break;

            case 'byAdverseEvents':
                $query .= '&search=' . $request->adverse_events . 'patient.reaction.reactionmeddrapt:' . '&limit=1';
                break;
            //search by indications and usage
            case 'byProductLabeling':
                $query .=  '&search=' . $request->drugName . '+AND+count=indications_and_usage' . '&limit=1';
                break;
        }


//         dd($query);
        return $query;

    }


    //execute the given query and store results to cache
    public function getDrugs($query)
    {

        // dd($query);

        //Create unique cache key by turning query url into md5 hash
        $cacheKey = 'drugs.' . md5($query);

        $json = Cache::remember($cacheKey, 3600, function () use ($query) {

            //Return object
            return json_decode(Http::get($query)->getBody());
        });

        return $json;

    }

    //get drug results by name
    public function getDrugsByName(Request $request)
    {
        //build api query here

        $query = self::stitchDrugQuery($request, 'byName', 'drugsFDA');


        $data = self::getDrugs($query);
        //$data2 = self::getDrugs($query);


        return $data;
    }


    //get drug results by application number & product number
    public function getIndividualDrugData($request, $searchMethod, $endPointKey)
    {
        //build api query here

        $query = self::stitchDrugQuery($request, $searchMethod, $endPointKey);
         //dd($query);

        $data = self::getDrugs($query);


        return $data;
    }



    //show drug results
    public function showDrug(Request $request)
    {

        $data = self::getIndividualDrugData($request, 'byApplicationNumProductNum', 'drugsFDA');
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

        //dd($data->results[0]->products[0]->brand_name);

        $labelingData = self::getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');



        return view('drugs.show',['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }


    public function showDrugsSearch(Request $request)
    {

        $data = self::getDrugsByName($request);

        if (isset($data->error->code) == 'NOT_FOUND') {
            return redirect('/')->with('message', "No matches found! Please check your spelling and try again.")
                ->withInput();
        }

        return view('drugs.search-results', ['drugs' => $data->results]);
    }
}




