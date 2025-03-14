<?php

// Define base URL path for api here:
$base_url = 'http://127.0.0.1/PlasmaGenAPIs/api/';
// $base_image_url = 'http://localhost/plasmaGenUploads/';
$base_image_url = 'https://sfatestuploads.pharmit.in/plasmaGenUploads/';
$google_maps_api_key = 'AIzaSyBFwtHIaHQ1J8PKur9RmQy4Z5WsM6kVVPE';

return [
    /*
    |--------------------------------------------------------------------------
    | Authentication API Endpoints
    |--------------------------------------------------------------------------
    |
    | This file is for storing the URLs of external Authentication APIs used
    | in the application. These APIs handle tasks like login, registration,
    | password reset, and profile updates.
    |
    */

    'base_image_url' => $base_image_url,

    'login_url' =>  $base_url . 'login.php',
    'entity_register_url' =>  $base_url . 'entity_register.php',
    'entity_fetch_all_url' =>  $base_url . 'entity_fetch_all.php',
    'entity_fetch_url' =>  $base_url . 'entity_fetch.php?id={id}',
    'entity_update_url' =>  $base_url . 'entity_update.php',
    'entity_fetch_all_active_url' =>  $base_url . 'entity_fetch_all_active.php',

    'getAllUsers_url' =>  $base_url . 'getAllUsers.php',
    'createUser_url' =>  $base_url . 'create_user.php',
    'getUser_url' =>  $base_url . 'get_user.php?id={id}',
    'updateUser_url' =>  $base_url . 'update_user.php',


    'getAllCities_url' =>  $base_url . 'cities_get_all.php',
    'add_city_url' =>  $base_url . 'city_add.php',
    'update_city_url' =>  $base_url . 'city_update.php',
    'delete_city_url' =>  $base_url . 'city_delete.php',

    'blood_bank_fetch_all_url' =>  $base_url . 'blood_bank_fetch_all.php',
    'blood_bank_register_url' =>  $base_url . 'blood_bank_register.php',
    'blood_bank_fetch_url' =>  $base_url . 'blood_bank_fetch.php?id={id}',
    'blood_bank_update_url' =>  $base_url . 'blood_bank_update.php',

    'warehouse_fetch_all_url' =>  $base_url . 'warehouse_fetch_all.php',
    'warehouse_register_url' =>  $base_url . 'warehouse_register.php',
    'warehouse_fetch_url' =>  $base_url . 'warehouse_fetch.php?id={id}',
    'warehouse_update_url' =>  $base_url . 'warehouse_update.php',

    'getAllSoucingAgents_url' =>  $base_url . 'sourcing_agents_list_all.php',
    'getAllCollectingAgents_url' =>  $base_url . 'collecting_agents_list_all.php',
    'tour_plan_create_url' =>  $base_url . 'tour_plan_create.php',
    'tour_plan_fetch_all_url' =>  $base_url . 'tour_plan_fetch_all.php',
    'tour_plan_delete_url' =>  $base_url . 'tour_plan_delete.php',

    'states_by_countryId_url' =>  $base_url . 'states_by_countryId.php?id={id}',
    'cities_by_stateId_url' =>  $base_url . 'cities_by_stateId.php?id={id}',

    'dashbaord_web_url' =>  $base_url . 'dashbaord_web.php',
    'roles_fetch_all_url' =>  $base_url . 'roles_fetch_all.php',
    'pending_documents_fetch_all_url' =>  $base_url . 'pending_documents_fetch_all.php',

    'collection_requests_all_url' =>  $base_url . 'collection_requests_all.php',
    'vehicle_details_submit_url' =>  $base_url . 'vehicle_details_submit.php',
    'collection_submitted_all_url' =>  $base_url . 'collection_submitted_all.php',
    'entity_features_fetch_url' =>  $base_url . 'entity_features_fetch.php',
    'entity_features_update_url' =>  $base_url . 'entity_features_update.php',
    'visits_per_day_all_url' =>  $base_url . 'visits_per_day_all.php',
    'drc_collections_submit_url' =>  $base_url . 'drc_collections_submit.php',
    'drc_sourcing_submit_url' =>  $base_url . 'drc_sourcing_submit.php',
    'dcr_approvals_fetch_all_url' =>  $base_url . 'dcr_approvals_fetch_all.php',
    'dcr_details_url' =>  $base_url . 'dcr_details.php',
    'dcr_update_status_url' =>  $base_url . 'dcr_update_status.php',
    'final_dcr_submit_url' =>  $base_url . 'final_dcr_submit.php',
    'final_dcr_approvals_fetch_all_url' =>  $base_url . 'final_dcr_approvals_fetch_all.php',
    'final_dcr_visit_details_fetch_all_url' =>  $base_url . 'final_dcr_visit_details_fetch_all.php',
    'final_dcr_status_fetch_url' =>  $base_url . 'final_dcr_status_fetch.php',
    'dcr_blood_bank_details_url' =>  $base_url . 'dcr_blood_bank_details.php',
    'reports_periodic_work_summary_url' =>  $base_url . 'reports_periodic_work_summary.php',
    'entity_fetch_parent_active_url' =>  $base_url . 'entity_fetch_parent_active.php',
    'employee_by_roleId_url' =>  $base_url . 'employee_by_roleId.php?roleId={id}',
    'roles_downward_hierarchy_url' =>  $base_url . 'roles_downward_hierarchy.php?roleId={id}',
    'user_report_mapping_create_url' =>  $base_url . 'user_report_mapping_create.php',
    'user_report_mapping_fetch_url' => $base_url . 'user_report_mapping_fetch.php',
    'user_report_mapping_edit_url' => $base_url . 'user_report_mapping_edit.php',
    'user_report_mapping_delete_url' => $base_url . 'user_report_mapping_delete.php',
    'user_work_location_mapping_create_url' =>  $base_url . 'user_work_location_mapping_create.php',
    'user_work_location_mapping_fetch_url' =>  $base_url . 'user_work_location_mapping_fetch.php',
    'user_work_location_mapping_edit_url' =>  $base_url . 'user_work_location_mapping_edit.php',
    'user_work_location_mapping_delete_url' =>  $base_url . 'user_work_location_mapping_delete.php',
    'employee_blood_bank_fetch_url' =>  $base_url . 'employee_blood_bank_fetch.php',
    'employee_cities_fetch_url' =>  $base_url . 'employee_cities_fetch.php',
    'employee_tp_status_url' =>  $base_url . 'employee_tp_status.php',
    'tour_plan_monthly_submit_url' =>  $base_url . 'tour_plan_monthly_submit.php',
    'tour_plan_edit_request_url' =>  $base_url . 'tour_plan_edit_request.php',
    'tour_plan_collection_request_url' =>  $base_url . 'tour_plan_collection_request.php',
    'tour_plan_collection_fetch_url' =>  $base_url . 'tour_plan_collection_fetch.php',
    'mark_tp_added_url' =>  $base_url . 'mark_tp_added.php',
    'dashboard_web_by_month_url' =>  $base_url . 'dashboard_web_by_month.php',
    'core_sourcing_bloodbanks_fetch_url' =>  $base_url . 'core_sourcing_bloodbanks_fetch.php',
    'gst_rates_fetch_url' =>  $base_url . 'gst_rates_fetch.php',
];
