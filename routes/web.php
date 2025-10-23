<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlotController;

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

Route::get('/', function () {
    return view('page-parser');
})->name('tableParser.parsePage');


Route::post('/parse-page', [PlotController::class, 'plotGraph'])
    ->name('tableParser.plotGraph');

Route::get('/plot-result', [PlotController::class, 'showResult'])
    ->name('plot.result');

Route::get('/download-plot/{filename}', [PlotController::class, 'downloadPlot'])
    ->name('plot.download');
