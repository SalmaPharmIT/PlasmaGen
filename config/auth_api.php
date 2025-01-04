<?php

// Define base URL path for api here:
$base_url = 'http://127.0.0.1/PlasmaGenAPIs/api/';
$base_image_url = 'http://localhost/plasmaGenUploads/';

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
];
