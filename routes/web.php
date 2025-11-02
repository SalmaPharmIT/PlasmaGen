<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntityController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BloodBankController;
use App\Http\Controllers\CityMasterController;
use App\Http\Controllers\TourPlannerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\ReportVisitsController;
use App\Http\Controllers\ReportsMasterController;
use App\Http\Controllers\ExpensesController;
use App\Http\Controllers\BagEntryController;
use App\Http\Controllers\ELISAReportController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\NATReportController;
use App\Http\Controllers\PlasmaController;
use App\Http\Controllers\ReportMiniPoolMegaPoolController;
use App\Http\Controllers\SubMiniPoolEntryController;
use App\Http\Controllers\PlasmaManagementController;
use App\Http\Controllers\BagStatusController;
use App\Http\Controllers\PlasmaRejectionController;
use App\Http\Controllers\PrinterLabelController;
use App\Http\Controllers\AuditTrailController;
use Illuminate\Http\Request;
// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
})->name('laravel.home');

// Login Routes
Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'loginPost'])->name('login.post');

// Dashboard Route (Protected)
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// Logout Route
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');


// Entity Registration Routes (Protected by 'auth' middleware)
Route::group(['middleware' => ['auth']], function () {
    Route::get('/register/entity', [EntityController::class, 'showRegistrationForm'])->name('entity.register');
    Route::post('/register/entity', [EntityController::class, 'register'])->name('entity.register.submit');

    /* *********************  Entity Starts ********************************* */
    // View Entities Page
    Route::get('/entities', [EntityController::class, 'index'])->name('entities.index');

    // API Endpoint to Fetch Entities
    Route::get('/api/entities', [EntityController::class, 'getEntities'])->name('api.entities');

    // Edit Entity starts ***********
    // Route to display the edit form - view
    Route::get('/entity/{id}', [EntityController::class, 'edit'])->name('entity.edit');

    // Route to handle the update request - update
    Route::put('/entity/{id}', [EntityController::class, 'update'])->name('entity.update');
    // Edit Entity ends ***********

    Route::get('/entities/parent-entities', [EntityController::class, 'getParentEntities'])->name('entities.getParentEntities');

    Route::get('/entities/settings', [EntityController::class, 'settings'])->name('entities.settings');

    Route::put('/entity/settings/save', [EntityController::class, 'saveSettings'])->name('entity.settings.save');

    // Route::post('/entities/update-name', [EntityController::class, 'updateName'])->name('entities.updateName');
    // Route::post('/entities/update-code', [EntityController::class, 'updateCode'])->name('entities.updateCode');
    // Route::post('/entities/update-contact-person', [EntityController::class, 'updateContactPerson'])->name('entities.updateContactPerson');
    // Route::post('/entities/update-contact-number', [EntityController::class, 'updateContactNumber'])->name('entities.updateContactNumber');
    // Route::post('/entities/update-email', [EntityController::class, 'updateEmail'])->name('entities.updateEmail');
    // Route::post('/entities/update-address', [EntityController::class, 'updateAddress'])->name('entities.updateAddress');

    /* *********************  Entity Ends ********************************* */


    /* *********************  Users Starts ********************************* */
    // View Entities Page
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    //Fetch Users list api
    Route::get('/api/users', [UserController::class, 'getUsers'])->name('api.users');

    Route::get('/register/user', [UserController::class, 'showUserRegistrationForm'])->name('user.register');
    Route::post('/register/user', [UserController::class, 'register'])->name('user.register.submit');
    // Route to display the edit form - view
    Route::get('/user/{id}', [UserController::class, 'edit'])->name('user.edit');
    // Route to handle the update request - update
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
    /* *********************  Users Ends ********************************* */

    /* *********************  Settings Starts ********************************* */
    Route::get('/api/states/{countryId}', [UserController::class, 'getStatesById'])->name('api.states');
    Route::get('/api/citiesById/{stateId}', [UserController::class, 'getCitiesById'])->name('api.citiesById');
    Route::get('/api/citiesByStateIds/{stateId}', [UserController::class, 'getCitiesByMultipleStateId'])->name('api.citiesByStateIds');
    /* *********************   Settings Ends ********************************* */

    /* *********************  Blood Bank Starts ********************************* */
    Route::get('/bloodbank', [BloodBankController::class, 'index'])->name('bloodbank.index');
    Route::get('/api/bloodbanks', [BloodBankController::class, 'getBloodBanks'])->name('api.bloodbanks');   // API Endpoint to Fetch Entities
    Route::get('/register/bloodbank', [BloodBankController::class, 'showRegistrationForm'])->name('bloodbank.register');
    Route::post('/register/bloodbank', [BloodBankController::class, 'register'])->name('bloodbank.register.submit');
    Route::get('/bloodbank/{id}', [BloodBankController::class, 'edit'])->name('bloodbank.edit');  // Route to display the edit form - view
    Route::put('/bloodbank/{id}', [BloodBankController::class, 'update'])->name('bloodbank.update');   // Route to handle the update request - update
    /* *********************  Blood Bank Ends ********************************* */

    /* *********************  Warehouse Starts ********************************* */
    Route::get('/warehouse', [WarehouseController::class, 'index'])->name('warehouse.index');
    Route::get('/api/warehouses', [WarehouseController::class, 'getWarehouses'])->name('api.warehouses');   // API Endpoint to Fetch Entities
    Route::get('/register/warehouse', [WarehouseController::class, 'showRegistrationForm'])->name('warehouse.register');
    Route::post('/register/warehouse', [WarehouseController::class, 'register'])->name('warehouse.register.submit');
    Route::get('/warehouse/{id}', [WarehouseController::class, 'edit'])->name('warehouse.edit');  // Route to display the edit form - view
    Route::put('/warehouse/{id}', [WarehouseController::class, 'update'])->name('warehouse.update');   // Route to handle the update request - update
    /* *********************  Warehouse Ends ********************************* */

     /* *********************  Masters for Admin Starts ********************************* */
    // View Entities Page
    Route::get('/citymaster', [CityMasterController::class, 'index'])->name('citymaster.index');
    Route::get('/api/cities', [CityMasterController::class, 'getCities'])->name('api.cities');
    Route::post('/citymaster', [CityMasterController::class, 'store'])->name('citymaster.store');
    Route::put('/citymaster/{id}', [CityMasterController::class, 'update'])->name('citymaster.update');
    Route::delete('/citymaster/{id}', [CityMasterController::class, 'destroy'])->name('citymaster.destroy');

    Route::get('/entities/features', [EntityController::class, 'entityFeatures'])->name('entities.features');
    Route::put('/entities/features/update', [EntityController::class, 'updateFeatureSettings'])->name('entities.features.update');

    Route::get('/users/reportMapping', [UserController::class, 'showUserReportMapping'])->name('users.reportMapping');
    Route::get('/get-user-report-mapping', [UserController::class, 'getUserReportMapping'])->name('getUserReportMapping');
    Route::get('/get-employee-by-role/{roleId}', [UserController::class, 'getEmployeeByRoleId'])->name('getEmployeeByRoleId');
    Route::get('/get-roles-by-hierarchy/{roleId}', [UserController::class, 'getRoleByDownwardHierarchy'])->name('getRoleByDownwardHierarchy');
    Route::post('/users/reportMapping/submit', [UserController::class, 'submitUserReportMapping'])->name('users.reportMapping.submit');
    Route::post('/users/reportMapping/edit', [UserController::class, 'editUserReportMapping'])->name('users.reportMapping.edit');
    Route::post('/users/reportMapping/delete', [UserController::class, 'deleteUserReportMapping'])->name('users.reportMapping.delete');

    Route::get('/users/workLocationMapping', [UserController::class, 'showWorkLocationMapping'])->name('users.workLocationMapping');
    Route::post('/users/workLocationMapping/submit', [UserController::class, 'submitWorkLocationMapping'])->name('users.workLocationMapping.submit');
    Route::get('/get-user-work-location-mapping', [UserController::class, 'getUserWorkLocationMapping'])->name('getUserWorkLocationMapping');
    Route::post('/users/workLocationMapping/editSubmit', [UserController::class, 'submitWorkLocationMappingEdit'])->name('users.workLocationMapping.editSubmit');
    Route::post('/users/workLocationMapping/delete', [UserController::class, 'deleteWorkLocationMapping'])->name('users.workLocationMapping.delete');
    Route::get('/users/workLocationMapping/bloodbanks/{cityIds}', [UserController::class, 'getBloodbanksByCity'])->name('users.workLocationMapping.bloodbanks');
    /* *********************   Masters for Admin Ends ********************************* */


    /* *********************  Tour Planner Starts ********************************* */
    Route::get('/tourplanner', [TourPlannerController::class, 'index'])->name('tourplanner.index');
    Route::get('/tourplanner/getCollectingAgents', [TourPlannerController::class, 'getCollectingAgents'])->name('tourplanner.getCollectingAgents');     //Fetch Collecting Agents Users list api
    Route::get('/tourplanner/getBloodBanks', [TourPlannerController::class, 'getBloodBanks'])->name('tourplanner.getBloodBanks');
    Route::get('/tourplanner/getEmployeesBloodBanks', [TourPlannerController::class, 'getEmployeesBloodBanks'])->name('tourplanner.getEmployeesBloodBanks');
    Route::get('/tourplanner/getEmployeesCities', [TourPlannerController::class, 'getEmployeeCities'])->name('tourplanner.getEmployeesCities');
    Route::get('/tourplanner/getPendingDocuments', [TourPlannerController::class, 'getPendingDocuments'])->name('tourplanner.getPendingDocuments');
    // New route for fetching calendar events
    Route::get('/tourplanner/getCalendarEvents', [TourPlannerController::class, 'getCalendarEvents'])->name('tourplanner.getCalendarEvents');
    Route::post('/tourplanner/saveTourPlan', [TourPlannerController::class, 'saveTourPlan'])->name('tourplanner.saveTourPlan');
    Route::delete('/tourplanner/deleteTourPlan', [TourPlannerController::class, 'deleteTourPlan'])->name('tourplanner.deleteTourPlan');

    Route::get('/tourplanner/sourcingCreateTourPlan', [TourPlannerController::class, 'showSourcingCreateTourPlan'])->name('tourplanner.sourcingCreateTourPlan');
    Route::post('/tourplanner/submit_monthly_tour_plan', [TourPlannerController::class, 'submitMonthlyTourPlan'])->name('tourplanner.submitMonthlyTourPlan');
    Route::post('/tourplanner/submit_edit_request', [TourPlannerController::class, 'submitEditRequest'])->name('tourplanner.submitEditRequest');
    Route::post('/tourplanner/requestCollection', [TourPlannerController::class, 'requestCollection'])->name('tourplanner.requestCollection');
    Route::get('/tourplanner/collectionIncomingRequests', [TourPlannerController::class, 'showCollectionIncomingRequests'])->name('tourplanner.collectionIncomingRequests');
    Route::get('/tourplanner/tpCollectionRequests', [TourPlannerController::class, 'getTPCollectionIncomingRequests'])->name('tourplanner.tpCollectionRequests');
    Route::get('/tourplanner/markTPAdded', [TourPlannerController::class, 'markTPAdded'])->name('tourplanner.markTPAdded');
    Route::get('/tourplanner/getAllActiveWarehouses', [TourPlannerController::class, 'getAllActiveWarehouses'])->name('tourplanner.getAllActiveWarehouses');  // Get All Acive Warehouses
    Route::get('/tourplanner/getAllActiveTransportPartners', [TourPlannerController::class, 'getAllActiveTransportPartners'])->name('tourplanner.getAllActiveTransportPartners');
    /* *********************  Tour Planner Ends ********************************* */

    /* *********************  Manage Tour Planner Starts ********************************* */
    Route::get('/tourplanner/manage', [TourPlannerController::class, 'manage'])->name('tourplanner.manage');
    // Route::get('/tourplanner/dcr', [TourPlannerController::class, 'dcrRequests'])->name('tourplanner.dcr');
    Route::get('/tourplanner/getDCRApprovals', [TourPlannerController::class, 'getDCRApprovals'])->name('tourplanner.getDCRApprovals');
    Route::get('/tourplanner/dcr-details/{id}', [TourPlannerController::class, 'showDCRDetails'])->name('tourplanner.dcr-details');
    Route::post('/tourplanner/dcr/{id}/update-status', [TourPlannerController::class, 'updateStatus'])->name('tourplanner.dcr.updateStatus');

    Route::get('/tourplanner/finalDCR', [TourPlannerController::class, 'finalDCRRequests'])->name('tourplanner.finalDCR');
    Route::get('/tourplanner/getFinalDCRApprovals', [TourPlannerController::class, 'getFinalDCRApprovals'])->name('tourplanner.getFinalDCRApprovals');
    Route::get('/tourplanner/dcrVisit-details/{id}', [TourPlannerController::class, 'showFinalDCRVisitDetails'])->name('tourplanner.dcrVisit-details');
    Route::get('/tourplanner/dcrVisits', [TourPlannerController::class, 'dcrVisits'])->name('tourplanner.dcrVisits');
    Route::get('/tourplanner/dcrStatus', [TourPlannerController::class, 'dcrStatusFetch'])->name('tourplanner.dcrStatus');
    /* *********************  Manage Tour Planner Ends ********************************* */

    /* *********************  Collections Starts ********************************* */
    Route::get('/collectionrequests', [TourPlannerController::class, 'collectionrequests'])->name('collectionrequest.index');
    Route::get('/collections/requests', [TourPlannerController::class, 'getCollectionRequests'])->name('collections.requests');
    Route::post('/collections/vehicle-details', [TourPlannerController::class, 'submitVehicleDetails'])->name('collections.submitVehicleDetails');
    Route::get('/collections/manage', [TourPlannerController::class, 'collectionsManage'])->name('collections.manage');
    Route::get('/collections/submitted', [TourPlannerController::class, 'getCollectionSubmitted'])->name('collections.submitted');
    Route::post('/collections/vehicle-updateVehicleDetails', [TourPlannerController::class, 'updateVehicleDetails'])->name('collections.updateVehicleDetails');
    /* *********************  Collections Ends ********************************* */


    Route::get('/dashboard/getDashboardData', [DashboardController::class, 'getDashboardData'])->name('dashboard.getDashboardData');
    Route::get('/dashboard/getFactoryDashboardData', [DashboardController::class, 'getFactoryDashboardData'])->name('dashboard.getFactoryDashboardData');
    Route::get('/dashboard/getDashboardGraphData', [DashboardController::class, 'getDashboardGraphData'])->name('dashboard.getDashboardGraphData');
    Route::get('/dashboard/getDashboardBloodBanksMapData', [DashboardController::class, 'getDashboardBloodBanksMapData'])->name('dashboard.getDashboardBloodBanksMapData');

    /* *********************  Report Visits Starts ********************************* */
    Route::get('/visits', [ReportVisitsController::class, 'index'])->name('visits.index');
    Route::get('/visits/view/{date}', [ReportVisitsController::class, 'showView'])->name('visits.view');
    Route::get('/api/visits/{date}', [ReportVisitsController::class, 'fetchVisits'])->name('visits.fetch');
    Route::get('/visits/getFeatureSettings', [ReportVisitsController::class, 'entityFeatures'])->name('visits.getFeatureSettings');
    Route::post('/visits/custom_update', [ReportVisitsController::class, 'updateVisit'])->name('visits.custom_update');
    Route::post('/visits/custom_sourcing_update', [ReportVisitsController::class, 'sourcingDCRSubmit'])->name('visits.custom_sourcing_update');
    Route::post('/visits/finalDCRsubmit', [ReportVisitsController::class, 'finalDCRsubmit'])->name('visits.finalDCRsubmit');
    Route::get('/visits/getEmployeesTPStatus', [ReportVisitsController::class, 'getEmployeesTPStatus'])->name('visits.getEmployeesTPStatus');
    Route::get('/visits/getCoreSourcingBloodBanks', [ReportVisitsController::class, 'getCoreSourcingBloodBanks'])->name('visits.getCoreSourcingBloodBanks');
    Route::get('/visits/getSourcingGSTRates', [ReportVisitsController::class, 'getSourcingGSTRates'])->name('visits.getSourcingGSTRates');
    Route::post('/visits/collection_edit_submit', [ReportVisitsController::class, 'collectionEditSubmit'])->name('visits.collection_edit_submit');
    Route::post('/visits/sourcing_edit_submit', [ReportVisitsController::class, 'sourcingEditSubmit'])->name('visits.sourcing_edit_submit');
    /* *********************  Report Visits Ends ********************************* */


    /* *********************  Reports for Admin Starts ********************************* */
    Route::get('/reports', [ReportsMasterController::class, 'index'])->name('reports.reports_work_summary');
    Route::post('/reports/getPeriodicWorkSummary', [ReportsMasterController::class, 'getPeriodicWorkSummaryData'])->name('reports.getPeriodicWorkSummary');
    Route::get('/reports/blood_banks', [ReportsMasterController::class, 'bloodbankSummaryIndex'])->name('reports.blood_banks_summary');
    Route::post('/reports/getBloodBankSummary', [ReportsMasterController::class, 'getBloodBankSummaryData'])->name('reports.getBloodBankSummary');
    Route::get('/reports/userWiseCollectionSummary', [ReportsMasterController::class, 'userWiseCollectionSummaryIndex'])->name('reports.user_wise_collection_summary');
    Route::post('/reports/getUserWiseColllectionSummary', [ReportsMasterController::class, 'getUserWiseColllectionSummaryData'])->name('reports.getUserWiseColllectionSummary');
    Route::get('/reports/bloodbankWiseCollectionSummary', [ReportsMasterController::class, 'bloodBankWiseCollectionSummaryIndex'])->name('reports.bloodbank_wise_collection_summary');
    Route::post('/reports/getBloodBankWiseColllectionSummary', [ReportsMasterController::class, 'getBloodBankWiseColllectionSummaryData'])->name('reports.getBloodBankWiseColllectionSummary');
    Route::get('/reports/tourPlanDateWiseSummary', [ReportsMasterController::class, 'tourPlanDateWiseSummaryIndex'])->name('reports.tour_palnner_datewise_summary');
    Route::post('/reports/getTourPlannerDateWiseSummary', [ReportsMasterController::class, 'getTourPlannerDateWiseSummaryData'])->name('reports.getTourPlannerDateWiseSummary');
    Route::get('/reports/userExpensesSummary', [ReportsMasterController::class, 'userExpensesSummaryIndex'])->name('reports.user_expenses_summary');
    Route::post('/reports/getUserExpensesSummary', [ReportsMasterController::class, 'getUserExpensesSummaryData'])->name('reports.getUserExpensesSummary');
    Route::get('/reports/dcrSummary', [ReportsMasterController::class, 'dcrSummaryIndex'])->name('reports.dcr_summary');
    Route::post('/reports/getUserDCRSummary', [ReportsMasterController::class, 'getUserDCRSummaryData'])->name('reports.getUserDCRSummary');
    /* *********************  Report Visits Ends ********************************* */

    /* ********************* Report Upload Routes ********************************* */
    Route::get('/report/upload', [ELISAReportController::class, 'upload'])->name('report.upload')->middleware('auth');
    Route::post('/report/store', [ELISAReportController::class, 'store'])->name('report.store')->middleware('auth');
    Route::post('/report/save', [ELISAReportController::class, 'save'])->name('report.save');
    Route::post('/report/check-existing', [ELISAReportController::class, 'checkExisting'])->name('report.check-existing');
    /* ********************* Report Upload Routes Ends ********************************* */

    /* ********************* Barcode Generator Routes ********************************* */
    Route::middleware(['auth'])->group(function () {
        Route::get('/barcode/generate', [BarcodeController::class, 'generate'])->name('barcode.generate');
        Route::post('/barcode/generate/codes', [BarcodeController::class, 'generateCodes'])->name('barcode.generate.codes');
        Route::post('/barcode/save', [BarcodeController::class, 'saveBarcodes'])->name('barcode.save');
        Route::get('/barcode/ar-numbers', [BarcodeController::class, 'getArNumbers'])->name('barcode.ar-numbers');
        Route::post('/barcode/reprint', [BarcodeController::class, 'reprintBarcodes'])->name('barcode.reprint');
        Route::get('/barcode/mega-pools', [BarcodeController::class, 'getMegaPools'])->name('barcode.mega-pools');
        Route::get('/barcode/template', [BarcodeController::class, 'generateTemplate'])->name('barcode.template');

        // BarTender Integration Route
        Route::post('/bartender/print', [PrinterLabelController::class, 'printWithBarTender'])->name('bartender.print');

        // Download barcode data as CSV
        Route::post('/bartender/download-csv', [PrinterLabelController::class, 'downloadCsv'])->name('bartender.download-csv');
    });
    /* ********************* Barcode Generator Routes Ends ********************************* */

    // Mini Pool Numbers Route - Moved outside middleware group
    Route::get('/subminipool/get-mini-pool-numbers', [SubMiniPoolEntryController::class, 'getMiniPoolNumbers'])
        ->name('subminipool.get-mini-pool-numbers')
        ->middleware('auth');

    // Sub Mini Pool Numbers Route
    Route::get('/subminipool/get-sub-mini-pool-numbers', [SubMiniPoolEntryController::class, 'getSubMiniPoolNumbers'])
        ->name('subminipool.get-sub-mini-pool-numbers')
        ->middleware('auth');

    // Mini Pool Bag Details Route
    Route::get('/subminipool/get-mini-pool-bag-details', [SubMiniPoolEntryController::class, 'getMiniPoolBagDetails'])
        ->name('subminipool.get-mini-pool-bag-details')
        ->middleware('auth');

    /* ********************* Expenses Starts ********************************* */
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/getUpdatedVisits', [ExpensesController::class, 'getUpdatedVisits'])->name('expenses.getUpdatedVisits');
    Route::get('/expenses/view', [ExpensesController::class, 'showExpenseView'])->name('expenses.view');
    Route::get('/expenses/fetchVisits', [ExpensesController::class, 'fetchVisitsExpenses'])->name('expenses.fetchVisits');
    Route::post('/expenses/submit', [ExpensesController::class, 'submitExpenses'])->name('expenses.submit');
    Route::get('expenses/fetchExpenses/{tp_id}', [ExpensesController::class, 'fetchExpenses'])->name('expenses.fetchExpenses');
    Route::delete('/expenses/delete/{id}', [ExpensesController::class, 'deleteExpense'])->name('expenses.delete');
    /* *********************  Expenses Ends ********************************* */

    /* ********************* New Bag Entry Routes ********************************* */
    Route::get('/newBagEntry', [BagEntryController::class, 'index'])->name('newBag.index')->middleware('auth');
    Route::get('/factory/newbagentry/sub-mini-pool-bag-entry', [BagEntryController::class, 'subMiniPoolBagEntry'])->name('factory.newbagentry.sub_mini_pool_bag_entry')->middleware('auth');
    Route::get('/factory/newbagentry/nat_re_test_mega_pool_entry', [NATReportController::class, 'retestMegaPoolIndex'])->name('factory.newbagentry.nat_re_test_mega_pool_entry')->middleware('auth');
    Route::post('/newBagEntry', [BagEntryController::class, 'store'])->name('newBag.store')->middleware('auth');
    Route::get('/check-mega-pool/{megaPoolNo}', [BagEntryController::class, 'checkMegaPool'])->name('check.mega.pool')->middleware('auth');
    Route::get('/bagEntryReject', [BagEntryController::class, 'rejectPlasmaBagEntry'])->name('rejectPlasmaBagEntry')->middleware('auth');
    /* ********************* New Bag Entry Routes Ends ********************************* */

    // Plasma Management Routes
    Route::prefix('plasma')->name('plasma.')->group(function () {
        Route::get('/entry', [PlasmaController::class, 'plasmaEntry'])->name('entry');
        Route::post('/store', [PlasmaController::class, 'store'])->name('store');
        Route::get('/dispensing', [PlasmaController::class, 'dispensing'])->name('dispensing');
        Route::get('/dispensing/print', [PlasmaController::class, 'printDispensing'])->name('dispensing.print');
        Route::get('/rejection', [PlasmaController::class, 'rejectionList'])->name('rejection');
        Route::post('/dispensing/get-bag-status', [PlasmaController::class, 'getBagStatusDetails'])->name('dispensing.get-bag-status');
        Route::post('/rejection/get-bag-status', [PlasmaController::class, 'getBagStatusForRejection'])->name('rejection.get-bag-status');
        Route::get('/generate-ar-no', [PlasmaController::class, 'generateArNo'])->name('generate-ar-no');
        Route::post('/update-ar-no', [PlasmaController::class, 'updateArNo'])->name('update-ar-no');
        Route::get('/get-by-ar-no/{ar_no}', [PlasmaController::class, 'getByArNo'])->name('get-by-ar-no')->where('ar_no', '.*');
        Route::get('/get-by-ar-no_release/{ar_no}', [PlasmaController::class, 'getReleaseArNo'])->name('get-by-ar-no_release')->where('ar_no', '.*');
        Route::get('/get-by-batch-number/{batch_number}', [PlasmaController::class, 'getBatchNumbers'])->name('get-by-batch-number')->where('batch_number', '.*');
        Route::get('/get-ar-numbers', [PlasmaController::class, 'getArNumbers'])->name('get-ar-numbers');
        Route::get('/get-mini-pool-numbers', [PlasmaController::class, 'getMiniPoolNumbers'])
            ->name('get_mini_pool_numbers');
        Route::post('/get-bag-status-details', [PlasmaController::class, 'getBagStatusDetails'])->name('getBagStatusDetails');
        Route::get('/ar-list', [PlasmaController::class, 'arList'])->name('ar-list');
        Route::get('/ar-list/print', [PlasmaController::class, 'printArList'])->name('ar-list.print');
        Route::post('/get-ar-list', [PlasmaController::class, 'getArList'])->name('get-ar-list');
        Route::get('/destruction-list', [PlasmaController::class, 'destructionList'])->name('destruction-list');
        Route::get('/destruction-list/print', [PlasmaController::class, 'printDestructionList'])->name('destruction-list.print');
        Route::post('/get-destruction-list', [PlasmaController::class, 'getDestructionList'])->name('get-destruction-list');
        Route::post('/reject-mega-pool', [PlasmaController::class, 'rejectMegaPool'])->name('reject-mega-pool');
        Route::get('/ar-details', [PlasmaController::class, 'getArDetails'])->name('ar-details');
        Route::get('/quality-rejected', [PlasmaController::class, 'getQualityRejectedEntries'])->name('quality-rejected');
    });

    // Add the API route for blood banks
    Route::get('/api/plasma/bloodbanks', [PlasmaController::class, 'getBloodBanks'])->name('api.plasma.bloodbanks');

    // Bag Entry Routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/bag-entries', [BagEntryController::class, 'index'])->name('bag-entries.index');
        Route::post('/bag-entries', [BagEntryController::class, 'store'])->name('bag-entries.store');
        Route::get('/bag-entries/{bagEntry}', [BagEntryController::class, 'show'])->name('bag-entries.show');
    });

    /* ********************* NAT Report Routes ********************************* */
    Route::middleware(['auth'])->group(function () {
        Route::get('/nat-report', [NATReportController::class, 'index'])->name('nat-report.index');
        Route::post('/nat-report/generate', [NATReportController::class, 'generateReport'])->name('nat-report.generate');
        Route::post('/nat-report/save', [NATReportController::class, 'saveReports'])->name('nat-report.save');

        // NAT Retest Mega Pool Routes
        Route::post('/nat-retest-mega/generate', [NATReportController::class, 'generateRetestReport'])->name('nat-retest-mega.generate');
        Route::post('/nat-retest-mega/save', [NATReportController::class, 'saveRetestReports'])->name('nat-retest-mega.save');
    });
    /* ********************* NAT Report Routes Ends ********************************* */

    Route::get('/mini-pool-details', [BagStatusController::class, 'getMiniPoolDetails'])->name('mini.pool.details');
    Route::get('/blood-centres', [BagStatusController::class, 'getBloodCentres'])->name('blood.centres');
    Route::get('/cities', [BagStatusController::class, 'getCities'])->name('cities');
});

// Factory Generate Report Routes
Route::get('/factory/generate-report/mega-pool-mini-pool', [ReportMiniPoolMegaPoolController::class, 'index'])
    ->name('factory.generate_report.mega_pool_mini_pool');
Route::post('/factory/generate-report/fetch-mega-pool-data', [ReportMiniPoolMegaPoolController::class, 'fetchMegaPoolData'])
    ->name('factory.generate_report.fetch_mega_pool_data');
Route::post('/factory/generate-report/print-mega-pool-report', [ReportMiniPoolMegaPoolController::class, 'printMegaPoolReport'])
    ->name('factory.generate_report.print_mega_pool_report');
Route::get('/factory/generate-report/fetch-ar-numbers', [ReportMiniPoolMegaPoolController::class, 'fetchARNumbers'])
    ->name('factory.generate_report.fetch_ar_numbers');

Route::get('/factory/generate-report/sub-mini-pool', function () {
    return view('factory.generate_report.sub_mini_pool');
})->name('factory.generate_report.sub_mini_pool');

Route::get('/factory/generate-report/plasma_dispensing', function () {
    return view('factory.generate_report.plasma_dispensing');
})->name('factory.generate_report.plasma_dispensing');

// Tail Cutting Report Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/factory/generate-report/tail-cutting', [ReportMiniPoolMegaPoolController::class, 'tailCuttingReport'])
        ->name('factory.generate_report.tail_cutting');
    Route::post('/factory/generate-report/tail-cutting', [ReportMiniPoolMegaPoolController::class, 'tailCuttingReport'])
        ->name('factory.generate_report.tail_cutting.fetch');
    Route::get('/factory/generate-report/tail-cutting/print', [ReportMiniPoolMegaPoolController::class, 'tailCuttingPrintTemplate'])
        ->name('factory.generate_report.tail_cutting.print');
});

/* *********************  Factory Report Routes Start ********************************* */
Route::get('/factory/report/plasma-despense', [PlasmaController::class, 'despense'])->name('factory.report.plasma_despense');
Route::get('/factory/report/plasma-release', [PlasmaController::class, 'release'])->name('factory.report.plasma_release');
Route::post('/factory/report/plasma-submit', [PlasmaController::class, 'submitPlasma'])->name('factory.report.plasma_submit');
Route::get('/factory/report/plasma-rejection', [PlasmaController::class, 'rejection'])->name('factory.report.plasma_rejection');
Route::post('/factory/report/plasma-rejection', [BagStatusController::class, 'storePlasmaRejection'])->name('plasma.rejection.store');
Route::post('/factory/report/nat-plasma-rejection', [BagStatusController::class, 'storeNatRejection'])->name('nat.plasma.rejection.store');
Route::get('/plasma/rejection/details', [BagStatusController::class, 'getPlasmaRejectionDetails'])->name('plasma.rejection.details');
Route::get('/get-ar-numbers', [BagStatusController::class, 'getArNumbers'])->name('get.ar.numbers');

/* *********************  Factory Report Routes Ends ********************************* */

/* ********************* Factory Report Routes ********************************* */
Route::get('/factory/report/sub-minipool-entry', [PlasmaController::class, 'subMiniPoolEntry'])->name('factory.report.sub_minipool_entry')->middleware('auth');
Route::get('/factory/report/get-reactive-minipools', [ReportMiniPoolMegaPoolController::class, 'getReactiveMiniPools'])->name('factory.report.get-reactive-minipools')->middleware('auth');
Route::get('/factory/report/get-minipool-data/{mini_pool_id}', [ReportMiniPoolMegaPoolController::class, 'getMiniPoolData'])->name('factory.report.get-minipool-data')->middleware('auth');
Route::post('/factory/report/upload-subminipool-results', [SubMiniPoolEntryController::class, 'uploadResults'])->name('subminipool.upload-results')->middleware('auth');
Route::post('/factory/report/save-subminipool-results', [SubMiniPoolEntryController::class, 'saveResults'])->name('subminipool.save-results')->middleware('auth');
Route::get('/factory/report/get-all-subminipool-results', [SubMiniPoolEntryController::class, 'getAllResults'])->name('subminipool.get-all-results')->middleware('auth');
Route::get('/factory/report/get-mini-pools-with-results', [SubMiniPoolEntryController::class, 'getMiniPoolsWithResults'])->name('subminipool.get-mini-pools-with-results')->middleware('auth');
Route::get('/factory/report/get-sub-mini-pools-by-mini-pool', [SubMiniPoolEntryController::class, 'getSubMiniPoolsByMiniPool'])->name('subminipool.get-sub-mini-pools-by-mini-pool')->middleware('auth');
Route::get('/factory/report/get-subminipool-report-data', [SubMiniPoolEntryController::class, 'getReportData'])->name('subminipool.get-report-data')->middleware('auth');

Route::post('/sub-mini-pool-entries', [SubMiniPoolEntryController::class, 'store'])->name('sub-mini-pool-entries.store');

/* *********************  Expenses Ends ********************************* */

/* *********************  Plasma Despense Starts ********************************* */
Route::get('/plasma/despense', [BagStatusController::class, 'showPlasmaDespense'])->name('plasma.despense');
Route::post('/plasma/despense/store', [BagStatusController::class, 'storePlasmaDespense'])->name('plasma.despense.store');
Route::get('/mini-pool-details', [BagStatusController::class, 'getMiniPoolDetails'])->name('mini.pool.details');
Route::get('/mini-pool-nonreactive-details', [BagStatusController::class, 'getNonReactiveMiniPoolDetails'])->name('mini.pool.nonreactive.details');
/* *********************  Plasma Despense Ends ********************************* */

// Route::post('/plasma/rejection/print', [PlasmaRejectionController::class, 'print'])->name('plasma.rejection.print');
Route::post('/plasma/rejection/print', [PlasmaRejectionController::class, 'print'])->name('plasma.rejection.print');

Route::middleware(['auth'])->group(function () {
    Route::post('/print/bartender', [PrinterLabelController::class, 'printWithBarTender'])->name('labels.bartender');
    Route::post('/labels/preview', [PrinterLabelController::class, 'preview'])->name('labels.preview');
    Route::post('/labels/browser-print', [PrinterLabelController::class, 'browserPrint'])->name('labels.browser-print');
    Route::get('/labels/system-printers', [PrinterLabelController::class, 'getSystemPrinters'])->name('labels.system-printers');
    Route::post('/labels/print', [PrinterLabelController::class, 'printLabels'])->name('labels.print');
    Route::post('/labels/download-pgl', [PrinterLabelController::class, 'downloadPgl'])->name('labels.download-pgl');
});

// Simple Print Server API endpoint - typically would be in a separate application
// This route doesn't require authentication for demo purposes only
Route::post('/print-server', function (Request $request) {
    // Log the incoming print request
    \Illuminate\Support\Facades\Log::info('Print server request received', [
        'data' => $request->all(),
        'ip' => $request->ip()
    ]);

    // Validate the request
    $validated = $request->validate([
        'zpl_data' => 'required|string',
        'printer_path' => 'required|string',
    ]);

    // In a real print server, you would:
    // 1. Save the ZPL data to a file
    // 2. Send it to a printer directly using system commands
    // 3. Return success/failure status

    // For demo purposes, just write to a file in storage
    $filename = 'print_job_' . time() . '.zpl';
    $path = storage_path('print_jobs/' . $filename);

    // Ensure the directory exists
    if (!file_exists(storage_path('print_jobs'))) {
        mkdir(storage_path('print_jobs'), 0777, true);
    }

    // Write the ZPL data to the file
    file_put_contents($path, $validated['zpl_data']);

    // Return a success response
    return response()->json([
        'success' => true,
        'message' => 'Print job received and saved',
        'file' => $filename,
        'printer_path' => $validated['printer_path'],
        'bytes_received' => strlen($validated['zpl_data'])
    ]);
});

/* *********************  Audit Trail Routes ********************************* */
Route::middleware(['auth'])->group(function () {
    // Main audit trail report page
    Route::get('/audit-trail', [AuditTrailController::class, 'index'])->name('audit.index');

    // API route to get audit trail data (for AJAX)
    Route::get('/audit-trail/data', [AuditTrailController::class, 'getAuditTrailData'])->name('audit.data');

    // View details of a specific audit record
    Route::get('/audit-trail/{id}', [AuditTrailController::class, 'show'])->name('audit.show');

    // Export audit trail to CSV
    Route::get('/audit-trail/export/csv', [AuditTrailController::class, 'export'])->name('audit.export');
});
/* *********************  Audit Trail Routes Ends ********************************* */

// Test route for audit trail
Route::get('/test-audit', function() {
    // Test different modules
    \App\Models\AuditTrail::log(
        'test',
        'Test Module',
        'Test Section',
        null,
        [],
        [],
        'This is a test audit entry'
    );

    \App\Models\AuditTrail::log(
        'view',
        'User Management',
        'User Profile',
        null,
        [],
        [],
        'Viewed user profile'
    );

    \App\Models\AuditTrail::log(
        'update',
        'Plasma Management',
        'Plasma Entry',
        null,
        ['status' => 'pending'],
        ['status' => 'approved'],
        'Updated plasma status'
    );

    \App\Models\AuditTrail::log(
        'create',
        'Bag Entry',
        'New Bag',
        null,
        [],
        ['bag_id' => 'BAG123', 'volume' => '250ml'],
        'Created new bag entry'
    );

    \App\Models\AuditTrail::log(
        'delete',
        'Report Upload',
        'ELISA Report',
        null,
        ['report_id' => 'RPT456'],
        [],
        'Deleted ELISA report'
    );

    return "Audit test entries created with various modules. Please check the audit trail.";
})->middleware('auth');

// Include profile routes
require __DIR__.'/profile.php';

/* *********************  BarTender Integration Starts ********************************* */
// This route will handle the print request from your form.
Route::post('/bartender/print', [PrinterLabelController::class, 'printWithBarTender'])->name('bartender.print');

// You will also need a route to display the form itself.
Route::get('/print-labels', function () {
    return view('generate'); // Assuming your blade file is named generate.blade.php
})->name('labels.generate');
/* *********************  BarTender Integration Ends ********************************* */
