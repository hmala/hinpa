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
use App\Http\Controllers\PrivateWingDetailsController;
use App\Http\Controllers\PrivateVisitController;
use App\Http\Controllers\PrivateVisitDetailsController;

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
Route::get('/no-permissions', function() {
    return view('auth.no-permissions');
})->name('no-permissions');



// تعطيل route التسجيل العام - المستخدمون يضافون من لوحة التحكم فقط
Auth::routes(['register' => false]);


Route::middleware(['2fa'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::post('/2fa', function () {
        return redirect(route('home'));
    })->name('2fa');
});

// تم تعطيل التسجيل العام - يتم إضافة المستخدمين من صفحة المستخدمين الإدارية فقط
// Route::get('/register', function () {
//     $mohs = App\Models\mohs::all();
//     $fctypes = App\Models\Fctypes::all();
//     $fck = App\Models\fck::all();
//
//     return view('auth.register', compact('mohs', 'fctypes', 'fck'));
// })->name('register');
// Route::post('/getInstitutionsForRegister', [RegisterController::class, 'getInstitutions'])->name('getInstitutionsForRegister');
// Route::get('/complete-registration', [RegisterController::class, 'completeRegistration'])->name('complete.registration');

Route::resource('mohs', 'App\Http\Controllers\MohsController');
Route::resource('Fctypes', 'App\Http\Controllers\FctypesController');
Route::resource('pations', 'App\Http\Controllers\PationsController');
Route::get('pations-data', [PationsController::class, 'getPationsData'])->name('pations.data');
Route::get('pations-nonapprove-data', [PationsController::class, 'getPationsNonApproveData'])->name('pations.nonapprove.data');
Route::get('pations-approve-data', [PationsController::class, 'getPationsApproveData'])->name('pations.approve.data');
Route::get('/edit_pations/{id}', 'App\Http\Controllers\PationsController@edit');
Route::get('/export/pations', [PationsController::class, 'export'])->name('export.pations');

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
Route::get('/institutions-by-hall-type', [App\Http\Controllers\InstitutionReportController::class, 'institutionsByHallType'])->name('institutions.by.hall.type')->middleware('auth');
Route::get('/institutions-by-hall-type/details/{hallTypeId}', [App\Http\Controllers\InstitutionReportController::class, 'getInstitutionsByHallType'])->name('institutions.by.hall.type.details');
Route::get('/institutions-by-type-department', [App\Http\Controllers\InstitutionReportController::class, 'institutionsByTypeAndDepartment'])->name('institutions.by.type.department')->middleware('auth');
Route::get('/institutions-by-type-department/details', [App\Http\Controllers\InstitutionReportController::class, 'getInstitutionsByTypeDepartment'])->name('institutions.by.type.department.details');
Route::get('/institutions-without-users', [App\Http\Controllers\InstitutionReportController::class, 'getInstitutionsWithoutUsers'])->name('institutions.without.users');
Route::get('/test-route', function() { return response()->json(['status' => 'ok']); });
Route::resource('hwadths', HwadthController::class);
use App\Http\Controllers\CaseController;

Route::resource('cases', CaseController::class);
Route::get('/cases/getBedsTotal/{month}/{year}', [CaseController::class, 'getBedsTotal']);
// في routes/web.php
Route::post('/print_pations', [PationsController::class, 'print_pations'])->name('print_pations');
Route::get('/export/pations', [PationController::class, 'export'])->name('export.pations');

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
    Route::get('service-specializations/import', [ServiceSpecializationController::class, 'showImportForm'])->name('service-specializations.import.form');
    Route::post('service-specializations/import', [ServiceSpecializationController::class, 'import'])->name('service-specializations.import');
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
Route::get('/users-export', [UserController::class, 'export'])->name('users.export');

// QR Code route - بدون middleware للوصول من الموبايل
Route::get('private-wings/{wing}/qr', [PrivateWingController::class, 'qrView'])->name('private-wings.qr');

Route::group(['middleware' => ['auth']], function () {
    Route::resource('roles', 'App\Http\Controllers\RoleController');
    Route::resource('users', 'App\Http\Controllers\UserController');
    // Private Wing trashed routes
    Route::get('private-wings/trashed', [PrivateWingController::class, 'trashed'])->name('private-wings.trashed');
    Route::get('private-wings/get-next-numbers', [PrivateWingController::class, 'getNextNumbers'])->name('private-wings.get-next-numbers');
    Route::get('private-wings/export', [PrivateWingController::class, 'export'])->name('private-wings.export');
    Route::get('invoice/private-wing/{wing}', [PrivateWingController::class, 'invoice'])->name('private-wings.invoice');
    Route::get('private-wings/{wing}/show', [PrivateWingController::class, 'show'])->name('private-wings.show');
    Route::patch('private-wings/cancel/{id}', [PrivateWingController::class, 'cancel'])->name('private-wings.cancel');
    Route::post('private-wings/{wing}/refund', [PrivateWingController::class, 'addRefund'])->name('private-wings.add-refund');
    Route::delete('private-wings/refund/{refund}', [PrivateWingController::class, 'deleteRefund'])->name('private-wings.delete-refund');
    Route::get('private-wings/{wing}/refund-receipt', [PrivateWingController::class, 'refundReceipt'])->name('private-wings.refund-receipt');
    Route::resource('private-wings', PrivateWingController::class);

    // Private Visit routes
    Route::get('private-visits/trashed', [PrivateVisitController::class, 'trashed'])->name('private-visits.trashed');
    Route::get('private-visits/get-next-numbers', [PrivateVisitController::class, 'getNextNumbers'])->name('private-visits.get-next-numbers');
    Route::get('private-visits/export', [PrivateVisitController::class, 'export'])->name('private-visits.export');
    Route::get('invoice/private-visit/{visit}', [PrivateVisitController::class, 'invoice'])->name('private-visits.invoice');
    Route::get('private-visits/{visit}/show', [PrivateVisitController::class, 'show'])->name('private-visits.show');
    Route::patch('private-visits/cancel/{id}', [PrivateVisitController::class, 'cancel'])->name('private-visits.cancel');
    Route::post('private-visits/{visit}/refund', [PrivateVisitController::class, 'addRefund'])->name('private-visits.add-refund');
    Route::delete('private-visits/refund/{refund}', [PrivateVisitController::class, 'deleteRefund'])->name('private-visits.delete-refund');
    Route::get('private-visits/{visit}/refund-receipt', [PrivateVisitController::class, 'refundReceipt'])->name('private-visits.refund-receipt');
    Route::get('private-visits/getSpecializations', [PrivateVisitController::class, 'getSpecializations'])->name('private-visits.get-specializations');
    Route::get('private-visits/getServiceSpecializations', [PrivateVisitController::class, 'getServiceSpecializations'])->name('private-visits.get-service-specializations');
    Route::resource('private-visits', PrivateVisitController::class);
    Route::resource('services', ServiceController::class);
    Route::resource('type-specializations', TypeSpecializationController::class);
    
    // Service Specialization routes
    Route::get('service-specializations', [ServiceSpecializationController::class, 'index'])->name('service-specializations.index');
    Route::get('service-specializations/create', [ServiceSpecializationController::class, 'create'])->name('service-specializations.create');
    Route::post('service-specializations', [ServiceSpecializationController::class, 'store'])->name('service-specializations.store');
    Route::get('service-specializations/{service}/{specialization}/edit', [ServiceSpecializationController::class, 'edit'])->name('service-specializations.edit');
    Route::put('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'update'])->name('service-specializations.update');
    Route::delete('service-specializations/{service}/{specialization}', [ServiceSpecializationController::class, 'destroy'])->name('service-specializations.destroy');
    
    // Private Wing routes

});

// جعل route جلب المؤسسات متاح للجميع مؤقتًا للاختبار
Route::post('/getInstitutions', [PrivateWingController::class, 'getInstitutions'])->name('getInstitutions');

Route::group(['middleware' => ['auth']], function () {
    Route::post('/getSpecializations', [PrivateWingController::class, 'getSpecializations'])->name('getSpecializations');
    Route::post('/getServicePrice', [PrivateWingController::class, 'getServicePrice'])->name('getServicePrice');    // إضافة مسار جديد لجلب الخدمات الخاصة بالتخصص الفرعي
    Route::post('/getServiceSpecializations', [PrivateWingController::class, 'getServiceSpecializations'])
        ->name('getServiceSpecializations');    
    // Backup routes
    Route::get('backup', [App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
    Route::get('backup/test', function() {
        return view('backup.test');
    })->name('backup.test');
    Route::post('backup/create', [App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
    Route::get('backup/download/{filename}', [App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
    Route::delete('backup/delete/{filename}', [App\Http\Controllers\BackupController::class, 'delete'])->name('backup.delete');
    
    // Export routes
    Route::get('export', [App\Http\Controllers\ExportController::class, 'index'])->name('export.index');
    Route::post('export/columns', [App\Http\Controllers\ExportController::class, 'getTableColumns'])->name('export.columns');
    Route::post('export/preview', [App\Http\Controllers\ExportController::class, 'preview'])->name('export.preview');
    Route::post('export/download', [App\Http\Controllers\ExportController::class, 'export'])->name('export.download');
    
    // Statistics routes
    Route::get('statistics', [App\Http\Controllers\StatisticsController::class, 'index'])->name('statistics.index');
    Route::get('statistics/comparison', [App\Http\Controllers\StatisticsController::class, 'getComparisonData'])->name('statistics.comparison');
    Route::get('statistics/export', [App\Http\Controllers\StatisticsController::class, 'exportStatistics'])->name('statistics.export');
    Route::get('statistics/institutions/{moh_id}', [App\Http\Controllers\StatisticsController::class, 'getInstitutionsByMoh'])->name('statistics.institutions');

    // Doctors routes
    Route::resource('doctors', App\Http\Controllers\DoctorController::class);

    // Private Wing Details routes
    Route::get('private-wing-details', [PrivateWingDetailsController::class, 'index'])->name('private-wing-details.index');
    Route::get('private-wing-details/export', [PrivateWingDetailsController::class, 'export'])->name('private-wing-details.export');
    Route::get('private-visit-details', [PrivateVisitDetailsController::class, 'index'])->name('private-visit-details.index');
    Route::get('private-visit-details/export', [PrivateVisitDetailsController::class, 'export'])->name('private-visit-details.export');

    // Operation Fees routes
    Route::get('operation-fees', [App\Http\Controllers\OperationFeesController::class, 'index'])->name('operation-fees.index');
    Route::get('operation-fees/export', [App\Http\Controllers\OperationFeesController::class, 'export'])->name('operation-fees.export');

    // Receipts routes
    Route::get('receipts', [App\Http\Controllers\ReceiptsController::class, 'index'])->name('receipts.index');
    Route::get('receipts/{id}', [App\Http\Controllers\ReceiptsController::class, 'show'])->name('receipts.show');
    Route::get('receipts-export', [App\Http\Controllers\ReceiptsController::class, 'export'])->name('receipts.export');

    // Invoices routes
    Route::get('invoices', [App\Http\Controllers\InvoicesController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{id}', [App\Http\Controllers\InvoicesController::class, 'show'])->name('invoices.show');
    Route::get('invoices-export', [App\Http\Controllers\InvoicesController::class, 'export'])->name('invoices.export');

});


