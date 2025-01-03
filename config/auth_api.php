<?php

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

    'login_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/login.php',
    'entity_register_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/entity_register.php',
    'entity_fetch_all_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/entity_fetch_all.php',
    'entity_fetch_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/entity_fetch.php?id={id}',
    'entity_update_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/entity_update.php',
    'entity_fetch_all_active_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/entity_fetch_all_active.php',

    'getAllUsers_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/getAllUsers.php',
    'createUser_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/create_user.php',
    'getUser_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/get_user.php?id={id}',
    'updateUser_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/update_user.php',


    'getAllCities_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/cities_get_all.php',
    'add_city_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/city_add.php',

    'blood_bank_fetch_all_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/blood_bank_fetch_all.php',
    'blood_bank_register_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/blood_bank_register.php',
    'blood_bank_fetch_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/blood_bank_fetch.php?id={id}',
    'blood_bank_update_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/blood_bank_update.php',

    'warehouse_fetch_all_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/warehouse_fetch_all.php',
    'warehouse_register_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/warehouse_register.php',
    'warehouse_fetch_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/warehouse_fetch.php?id={id}',
    'warehouse_update_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/warehouse_update.php',

    'getAllSoucingAgents_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/sourcing_agents_list_all.php',
    'getAllCollectingAgents_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/collecting_agents_list_all.php',
    'tour_plan_create_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/tour_plan_create.php',
    'tour_plan_fetch_all_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/tour_plan_fetch_all.php',
    'tour_plan_delete_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/tour_plan_delete.php',

    'states_by_countryId_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/states_by_countryId.php?id={id}',
    'cities_by_stateId_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/cities_by_stateId.php?id={id}',

    'dashbaord_web_url' => 'http://127.0.0.1/PlasmaGenAPIs/api/dashbaord_web.php',
];
