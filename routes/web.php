<?php

use App\Http\Controllers\DrugController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DrugController::class, 'index']);


//routes to show drug, less descriptive uri with only ndc
// Route::get('/drugs/{sponsor_name}/{brand_name}/{product_ndc}', [DrugController::class, 'showDrug']);
Route::get('/drugs/{application_num}/{product_num}', [DrugController::class, 'showDrug']);

Route::post('/drugs', [DrugController::class, 'showDrugsSearch']);

Route::fallback([DrugController::class, 'trySearchingForMedication']);

// Route::get('/drugs/search-results', [DrugController::class, 'showDrugSearchResults']);
