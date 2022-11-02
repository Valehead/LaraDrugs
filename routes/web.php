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


Route::post('/drugs', [DrugController::class, 'getDrugs']);

// Route::get('/drugs/search-results', [DrugController::class, 'showDrugSearchResults']);
