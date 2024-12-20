<?php
  
  use App\Http\Controllers\Auth\RegisterController;
  use App\Http\Controllers\HomeController;
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

Route::get('/', function () {
    return view('auth.login');
});



Auth::routes();


Route::middleware(['2fa'])->group(function () {
   
    Route::get('/home', [HomeController::class, 'index'])->name('home');
   
    Route::post('/2fa', function () {
        return redirect(route('home'));
    })->name('2fa');
});
  
Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');
Route::resource('mohs','App\Http\Controllers\MohsController');
Route::resource('Fctypes','App\Http\Controllers\FctypesController');
Route::resource('pations','App\Http\Controllers\PationsController');
Route::resource('rdhs','App\Http\Controllers\RdhsController');
Route::resource('fcks','App\Http\Controllers\FCKController');