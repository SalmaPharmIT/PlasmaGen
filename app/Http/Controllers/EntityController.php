<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class EntityController extends Controller
{
    /**
     * Show the Entity Registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        // Fetch data for dropdowns (entity types, countries, etc.)
        $entityTypes = \App\Models\EntityTypeMaster::all();
        $countries = \App\Models\Country::all();
        $states = \App\Models\State::all();
        $cities = \App\Models\City::all();

        // Get the IDs for Blood Bank and Warehouse
        $bloodBankTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Blood Bank')->value('id');
        $warehouseTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Warehouse')->value('id');


        return view('entities.register', compact('entityTypes', 'countries', 'states', 'cities', 'bloodBankTypeId', 'warehouseTypeId'));
        
    }

    /**
     * Handle the Entity Registration form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'entity_type_id' => 'required|exists:entity_type_master,id',
            'entity_license_number' => 'nullable|string|max:255',
            'pan_id' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'fax_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile_no' => 'required|string|max:15|unique:entities,mobile_no',
            'bank_account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'entity_customer_care_no' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'license_validity' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'username' => 'nullable|string|max:255|unique:entities,username',
            'password' => 'nullable|string|min:6|confirmed',
            'parent_entity_id' => [
            'nullable',
            'exists:entities,id',
            function ($attribute, $value, $fail) use ($request) {
                $entityType = \App\Models\EntityTypeMaster::find($request->input('entity_type_id'));
                if (in_array($entityType->entity_name, ['Blood Bank', 'Warehouse']) && empty($value)) {
                    $fail('The parent entity is required for the selected entity type.');
                }
            }
        ],
        ]);

         // Retrieve the token from the session
         $token = $request->session()->get('api_token');

         if (!$token) {
             // Log the missing token
             Log::warning('API token missing in session.');
 
             // Redirect to login with an error message
             return Redirect::route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
         }
 

          // Prepare the data to send to the external API
          $postData = [
            'name'                  => $validatedData['name'],
            'entity_type_id'        => $validatedData['entity_type_id'],
            'entity_license_number' => $request->input('entity_license_number'),
            'pan_id'                => $request->input('pan_id'),
            'country_id'            => $request->input('country_id'),
            'state_id'              => $request->input('state_id'),
            'city_id'               => $request->input('city_id'),
            'pincode'               => $request->input('pincode'),
            'address'               => $request->input('address'),
            'fax_number'            => $request->input('fax_number'),
            'email'                 => $request->input('email'),
            'mobile_no'             => $validatedData['mobile_no'],
            'bank_account_number'   => $request->input('bank_account_number'),
            'ifsc_code'             => $request->input('ifsc_code'),
            'entity_customer_care_no' => $request->input('entity_customer_care_no'),
            'gstin'                 => $request->input('gstin'),
            'billing_address'       => $request->input('billing_address'),
            'license_validity'      => $request->input('license_validity'),
            'latitude'              => $request->input('latitude'),
            'longitude'             => $request->input('longitude'),
            // 'username'              => $request->input('username'),
            // 'password'              => $request->input('password'),
            'created_by'            => $request->input('created_by', ''),
            'modified_by'           => $request->input('modified_by', ''),
            'entity_id'             => $request->input('parent_entity_id'), // Include parent entity
            // 'logo' will be handled separately
        ];

         // Remove 'password_confirmation' if present
         if (isset($postData['password_confirmation'])) {
            unset($postData['password_confirmation']);
        }

        // Handle the logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');

            // Read the file content
            $logoContent = file_get_contents($logo->getRealPath());

            // Encode the file content to Base64
            $logoBase64 = 'data:' . $logo->getMimeType() . ';base64,' . base64_encode($logoContent);

            // Add the Base64-encoded logo to the data
            $postData['logo'] = $logoBase64;
        }

       
        // Define the external API URL
        $apiUrl = config('auth_api.entity_register_url'); // Ensure this is correctly set in config/auth_api.php

        try {

            // Log the data being sent
            Log::info('Sending data to Entity Registration API', [
                'data' => $postData,
            ]);

           
            // Make the API request with the Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('Entity Registration API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);


            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    // Handle successful registration
                   // return back()->with('success', 'Entity registered successfully.');
                    return redirect()->route('entities.index')->with('success', 'Entity registered successfully.');
                } else {
                    // Log the failure message from the API
                    Log::warning('Entity registration failed', ['message' => $apiResponse['message']]);

                    // Redirect back with the error message
                    return back()->withErrors(['registration_error' => $apiResponse['message']])->withInput();
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('Entity registration API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                // Redirect back with a generic error message
                return back()->withErrors(['api_error' => 'Failed to connect to the registration server.'])->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Entity registration exception', ['error' => $e->getMessage()]);

            // Redirect back with an exception message
            return back()->withErrors(['exception' => 'An error occurred while registering the entity.'])->withInput();
        }
    }

    /**
     * Show the entities list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('entities.index');
    }

    /**
     * API Endpoint to Fetch All Entities.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEntities()
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching entities
        $apiUrl = config('auth_api.entity_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Entity fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Entity fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching entities from external API.', ['api_url' => $apiUrl]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    return response()->json([
                        'success' => true,
                        'data' => Arr::get($apiResponse, 'data', []),
                    ]);
                } else {
                    Log::warning('External API returned failure.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ]);
                }
            } else {
                Log::error('Failed to fetch entities from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch entities from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching entities from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching entities.',
            ], 500);
        }
    }


    public function edit($id)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return Redirect::route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL for fetching a single entity
        $apiUrl = config('auth_api.entity_fetch_url');

        if (!$apiUrl) {
            Log::error('Entity fetch single URL not configured.');
            return back()->withErrors(['api_error' => 'Entity fetch URL is not configured.']);
        }

        // Replace {id} placeholder with actual ID
        $apiUrl = str_replace('{id}', $id, $apiUrl);

        Log::info('Fetching single entity from external API.', ['api_url' => $apiUrl]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for Single Entity', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    $entity = Arr::get($apiResponse, 'data');

                    // Fetch related data for dropdowns
                    $entities = \App\Models\Entity::all();
                    $entityTypes = \App\Models\EntityTypeMaster::all();
                    $countries    = \App\Models\Country::all();
                    $states       = \App\Models\State::all();
                    $cities       = \App\Models\City::all();
                    $parentEntityTypesResponse = $this->getParentEntities();

                    $responseContent = $parentEntityTypesResponse->getContent();
                    $parentEntityTypesData = json_decode($responseContent, true);
                    $parentEntityTypes = $parentEntityTypesData['data'] ?? [];

                    // Get the IDs for Blood Bank and Warehouse
                    $bloodBankTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Blood Bank')->value('id');
                    $warehouseTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Warehouse')->value('id');

                    $entity['parent_entity_id'] = $entity['entity_id'];

                    Log::info('Fetching single entity data from external API.', ['entity' => $entity]);

                    return view('entities.edit', compact('entity', 'entityTypes', 'countries', 'states', 'cities', 'entities', 'bloodBankTypeId', 'warehouseTypeId', 'parentEntityTypes'));
                } else {
                    Log::warning('External API returned failure for single entity.', ['message' => Arr::get($apiResponse, 'message')]);
                    return back()->withErrors(['api_error' => Arr::get($apiResponse, 'message', 'Unknown error from API.')]);
                }
            } else {
                Log::error('Failed to fetch single entity from external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withErrors(['api_error' => 'Failed to fetch entity from the external API.']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching single entity from external API.', ['error' => $e->getMessage()]);
            return back()->withErrors(['exception' => 'An error occurred while fetching the entity.']);
        }
    }

    /**
     * Update the specified entity in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'entity_type_id' => 'required|exists:entity_type_master,id',
            'entity_license_number' => 'nullable|string|max:255',
            'pan_id' => 'nullable|string|max:255',
            'country_id' => 'nullable|exists:countries,id',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'pincode' => 'nullable|string|max:10',
            'address' => 'nullable|string',
            'fax_number' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile_no' => 'required|string|max:15|unique:entities,mobile_no,' . $id,
            'bank_account_number' => 'nullable|string|max:255',
            'ifsc_code' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'entity_customer_care_no' => 'nullable|string|max:255',
            'gstin' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'license_validity' => 'nullable|date',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'username' => 'nullable|string|max:255|unique:entities,username,' . $id,
            'password' => 'nullable|string|min:6|confirmed',
            'account_status' => 'required|in:active,inactive,suspended', // New validation rule
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL
        $apiUrl = config('auth_api.entity_update_url');

        // Prepare the data to send
        $postData = [
            'id'                    => $id,
            'name'                  => $validatedData['name'],
            'entity_type_id'        => $validatedData['entity_type_id'],
            'entity_license_number' => $request->input('entity_license_number'),
            'pan_id'                => $request->input('pan_id'),
            'country_id'            => $request->input('country_id'),
            'state_id'              => $request->input('state_id'),
            'city_id'               => $request->input('city_id'),
            'pincode'               => $request->input('pincode'),
            'address'               => $request->input('address'),
            'fax_number'            => $request->input('fax_number'),
            'email'                 => $request->input('email'),
            'mobile_no'             => $validatedData['mobile_no'],
            'bank_account_number'   => $request->input('bank_account_number'),
            'ifsc_code'             => $request->input('ifsc_code'),
            'entity_customer_care_no'=> $request->input('entity_customer_care_no'),
            'gstin'                 => $request->input('gstin'),
            'billing_address'       => $request->input('billing_address'),
            'license_validity'      => $request->input('license_validity'),
            'latitude'              => $request->input('latitude'),
            'longitude'             => $request->input('longitude'),
            'username'              => $request->input('username'),
            'password'              => $request->input('password'),
            'modified_by'           => auth()->id(),
            'account_status'        => $request->input('account_status'), // Include account_status
        ];

        // Send the request using Laravel's HTTP client
        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ]);

        // if ($request->hasFile('logo')) {
        //     $logo = $request->file('logo');
        //     $http = $http->attach('logo', fopen($logo->getRealPath(), 'r'), $logo->getClientOriginalName());
        // }

        // Handle the logo upload
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');

            // Read the file content
            $logoContent = file_get_contents($logo->getRealPath());

            // Encode the file content to Base64
            $logoBase64 = 'data:' . $logo->getMimeType() . ';base64,' . base64_encode($logoContent);

            // Add the Base64-encoded logo to the data
            $postData['logo'] = $logoBase64;
        }

        try {
            $response = $http->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                
                    return back()->with('success', 'Entity updated successfully.');
                } else {
                    return back()->withErrors(['update_error' => $apiResponse['message']])->withInput();
                }
            } else {
                return back()->withErrors(['api_error' => 'Failed to connect to the update server.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['exception' => 'An error occurred while updating the entity: ' . $e->getMessage()])->withInput();
        }
    }


    /**
     * Fetch Parent Entities for the Parent Entity Dropdown.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getParentEntities()
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching entities
        $apiUrl = config('auth_api.entity_fetch_parent_active_url');

        if (!$apiUrl) {
            Log::error('Entity fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Entity fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    return response()->json([
                        'success' => true,
                        'data' => $apiResponse['data'],
                    ]);
                } else {
                    Log::warning('External API returned failure.', ['message' => $apiResponse['message']]);
                    return response()->json([
                        'success' => false,
                        'message' => $apiResponse['message'],
                    ]);
                }
            } else {
                Log::error('Failed to fetch entities from external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch entities from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching entities from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching entities.',
            ], 500);
        }
    }


    /**
     * Show the entities feature settings page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
    */
    public function entityFeatures()
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token missing. Please log in again.']);
        }

        // Define the external API URL for fetching entity features
        $apiUrl = config('auth_api.entity_features_fetch_url');

        if (!$apiUrl) {
            Log::error('Entity Features Fetch URL not configured.');
            return back()->withErrors(['api_error' => 'Entity Features Fetch URL is not configured.']);
        }

        try {
            // Make the API request to fetch entity features
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('Entity Features Fetch API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    $features = $apiResponse['data'];

                    // Convert features array to an object for easier access in Blade
                    $entity = (object) $features;

                    // Log the fetched features
                    Log::info('Fetched Entity Features', ['features' => $entity]);

                    // Pass 'entity' to the view instead of 'features'
                    return view('entities.featuresettings', compact('entity'));
                } else {
                    Log::warning('Entity Features Fetch API returned failure.', ['message' => $apiResponse['message']]);
                    return back()->withErrors(['api_error' => $apiResponse['message']]);
                }
            } else {
                Log::error('Failed to fetch entity features from external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withErrors(['api_error' => 'Failed to fetch entity features from the external API.']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching entity features from external API.', ['error' => $e->getMessage()]);
            return back()->withErrors(['exception' => 'An error occurred while fetching entity features.']);
        }
    }


       /**
     * Handle the Entity Feature Settings form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateFeatureSettings(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'km_bound' => 'required|numeric|min:0',
            'location_enabled' => 'required|in:yes,no',
            // Add other validation rules as necessary
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL for updating feature settings
        $apiUrl = config('auth_api.entity_features_update_url'); // Ensure this is set in config/auth_api.php

        if (!$apiUrl) {
            Log::error('Entity Features Update URL not configured.');
            return back()->withErrors(['api_error' => 'Entity Features Update URL is not configured.']);
        }

        // Prepare the data to send to the external API
        $postData = [
            'km_bound'          => $validatedData['km_bound'],
            'location_enabled'  => $validatedData['location_enabled'],
            'modified_by'       => Auth::id(),
        ];

        Log::info('Entity Features Update API Request', [
            'data' => $postData
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->put($apiUrl, $postData);

            Log::info('Entity Features Update API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    // Handle successful update
                    return back()->with('success', 'Entity Feature Settings updated successfully.');
                } else {
                    // Log the failure message from the API
                    Log::warning('Entity Features Update API returned failure.', ['message' => $apiResponse['message']]);

                    // Redirect back with the error message
                    return back()->withErrors(['update_error' => $apiResponse['message']])->withInput();
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('Failed to update entity features via external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                // Redirect back with a generic error message
                return back()->withErrors(['api_error' => 'Failed to update entity features via the external API.'])->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Exception while updating entity features via external API.', ['error' => $e->getMessage()]);

            // Redirect back with an exception message
            return back()->withErrors(['exception' => 'An error occurred while updating entity features.'])->withInput();
        }
    }
    
}
