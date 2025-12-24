<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class WarehouseController extends Controller
{

    /**
     * Show the entities list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('warehouse.index');
    }

    /**
     * API Endpoint to Fetch All Warehouses.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWarehouses()
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
        $apiUrl = config('auth_api.warehouse_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Warehouse fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Warehouse fetch URL is not configured.'
            ], 500);
        }

        Log::info('Fetching Warehouse from external API.', ['api_url' => $apiUrl]);

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
                Log::error('Failed to fetch Warehouse from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch Warehouse from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching Warehouse from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching Warehouse.',
            ], 500);
        }
    }

    /**
     * Show the Warehouse Registration form.
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


        return view('warehouse.register', compact('entityTypes', 'countries', 'states', 'cities', 'bloodBankTypeId', 'warehouseTypeId'));
        
    }


    /**
     * Handle the Warehouse Registration form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate the form data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
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
            'entity_contact_person' => 'nullable|string|max:255',
            'FFP_procurement_company' => 'nullable|string|max:255',
            'final_accepted_offer' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            // New validation rules for documents
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'is_plant_warehouse' => 'nullable',
        ]);

         // Retrieve the token from the session
         $token = $request->session()->get('api_token');

         if (!$token) {
             // Log the missing token
             Log::warning('API token missing in session.');
 
             // Redirect to login with an error message
             return Redirect::route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
         }
 
         $isPlantWarehouse = $request->has('is_plant_warehouse') ? 1 : 0;

          // Prepare the data to send to the external API
          $postData = [
            'name'                  => $validatedData['name'],
            'entity_type_id'        => $request->input('entity_type_id', '3'),   // entity_type_id - 3 for Warehouses
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
            'created_by'            => $request->input('created_by', ''),
            'modified_by'           => $request->input('modified_by', ''),
            'entity_contact_person'  => $request->input('entity_contact_person'),
            'FFP_procurement_company'=> $request->input('FFP_procurement_company'),
            'final_accepted_offer'   => $request->input('final_accepted_offer'),
            'payment_terms'          => $request->input('payment_terms'),
            'is_plant_warehouse'     => $isPlantWarehouse,
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

       
       // Handle multiple documents
        if ($request->hasFile('documents')) {
            $postData['documents'] = []; // Initialize as an array
            foreach ($request->file('documents') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['documents'][] = $documentBase64;
            }
        }

       
        // Define the external API URL
        $apiUrl = config('auth_api.warehouse_register_url'); // Ensure this is correctly set in config/auth_api.php

        try {

            // Log the data being sent
            Log::info('Sending data to Blood Bank Registration API', [
                'data' => $postData,
            ]);

           
            // Make the API request with the Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('Blood Bank Registration API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);


            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    // Handle successful registration
                   // return back()->with('success', 'Entity registered successfully.');
                    return redirect()->route('warehouse.index')->with('success', 'Warehouse registered successfully.');
                } else {
                    // Log the failure message from the API
                    Log::warning('Warehouse registration failed', ['message' => $apiResponse['message']]);

                    // Redirect back with the error message
                    return back()->withErrors(['registration_error' => $apiResponse['message']])->withInput();
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('Warehouse registration API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                // Redirect back with a generic error message
                return back()->withErrors(['api_error' => 'Failed to connect to the registration server.'])->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('Warehouse registration exception', ['error' => $e->getMessage()]);

            // Redirect back with an exception message
            return back()->withErrors(['exception' => 'An error occurred while registering the Warehouse.'])->withInput();
        }
    }


    /**
     * Edit View the Wrehouse.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function edit($id)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return Redirect::route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL for fetching a single warehouse
        $apiUrl = config('auth_api.warehouse_fetch_url');

        if (!$apiUrl) {
            Log::error('Warehouse fetch single URL not configured.');
            return back()->withErrors(['api_error' => 'Warehouse fetch URL is not configured.']);
        }

        // Replace {id} placeholder with actual ID
        $apiUrl = str_replace('{id}', $id, $apiUrl);

        Log::info('Fetching single Warehouse from external API.', ['api_url' => $apiUrl]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for Single Warehouse', [
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

                    // Get the IDs for Blood Bank and Warehouse
                    $bloodBankTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Blood Bank')->value('id');
                    $warehouseTypeId = \App\Models\EntityTypeMaster::where('entity_name', 'Warehouse')->value('id');

                    $entity['parent_entity_id'] = $entity['entity_id'];

                    Log::info('Fetching single Warehouse data from external API.', ['entity' => $entity]);

                    return view('warehouse.edit', compact('entity', 'entityTypes', 'countries', 'states', 'cities', 'entities', 'bloodBankTypeId', 'warehouseTypeId'));
                } else {
                    Log::warning('External API returned failure for single Warehouse.', ['message' => Arr::get($apiResponse, 'message')]);
                    return back()->withErrors(['api_error' => Arr::get($apiResponse, 'message', 'Unknown error from API.')]);
                }
            } else {
                Log::error('Failed to fetch single Warehouse from external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withErrors(['api_error' => 'Failed to fetch Warehouse from the external API.']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching single Warehouse from external API.', ['error' => $e->getMessage()]);
            return back()->withErrors(['exception' => 'An error occurred while fetching the Warehouse.']);
        }
    }



    /**
     * Update the specified warehouse in storage.
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
            'entity_contact_person' => 'nullable|string|max:255',
            'FFP_procurement_company' => 'nullable|string|max:255',
            'final_accepted_offer' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            // New validation rules for documents
            'documents' => 'nullable|array',
            'documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,csv,txt|max:2048',
            'documents_to_delete' => 'nullable|array',
            'documents_to_delete.*' => 'string',
            'is_plant_warehouse' => 'nullable',
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL
        $apiUrl = config('auth_api.warehouse_update_url');

        $isPlantWarehouse = (int) $request->input('is_plant_warehouse', 0);
        
        // Prepare the data to send
        $postData = [
            'id'                    => $id,
            'name'                  => $validatedData['name'],
            'entity_type_id'        => $request->input('entity_type_id', '3'),   // entity_type_id - 3 for Warehouses
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
            'entity_contact_person'  => $request->input('entity_contact_person'),
            'FFP_procurement_company'=> $request->input('FFP_procurement_company'),
            'final_accepted_offer'   => $request->input('final_accepted_offer'),
            'payment_terms'          => $request->input('payment_terms'),
            'is_plant_warehouse'    => $isPlantWarehouse,
        ];

        // Send the request using Laravel's HTTP client
        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ]);

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

        // Handle multiple documents
        if ($request->hasFile('documents')) {
            $postData['documents'] = []; // Initialize as an array
            foreach ($request->file('documents') as $document) {
                if ($document->isValid()) {
                    $documentContent = file_get_contents($document->getRealPath());
                    $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                    $postData['documents'][] = $documentBase64;
                } else {
                    Log::warning('Invalid document file:', ['file' => $document->getClientOriginalName()]);
                }
            }

            Log::info('Processed documents:', ['documents' => $postData['documents']]);
        } else {
            Log::info('No documents to process.');
        }

        // Correct handling of documents to delete
        if ($request->has('documents_to_delete')) {
            $documentsToDelete = $request->input('documents_to_delete', []);
            $postData['documents_to_delete'] = $documentsToDelete;
            Log::info('Documents to delete:', ['documents_to_delete' => $documentsToDelete]);
        }

        // Log the data being sent
        Log::info('Sending data to Warehouse Update API', ['data' => $postData]);

        try {

            $response = $http->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                
                    return back()->with('success', 'Warehouse details updated successfully.');
                } else {
                    return back()->withErrors(['update_error' => $apiResponse['message']])->withInput();
                }
            } else {
                return back()->withErrors(['api_error' => 'Failed to connect to the update server.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['exception' => 'An error occurred while updating the warehouse details: ' . $e->getMessage()])->withInput();
        }
    }

}
