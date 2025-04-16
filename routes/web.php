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
    /* *********************  Report Visits Ends ********************************* */


    /* ********************* Expenses Starts ********************************* */
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('/expenses/getUpdatedVisits', [ExpensesController::class, 'getUpdatedVisits'])->name('expenses.getUpdatedVisits');
    Route::get('/expenses/view', [ExpensesController::class, 'showExpenseView'])->name('expenses.view');
    Route::get('/expenses/fetchVisits', [ExpensesController::class, 'fetchVisitsExpenses'])->name('expenses.fetchVisits');
    Route::post('/expenses/submit', [ExpensesController::class, 'submitExpenses'])->name('expenses.submit');  
    Route::get('expenses/fetchExpenses/{tp_id}', [ExpensesController::class, 'fetchExpenses'])->name('expenses.fetchExpenses');
    Route::delete('/expenses/delete/{id}', [ExpensesController::class, 'deleteExpense'])->name('expenses.delete');
    /* *********************  Report Visits Ends ********************************* */

/* *********************  Users Starts ********************************* */
    // View Entities Page
    Route::get('/newBagEntry', [BagEntryController::class, 'index'])->name('newBag.index');
    Route::post('/newBagEntry', [BagEntryController::class, 'store'])->name('newBag.store');
    
    // Other registration routes...
});

