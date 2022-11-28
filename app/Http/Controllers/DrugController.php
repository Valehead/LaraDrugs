<?php
/**
 * Copyright © Nextvisit Inc. All rights reserved.
 */


namespace App\Http\Controllers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

/**
 * Nextvisit Drug Controller
 *
 * Interfaces with the openFDA API to provide up-to-date medication data through user searches.
 *
 * @version 1.0.0
 * @author Nick Vales & Maxx Grass
 */
class DrugController extends Controller
{
    /**
     * Used to supply first segment of base API URL.
     * @var string <p>First segment of base api url to access Drug API.</p>
     */
    static string $api_url = "https://api.fda.gov/drug/";


    /**
     * Used to supply the different endPoint URL segments to the stitchDrugQuery method based on value supplied by methods accessing it.
     * @var string[] <p>Array of endPoint strings for URL construction.</p>
     */
    static array $endPoint = [
        'adverseEvents' => 'event.json?',
        'productLabeling' => 'label.json?',
        'NDC' => 'ndc.json?',
        'recallEnforcement' => 'enforcement.json?',
        'drugsFDA' => 'drugsfda.json?'
    ];


    /**
     * Returns Index view for main project page.
     * @return Application|Factory|View
     */
    public function index() {
        return view('drugs.index');
    }


    /**
     * Default fallback method for when a route/view is not defined.
     * @return Application|RedirectResponse|Redirector
     */
    public function trySearchingForMedication() {
        return redirect('/')->with('message', "Oops! Something unexpected happened. Try searching for a medication here!");
    }



    /**
     * Returns the openFdaApiKey from the project's .env file.
     * @return Repository|Application|mixed
     */
    static function getOpenFdaApiKey() {
        return config('values.openFdaApiKey');
    }



    /**
     * Returns the API call string after it has been stitched together from input variables and static class variables.
     * @param object $dataToSearch <p>Object containing Drug FDA info needed to add to the query.</p>
     * @param string $searchMethod <p>Search method to be performed.</p>
     * @param string $endPointKey <p>Key to access static $endPoint array.</p>
     * @return string
     */
    public function stitchDrugQuery(object $dataToSearch, string $searchMethod, string $endPointKey) {

        //marketing_status attempts to exclude
        $query = self::$api_url . self::$endPoint[$endPointKey] . "api_key=" . $this->getOpenFdaApiKey();
        $filterDiscontinuedDrugs =  '&search=products.marketing_status:(Prescription+OR+Over-the-counter)+AND+';

        switch ($searchMethod) {
            //search by drug name
            case 'byName':
                $query .= $filterDiscontinuedDrugs . 'products.brand_name:' . $dataToSearch->drugName . '&limit=100' . '&sort=application_number:asc';
                break;
            //search by application num and product num
            case 'byApplicationNumAndProductNum':
                $query .= $filterDiscontinuedDrugs . 'application_number:"' . $dataToSearch->application_num . '"+AND+products.product_number:' . $dataToSearch->product_num . '&limit=1' . '&sort=application_number:asc';
                break;
            //search by indications and usage
            case 'byProductLabeling':
                $query .=  '&search=' . $dataToSearch->drugName . '+AND+count=indications_and_usage' . '&limit=1';
                break;
            case 'byNDC';
                $query .= '&search=' . $dataToSearch->drugName . '&limit=1';
                break;
            //search by adverse events
            case 'byAdverseEvents':
                $query .= '&search=' . $dataToSearch->drugName . '+AND+count=patient.reaction.reactionmeddrapt.exact' . '&limit=10';
                break;
        }

        return $query;

    }




    /**
     * Returns and caches results for API call result.
     * @param string $query <p>API Query String returned from the stitchDrugQuery method.</p>
     * @return object <p>Result from the API call</p>
     */
    public function getAndCacheDrugs(string $query) {

        //Create unique cache key by turning query url into md5 hash
        $cacheKey = 'drugs.' . md5($query);

        return $this->getResultsFromApiAndCache($cacheKey, $query);

    }




    /**
     * Returns an Object of data returned by the API call for a single Medication.
     * @param $request <p>Incoming Request object containing the application number and corresponding product number for what needs to be searched for.</p>
     * @param string $searchMethod <p>Search method to be performed.</p>
     * @param string $endPointKey <p>Key to access static $endPoint array.</p>
     * @return object <p>API call result.</p>
     */
    public function getIndividualDrugData(object $request, string $searchMethod, string $endPointKey) {

        $query = $this->stitchDrugQuery($request, $searchMethod, $endPointKey);

        return $this->getAndCacheDrugs($query);

    }




    /**
     * Returns the view page for a single Medication. Also returns the data objects containing product details.
     * @param Request $request <p>Request object being supplied by the router.</p>
     * @return Application|Factory|View|RedirectResponse|Redirector <p>Single Medication View and API results objects</p>
     */
    public function showSingleDrug(Request $request) {

        $data = $this->getIndividualDrugData($request, 'byApplicationNumAndProductNum', 'drugsFDA');


        if(! $this->ensureDrugDataIsValid($data)) {
            return $this->returnNoMatchesFound();
        }


        $this->ensure_returned_data_is_the_correct_product($data, $request->product_num);

        $labelingData = $this->getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');


        return view('drugs.show',['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }





    /**
     * Returns Medication Search Results View and Drugs data returned from the API
     * @param Request $request <p>Request object being supplied by the router.</p>
     * @return Application|Factory|View|RedirectResponse|Redirector <p>Search Results View and API results object</p>
     */
    public function showDrugsSearch(Request $request) {

        $data = $this->getIndividualDrugData($request, 'byName', 'drugsFDA');


        if(! $this->ensureDrugDataIsValid($data)) {
            return $this->returnNoMatchesFound();
        }


        if ($this->ensureOnlyOneProductReturned($data)) {
            return $this->skip_search_results_page_if_only_one_result($data);
        }


        return view('drugs.search-results', ['drugs' => $data->results]);

    }




    /**
     * Returns single Medication View if an initial search only returns one medication result.
     * @param object $data <p>Object containing initial Drug data returned from the drugsFDA endpoint. It will be used to GET label data, then it will also be returned to the view.</p>
     * @return Application|Factory|View <p>Single Medication View and API results objects.</p>
     */
    public function skip_search_results_page_if_only_one_result(object $data) {

        $labelingData = $this->getIndividualDrugData((object) ['drugName' => $data->results[0]->products[0]->brand_name], 'byProductLabeling', 'productLabeling');

        return view('drugs.show', ['drug' => $data->results[0], 'druginfo' => $labelingData->results[0]]);

    }



    /**
     * Returns a bool based on if the API returned valid data or not.
     * @param object $dataResults <p>API results</p>
     * @return bool
     */
    private function ensureDrugDataIsValid(object $dataResults) {

        if (isset($dataResults->error->code)){
            return false;
        }

        return true;

    }



    /**
     * Returns a redirect to the Index view if the initial API search doesn't return any medications.
     * @return Application|RedirectResponse|Redirector <p>Redirect with prior form input as well as a message telling the user what happened.</p>
     */
    private function returnNoMatchesFound(){

        return redirect('/')->with('message', "No Medications found! Please check your spelling and try again.")
        ->withInput();

    }



    /**
     * Returns a bool based on if only 1 product was returned by the search API call.
     * @param object $dataResults <p>Medication results data</p>
     * @return bool
     */
    private function ensureOnlyOneProductReturned(object $dataResults) {

        if(count($dataResults->results) === 1 && count($dataResults->results[0]->products) === 1) {
            return true;
        }

        return false;

    }



    /**
     * Modifies the $dataResults object by reference to ensure the product we're attempting to serve IS the product the user chose to view.
     * @param object &$dataResults <p>Parameter to be modified to only include the single requested product.</p>
     * @param $productNumber <p>Product number specified by the Request url.</p>
     * @return void
     */
    private function ensure_returned_data_is_the_correct_product(object $dataResults, $productNumber): void{

        $result = array_values(array_filter($dataResults->results[0]->products, function ($product) use ($productNumber) {
            return $product->product_number === $productNumber;
        }));

        unset($dataResults->results[0]->products);

        $dataResults->results[0]->products = $result;

    }



    /**
     * Fetches, caches, and returns the API Result using the Query string supplied.
     * @param string $cacheKey <p>md5 Hashed query string for storing results in cache.</p>
     * @param string $query <p>Query String for API call.</p>
     * @return object <p>API Result decoded from Json.</p>
     */
    private function getResultsFromApiAndCache(string $cacheKey, string $query): object{

        return Cache::remember($cacheKey, 3600, function () use ($query) {
            return json_decode(Http::get($query)->getBody());

        });
    }

}
