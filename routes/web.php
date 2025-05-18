<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PationsController;
use App\Http\Controllers\SalatController;
use App\Http\Controllers\SurgeryController;
use App\Http\Controllers\HwadthController;
use App\Http\Controllers\ServiceSpecializationController;
use App\Http\Controllers\PationController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrivateWingController;
use App\Http\Controllers\TypeSpecializationController;

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
Route::get('/register', function () {
    $mohs = App\Models\mohs::all();
    $fctypes = App\Models\Fctypes::all();
    $fck = App\Models\fck::all();

    return view('auth.register', compact('mohs', 'fctypes', 'fck'));
})->name('register');
Route::post('/getInstitutions', [RegisterController::class, 'getInstitutions'])->name('getInstitutions');
Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');
Route::resource('mohs', 'App\Http\Controllers\MohsController');
Route::resource('Fctypes', 'App\Http\Controllers\FctypesController');
Route::resource('pations', 'App\Http\Controllers\PationsController');
Route::get('/edit_pations/{id}', 'App\Http\Controllers\PationsController@edit');

Route::put('pations/update', [PationsController::class, 'update'])->name('Pations.update');
Route::get('pations_nonapprove', 'App\Http\Controllers\PationsController@pations_nonapprove');
Route::get('pations_approve', 'App\Http\Controllers\PationsController@pations_approve');
Route::get('/show_status/{id}', 'App\Http\Controllers\PationsController@show1')->name('status_show');
Route::put('/status_update/{id}', 'App\Http\Controllers\PationsController@status_update')->name('status_update');
Route::resource('rdhs', 'App\Http\Controllers\RdhsController');
Route::resource('Fcks', 'App\Http\Controllers\FckController');
Route::resource('surgery', 'App\Http\Controllers\SurgeryController');
Route::get('/surgery', [SurgeryController::class, 'index'])->name('surgery.surgery');
Route::get('/edit_surgery/{id}', 'App\Http\Controllers\SurgeryController@edit');
Route::put('surgery/update', [SurgeryController::class, 'update'])->name('surgery.update');
Route::resource('salat', 'App\Http\Controllers\SalatController');
Route::get('/edit_salat/{id}', 'App\Http\Controllers\SalatController@edit');
Route::put('salat/update', [SalatController::class, 'update'])->name('salat.update');
Route::resource('surgs', 'App\Http\Controllers\SalatController');
Route::get('/report', 'App\Http\Controllers\PationsController@print_pations');
Route::resource('hwadths', HwadthController::class);
use App\Http\Controllers\CaseController;

Route::resource('cases', CaseController::class);
Route::get('/cases/getBedsTotal/{month}/{year}', [CaseController::class, 'getBedsTotal']);
// في routes/web.php
Route::post('/print_pations', [PationsController::class, 'print_pations'])->name('print_pations');

use App\Http\Controllers\UserController;

use App\Http\Controllers\RdhsController;

use App\Http\Controllers\BackupController;

Route::get('/getHallsByRdh/{rdhId}', [RdhsController::class, 'getHallsByRdh']);

Route::post('/home', [HomeController::class, 'index'])->name('index');

use App\Http\Controllers\PDFController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\ServiceController;
use App\Models\fck;

// Service-Specialization routes
Route::group(['middleware' => ['auth']], function () {
    Route::get('service-specializations', [ServiceSpecializationController::class, 'index'])->name('service-specializations.index');
    Route::get('service-specializations/create', [ServiceSpecializationController::class, 'create'])->name('service-specializations.create');
    Route::post('service-specializations', [ServiceSpecializationController::class, 'store'])->name('service-specializations.store');
    Route::get('service-specializations/{service}/{specialization}/edit', [ServiceSpecializationController::class, 'edit'])->name('service-specializations.edit');
    Route::put('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'update'])->name('service-specializations.update');
    Route::delete('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'destroy'])->name('service-specializations.destroy');
});
Route::get('/getCalendarData', function () {
    $selectedYear = request('year');

    $data = DB::table('fcks')
        ->leftJoin('pations', function ($join) use ($selectedYear) {
            $join->on('fcks.id', '=', 'pations.fck_id')
                 ->where('pations.year', '=', $selectedYear);
        })
        ->select('fcks.Fckname', 'pations.month', 'pations.id AS entry_exists')
        ->orderBy('pations.month')
        ->get();

    return response()->json($data);
});
Route::get('/backup', [BackupController::class, 'backupForUser'])->middleware('auth');
Route::get('/recordindex', [RecordController::class, 'recordindex'])->name('record.recordindex');
Route::get('/download/{file}', [RecordController::class, 'download'])->name('download.file');
Route::get('/delete/{file}', [RecordController::class, 'delete'])->name('delete.file');
Route::get('/view-file/{file}', [RecordController::class, 'viewFile'])->name('view.file');
Route::middleware('auth')->group(function () {
    Route::get('/record', [RecordController::class, 'index'])->name('record.index');
    Route::get('/filter-institutions', [RecordController::class, 'filterInstitutions']);
    Route::post('/record', [RecordController::class, 'store'])->name('record.store');
});
Route::get('/users/{id}/change-password', [UserController::class, 'showChangePasswordForm'])->name('users.change-password-form');
Route::patch('/users/{id}/change-password', [UserController::class, 'changePassword'])->name('users.change-password');
Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', 'App\Http\Controllers\RoleController');
    Route::resource('users', 'App\Http\Controllers\UserController');
    Route::resource('private-wings', PrivateWingController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('type-specializations', TypeSpecializationController::class);
    
    // Service Specialization routes
    Route::get('service-specializations', [ServiceSpecializationController::class, 'index'])->name('service-specializations.index');
    Route::get('service-specializations/create', [ServiceSpecializationController::class, 'create'])->name('service-specializations.create');
    Route::post('service-specializations', [ServiceSpecializationController::class, 'store'])->name('service-specializations.store');
    Route::get('service-specializations/{service}/{specialization}/edit', [ServiceSpecializationController::class, 'edit'])->name('service-specializations.edit');
    Route::put('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'update'])->name('service-specializations.update');
    Route::delete('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'destroy'])->name('service-specializations.destroy');
});
// مسار تصدير البيانات
Route::get('/export-pations', [PationController::class, 'export'])->name('export.pations');
