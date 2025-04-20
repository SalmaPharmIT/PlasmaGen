<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class UserController extends Controller
{
   
    /**
     * Show the users list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('users.index');
    }


    /**
     * API Endpoint to Fetch All Users.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getUsers()
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
        $apiUrl = config('auth_api.getAllUsers_url');

        if (!$apiUrl) {
            Log::error('Users fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Users fetch URL is not configured.'
            ], 500);
        }

      //  Log::info('Fetching users from external API.', ['api_url' => $apiUrl]);

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
                Log::error('Failed to fetch users from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch users from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching users from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching users.',
            ], 500);
        }
    }


     /**
     * Show the User Registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showUserRegistrationForm()
    {
        // Fetch data for dropdowns (entity types, countries, etc.)
        $entity = \App\Models\Entity::all();
        $countries = \App\Models\Country::all();
        $states = \App\Models\State::all();
        $cities = \App\Models\City::all();
      //  $roles = \App\Models\Role::all();
     //   $roles = \App\Models\Role::where('status', 'active')->get();
        $user = \App\Models\User::all();
        $userStatuses = \App\Models\User::$userStatuses;
        $userGender = \App\Models\User::$userGender;

        // Fetch roles via API
        $rolesResponse = $this->getRoles();

        $parentEntityTypesResponse = $this->getParentEntities();

        $responseContent = $parentEntityTypesResponse->getContent();
        $parentEntityTypesData = json_decode($responseContent, true);
        $parentEntityTypes = $parentEntityTypesData['data'] ?? [];

        // Handle API response
        if (is_array($rolesResponse) && isset($rolesResponse[0]->id)) { // Note the change to ->id
            // Successfully retrieved roles
            $roles = $rolesResponse;
        } elseif ($rolesResponse instanceof \Illuminate\Http\JsonResponse) {
            // An error occurred while fetching roles
            // You can choose to handle the error as needed
            // For example, redirect back with an error message
            return back()->withErrors(['roles_error' => 'Unable to fetch roles at this time. Please try again later.']);
        } else {
            // Unexpected response structure
            return back()->withErrors(['roles_error' => 'Unexpected error while fetching roles.']);
        }

        // Log::info('Fetching userStatuses.', ['userStatuses' => $userStatuses]);

        return view('users.register', compact('entity', 'countries', 'states', 'cities', 'roles', 'user', 'userStatuses', 'userGender', 'parentEntityTypes'));
        
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
          $request->validate([
            'name' => 'required|string|max:255',
            'entity_id' => 'required|exists:entities,id',
            'role_id' => 'required|exists:roles,id',
            'gender' => 'required|in:male,female,other',
            'mobile_no' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email',
            'dob' => 'nullable|date',
            'pan_id' => 'required|string|max:20',
            'aadhar_id' => 'required|string|max:20',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'required|string|max:10',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6|confirmed',
            'created_by' => 'nullable|exists:users,id',
            'modified_by' => 'nullable|exists:users,id',
            'test_type' => [
                'nullable',
                'in:1,2,3',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('role_id') == 16 && empty($value)) {
                        $fail('The test type field is required when role is 16.');
                    }
                },
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
            'name'                  => $request->input('name'),
            'entity_id'        => $request->input('entity_id'), 
            'role_id' => $request->input('role_id'),  
            'test_type' => $request->input('role_id') == 16 ? $request->input('test_type') : null,
            'gender'                => $request->input('gender'),
            'mobile_no'            => $request->input('mobile_no'),
            'email'              => $request->input('email'),
            'dob'               => $request->input('dob'),
            'pan_id'               => $request->input('pan_id'),
            'aadhar_id'               => $request->input('aadhar_id'),
            'country_id'                 => $request->input('country_id'),
            'state_id'             => $request->input('state_id'),
            'city_id'   => $request->input('city_id'),
            'pincode'             => $request->input('pincode'),
            'address' => $request->input('address'),
            'latitude'                 => $request->input('latitude'),
            'longitude'       => $request->input('longitude'),
            'username'              => $request->input('username'),
            'password'              => $request->input('password'),
            'created_by'            => $request->input('created_by', ''),
            'modified_by'           => $request->input('modified_by', ''),
            // 'logo' will be handled separately
        ];

         // Remove 'password_confirmation' if present
         if (isset($postData['password_confirmation'])) {
            unset($postData['password_confirmation']);
        }

        // Handle the logo upload
        if ($request->hasFile('profile_pic')) {
            $logo = $request->file('profile_pic');

            // Read the file content
            $logoContent = file_get_contents($logo->getRealPath());

            // Encode the file content to Base64
            $logoBase64 = 'data:' . $logo->getMimeType() . ';base64,' . base64_encode($logoContent);

            // Add the Base64-encoded logo to the data
            $postData['profile_pic'] = $logoBase64;
        }

       
        // Define the external API URL
        $apiUrl = config('auth_api.createUser_url'); // Ensure this is correctly set in config/auth_api.php

        try {

            // Log the data being sent
            Log::info('Sending data to User Registration API', [
                'data' => $postData,
            ]);

           
            // Make the API request with the Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('User Registration API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);


            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    // Handle successful registration
                   // return back()->with('success', 'User registered successfully.');
                    return redirect()->route('users.index')->with('success', 'User registered successfully.');
                } else {
                    // Log the failure message from the API
                    Log::warning('User registration failed', ['message' => $apiResponse['message']]);

                    // Redirect back with the error message
                    return back()->withErrors(['registration_error' => $apiResponse['message']])->withInput();
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('User registration API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                // Redirect back with a generic error message
                return back()->withErrors(['api_error' => 'Failed to connect to the registration server.'])->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('User registration exception', ['error' => $e->getMessage()]);

            // Redirect back with an exception message
            return back()->withErrors(['exception' => 'An error occurred while registering the user.'])->withInput();
        }
    }


    
     /**
     * Handle the User Registration form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return Redirect::route('login')->withErrors(['token_error' => 'Authentication token not found. Please log in again.']);
        }

        // Define the external API URL for fetching a single user
        $apiUrl = config('auth_api.getUser_url');

        if (!$apiUrl) {
            Log::error('User fetch single URL not configured.');
            return back()->withErrors(['api_error' => 'User fetch URL is not configured.']);
        }

        // Replace {id} placeholder with actual ID
        $apiUrl = str_replace('{id}', $id, $apiUrl);

        Log::info('Fetching single user from external API.', ['api_url' => $apiUrl]);

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
                    $user = Arr::get($apiResponse, 'data');

                    // Ensure test_type is properly set from API response
                    if (isset($user['test_type'])) {
                        $user['test_type'] = (string)$user['test_type']; // Convert to string for select option comparison
                    } else {
                        $user['test_type'] = null;
                    }

                    // Fetch related data for dropdowns
                    $entities = \App\Models\Entity::all();
                    $countries = \App\Models\Country::all();
                    $states = \App\Models\State::all();
                    $cities = \App\Models\City::all();
                    $roles = \App\Models\Role::where('status', 'active')
                        ->where('is_factory', 1)
                        ->get();
                    $userStatuses = \App\Models\User::$userStatuses;
                    $userGender = \App\Models\User::$userGender;

                    // Log the user data for debugging
                    Log::info('User data being passed to view', [
                        'user' => $user,
                        'test_type' => $user['test_type']
                    ]);

                    return view('users.edit', compact('user', 'entities', 'countries', 'states', 'cities', 'roles', 'userStatuses', 'userGender'));
                } else {
                    Log::warning('External API returned failure for single user.', ['message' => Arr::get($apiResponse, 'message')]);
                    return back()->withErrors(['api_error' => Arr::get($apiResponse, 'message', 'Unknown error from API.')]);
                }
            } else {
                Log::error('Failed to fetch single user from external API.', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return back()->withErrors(['api_error' => 'Failed to fetch user from the external API.']);
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching single user from external API.', ['error' => $e->getMessage()]);
            return back()->withErrors(['exception' => 'An error occurred while fetching the user.']);
        }
    }
    

    /**
     * Handle the User Update form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id  // User ID to update
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Validate the form data
        $request->validate([
            'name' => 'required|string|max:255',
            'entity_id' => 'required|exists:entities,id',
            'role_id' => 'required|exists:roles,id',
            'gender' => 'required|in:male,female,other',
            'mobile_no' => 'required|string|max:15',
            'email' => 'required|email|unique:users,email,' . $id, // Allow the current user's email
            'dob' => 'nullable|date',
            'pan_id' => 'required|string|max:20',
            'aadhar_id' => 'required|string|max:20',
            'account_status' => 'required|in:active,inactive,suspended',
            'country_id' => 'required|exists:countries,id',
            'state_id' => 'required|exists:states,id',
            'city_id' => 'required|exists:cities,id',
            'pincode' => 'required|string|max:10',
            'address' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'profile_pic' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'username' => 'required|string|max:255|unique:users,username,' . $id, // Allow the current user's username
            'password' => 'nullable|string|min:8|confirmed',
            'created_by' => 'nullable|exists:users,id',
            'modified_by' => 'nullable|exists:users,id',
            'test_type' => [
                'nullable',
                'in:1,2,3',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->input('role_id') == 16 && empty($value)) {
                        $fail('The test type field is required when role is 16.');
                    }
                },
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
            'id' => $id,
            'name' => $request->input('name'),
            'entity_id' => $request->input('entity_id'),
            'role_id' => $request->input('role_id'),
            'test_type' => $request->input('role_id') == 16 ? $request->input('test_type') : null,
            'gender' => $request->input('gender'),
            'mobile_no' => $request->input('mobile_no'),
            'email' => $request->input('email'),
            'dob' => $request->input('dob'),
            'pan_id' => $request->input('pan_id'),
            'aadhar_id' => $request->input('aadhar_id'),
            'account_status' => $request->input('account_status'),
            'country_id' => $request->input('country_id'),
            'state_id' => $request->input('state_id'),
            'city_id' => $request->input('city_id'),
            'pincode' => $request->input('pincode'),
            'address' => $request->input('address'),
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'username' => $request->input('username'),
            'modified_by' => $request->input('modified_by', Auth::user()->id ?? ''),
            // 'password' will be handled separately
        ];

        // Handle password if provided
        if ($request->filled('password')) {
            $postData['password'] = $request->input('password');
        }

        // Handle the profile picture upload
        if ($request->hasFile('profile_pic')) {
            $profilePic = $request->file('profile_pic');

            // Read the file content
            $profilePicContent = file_get_contents($profilePic->getRealPath());

            // Encode the file content to Base64
            $profilePicBase64 = 'data:' . $profilePic->getMimeType() . ';base64,' . base64_encode($profilePicContent);

            // Add the Base64-encoded profile picture to the data
            $postData['profile_pic'] = $profilePicBase64;
        }

        // Log the missing token
        Log::warning('User Update Request Body: ', $postData);

        // Define the external API URL for updating a user
        $apiUrl = config('auth_api.updateUser_url'); // Ensure this is correctly set in config/auth_api.php

        Log::warning('User Update apiUrl:', ['api_url' => $apiUrl]);

        // Send the request using Laravel's HTTP client
        $http = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ]);

        try {

             // Log the data being sent
             Log::info('Sending data to User Update API', [
                'data' => $postData,
            ]);

            $response = $http->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                
                    return back()->with('success', 'User updated successfully.');
                } else {
                    return back()->withErrors(['update_error' => $apiResponse['message']])->withInput();
                }
            } else {
                return back()->withErrors(['api_error' => 'Failed to connect to the update server.'])->withInput();
            }
        } catch (\Exception $e) {
            return back()->withErrors(['exception' => 'An error occurred while updating the user: ' . $e->getMessage()])->withInput();
        }
    }

    public function getStatesById($countryId)
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

        $apiUrl = config('auth_api.states_by_countryId_url');

        if (!$apiUrl) {
            Log::error('States API URL not configured.');
            return back()->withErrors(['api_error' => 'States API URL is not configured.']);
        }

        // Replace {countryId} placeholder with the actual value
        $apiUrl = str_replace('{id}', $countryId, $apiUrl);
        // Log::info('States API from external API.', ['api_url' => $apiUrl]);
       
    
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for States', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

    
            if ($response->successful()) {
                $apiResponse = $response->json();

              //  Log::info('Fetching states for country ID', ['apiResponse' => $apiResponse]);
                return response()->json([
                    'success' => true,
                    'data' => $apiResponse['data'] ?? []
                ]);
            }
    
            Log::error('Failed to fetch states', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch states.'], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception while fetching states', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }
    



    public function getCitiesById($stateId)
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

        $apiUrl = config('auth_api.cities_by_stateId_url');
       

        if (!$apiUrl) {
            Log::error('Cities API URL not configured.');
            return back()->withErrors(['api_error' => 'Cities API URL is not configured.']);
        }

        // Replace {stateId} placeholder with the actual value
        $apiUrl = str_replace('{id}', $stateId, $apiUrl);
      //  Log::info('Cities API from external API.', ['api_url' => $apiUrl]);
       
    
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for Cities', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

    
            if ($response->successful()) {
                $apiResponse = $response->json();

               // Log::info('Fetching cities for state ID', ['apiResponse' => $apiResponse]);
                return response()->json([
                    'success' => true,
                    'data' => $apiResponse['data'] ?? []
                ]);
            }
    
            Log::error('Failed to fetch cities', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch cities.'], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception while fetching cities', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }


    /**
     * Fetch roles from the external API.
     *
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function getRoles()
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session while fetching roles.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching roles
        $apiUrl = config('auth_api.roles_fetch_all_url');

        if (!$apiUrl) {
            Log::error('Roles fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Roles fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for Roles', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    // Assuming the API returns roles under 'data' key
                    $roles = Arr::get($apiResponse, 'data', []);

                    // **Filter only active roles**
                    $activeRoles = array_filter($roles, function ($role) {
                        return isset($role['status']) && strtolower($role['status']) === 'active';
                    });

                    // Reindex the array to ensure proper indexing
                    $activeRoles = array_values($activeRoles);

                    // **Convert each role array to an object**
                    $activeRoles = array_map(function ($role) {
                        return (object) $role;
                    }, $activeRoles);

                    return $activeRoles;
                } else {
                    Log::warning('External API returned failure for roles.', ['message' => Arr::get($apiResponse, 'message')]);
                    return response()->json([
                        'success' => false,
                        'message' => Arr::get($apiResponse, 'message', 'Unknown error from API.'),
                    ], 400);
                }
            } else {
                Log::error('Failed to fetch roles from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch roles from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching roles from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching roles.',
            ], 500);
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
     * Show the User Report Mapping form.
     *
     * @return \Illuminate\View\View
    */
    public function showUserReportMapping()
    { 
        // Fetch roles via API
        $rolesResponse = $this->getRoles();

        // Handle API response
        if (is_array($rolesResponse) && isset($rolesResponse[0]->id)) { // Note the change to ->id
            // Successfully retrieved roles
            $roles = $rolesResponse;
        } elseif ($rolesResponse instanceof \Illuminate\Http\JsonResponse) {
            // An error occurred while fetching roles
            // You can choose to handle the error as needed
            // For example, redirect back with an error message
            return back()->withErrors(['roles_error' => 'Unable to fetch roles at this time. Please try again later.']);
        } else {
            // Unexpected response structure
            return back()->withErrors(['roles_error' => 'Unexpected error while fetching roles.']);
        }

        return view('users.reportMapping', compact('roles'));
        
    }


    public function getEmployeeByRoleId($roleId)
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

        $apiUrl = config('auth_api.employee_by_roleId_url');

        if (!$apiUrl) {
            Log::error('Employee By Role ID API URL not configured.');
            return back()->withErrors(['api_error' => 'Employee By Role ID API URL is not configured.']);
        }

        // Replace {countryId} placeholder with the actual value
        $apiUrl = str_replace('{id}', $roleId, $apiUrl);
        Log::info('Employee By Role ID API from external API.', ['api_url' => $apiUrl]);
       
    
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for States', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

    
            if ($response->successful()) {
                $apiResponse = $response->json();

              //  Log::info('Fetching states for country ID', ['apiResponse' => $apiResponse]);
                return response()->json([
                    'success' => true,
                    'data' => $apiResponse['data'] ?? []
                ]);
            }
    
            Log::error('Failed to fetch Employees', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch Employees.'], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception while fetching Employees', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }


    public function getRoleByDownwardHierarchy($roleId)
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

        $apiUrl = config('auth_api.roles_downward_hierarchy_url');

        if (!$apiUrl) {
            Log::error('Role By Downward Hirarchy ID API URL not configured.');
            return back()->withErrors(['api_error' => 'Role By Downward Hirarchy API URL is not configured.']);
        }

        // Replace {countryId} placeholder with the actual value
        $apiUrl = str_replace('{id}', $roleId, $apiUrl);
        Log::info('Role By Downward Hirarchy API from external API.', ['api_url' => $apiUrl]);
       
    
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for States', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

    
            if ($response->successful()) {
                $apiResponse = $response->json();

              //  Log::info('Fetching states for country ID', ['apiResponse' => $apiResponse]);
                return response()->json([
                    'success' => true,
                    'data' => $apiResponse['data'] ?? []
                ]);
            }
    
            Log::error('Failed to fetch roles', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch roles.'], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception while fetching roles', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

    public function submitUserReportMapping(Request $request)
    {

        Log::info('submitUserReportMapping', ['request' => $request]);
        // Validate all mandatory fields from the mapping form
        $validated = $request->validate([
            'role'           => 'required|exists:roles,id', // Manager Role
            'manager'        => 'required|exists:users,id',
            'employee_role'  => 'required|exists:roles,id',
            'employee'       => 'required|exists:users,id',
        ]);

        // Determine entity_id (for example, from the authenticated user's record)
        $entity_id = Auth::user()->entity_id ?? null;
        $created_by = Auth::id();
        $modified_by = Auth::id();

        // Build the data to post (mapping fields as defined in your migration)
        $postData = [
            'entity_id'         => $entity_id,
            'manager_id'        => $validated['manager'],
            'manager_role_id'   => $validated['role'],
            'employee_id'       => $validated['employee'],
            'employee_role_id'  => $validated['employee_role'],
            'created_by'        => $created_by,
            'modified_by'       => $modified_by,
        ];

        Log::info('submitUserReportMapping from external API.', ['postData' => $postData]);

        // Retrieve the API token from session
        $token = session()->get('api_token');
        if (!$token) {
            return redirect()->route('login')->withErrors(['token_error' => 'Authentication token missing. Please log in again.']);
        }

        // Get the external API URL for creating a mapping (make sure this is set in your config)
        $apiUrl = config('auth_api.user_report_mapping_create_url');
        if (!$apiUrl) {
            return redirect()->back()->withErrors(['api_error' => 'Mapping API URL not configured.']);
        }

        try {
            // Send the data to the external API via POST
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success')) {
                    return redirect()->back()->with('success', 'Mapping created successfully.');
                } else {
                    return redirect()->back()->withErrors(['submit_error' => Arr::get($apiResponse, 'message', 'Unknown error.')])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['submit_error' => 'Failed to connect to the mapping API.'])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['submit_error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }


    public function getUserReportMapping()
    {
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }
        $apiUrl = config('auth_api.user_report_mapping_fetch_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping fetch URL is not configured.'
            ], 500);
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            if ($response->successful()) {
                return response()->json($response->json());
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch mapping data.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred.'
            ], 500);
        }
    }


    public function editUserReportMapping(Request $request)
    {
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Authentication token missing.'], 401);
        }
        $apiUrl = config('auth_api.user_report_mapping_edit_url'); // Set this in your config
        if (!$apiUrl) {
            return response()->json(['success' => false, 'message' => 'Edit API URL not configured.'], 500);
        }
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $request->all());
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }


    public function deleteUserReportMapping(Request $request)
    {
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }
        
        $apiUrl = config('auth_api.user_report_mapping_delete_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping delete URL not configured.'
            ], 500);
        }
        
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $request->all());
            
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }



    /**
     * Show the showWorkLocationMapping form.
     *
     * @return \Illuminate\View\View
    */
    public function showWorkLocationMapping()
    { 

         // Fetch roles via API
         $rolesResponse = $this->getRoles();

         // Handle API response
         if (is_array($rolesResponse) && isset($rolesResponse[0]->id)) { // Note the change to ->id
             // Successfully retrieved roles
             $roles = $rolesResponse;
         } elseif ($rolesResponse instanceof \Illuminate\Http\JsonResponse) {
             // An error occurred while fetching roles
             // You can choose to handle the error as needed
             // For example, redirect back with an error message
             return back()->withErrors(['roles_error' => 'Unable to fetch roles at this time. Please try again later.']);
         } else {
             // Unexpected response structure
             return back()->withErrors(['roles_error' => 'Unexpected error while fetching roles.']);
         }
 

        $countries = \App\Models\Country::all();
        return view('users.workLocationMapping', compact('countries', 'roles'));
        
    }

    public function submitWorkLocationMapping(Request $request)
    {
        $validated = $request->validate([
            'role'       => 'required|exists:roles,id',
            'manager'    => 'required|exists:users,id',
            'country_id' => 'required|exists:countries,id',
            'state_id'   => 'required|exists:states,id',
            'city_id'    => 'required|array',
            'city_id.*'  => 'required|exists:cities,id',
        ]);
    
        // Ensure city_id is always an array (even if only one is selected)
        $cityIds = Arr::wrap($validated['city_id']);
    
        // Retrieve additional details
        $entity_id  = Auth::user()->entity_id ?? null;
        $user_id    = $validated['manager'];
        $created_by = Auth::id();
        $modified_by = Auth::id();
    
        // Build the data payload, passing the entire array for city_id
        $postData = [
            'entity_id'   => $entity_id,
            'user_id'     => $user_id,
            'city_id'     => $cityIds, // Entire array
            'state_id'    => $validated['state_id'],
            'country_id'  => $validated['country_id'],
            'created_by'  => $created_by,
            'modified_by' => $modified_by,
        ];
    
        // Log the post data for debugging
        Log::info('submitWorkLocationMapping payload to external API.', ['postData' => $postData]);
    
        $token = session()->get('api_token');
        $apiUrl = config('auth_api.user_work_location_mapping_create_url');
    
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $postData);
        
            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success')) {
                    return redirect()->back()->with('success', 'Work location mapping(s) created successfully.');
                } else {
                    return redirect()->back()->withErrors(['submit_error' => Arr::get($apiResponse, 'message', 'Unknown error.')])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['submit_error' => 'Failed to connect to the work location mapping API.'])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['submit_error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }


    public function getUserWorkLocationMapping()
    {
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        $apiUrl = config('auth_api.user_work_location_mapping_fetch_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            if ($response->successful()) {
                $apiResponse = $response->json();
                Log::info('getUserWorkLocationMapping response from external API.', ['apiResponse' => $apiResponse]);

                // Simply return the raw data array as received from the API.
                return response()->json([
                    'success' => true,
                    'data'    => Arr::get($apiResponse, 'data', [])
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch mapping data.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching mapping data.'
            ], 500);
        }
    }


    public function submitWorkLocationMappingEdit(Request $request)
    {
        $validated = $request->validate([
            'entity_id'   => 'required|exists:entities,id',
            'user_id'     => 'required|exists:users,id',
            'country_id'  => 'required|exists:countries,id',
            'state_id'    => 'required|exists:states,id',
            'city_id'     => 'required|array',
            'city_id.*'   => 'required|exists:cities,id',
        ]);

        // Retrieve additional details like created_by and modified_by
        $created_by = Auth::id();
        $modified_by = Auth::id();

        // Build the payload including the grouping keys and new array of city IDs
        $postData = [
            'entity_id'   => $validated['entity_id'],
            'user_id'     => $validated['user_id'],
            'country_id'  => $validated['country_id'],
            'state_id'    => $validated['state_id'],
            'city_id'     => $validated['city_id'],  // Array of city IDs
            'created_by'  => $created_by,
            'modified_by' => $modified_by,
        ];

        $token = session()->get('api_token');
        $apiUrl = config('auth_api.user_work_location_mapping_edit_url');
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success')) {
                    return redirect()->back()->with('success', 'User Work location mapping updated successfully.');
                } else {
                    return redirect()->back()->withErrors(['edit_error' => Arr::get($apiResponse, 'message', 'Unknown error.')])->withInput();
                }
            } else {
                return redirect()->back()->withErrors(['edit_error' => 'Failed to connect to the mapping edit API.'])->withInput();
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['edit_error' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }


    public function deleteWorkLocationMapping(Request $request)
    {
        $request->validate([
            'entity_id'   => 'required|integer',
            'user_id'     => 'required|integer',
            'state_id'    => 'required|integer',
            'country_id'  => 'required|integer',
        ]);

        $entity_id = $request->input('entity_id');
        $user_id   = $request->input('user_id');
        $state_id  = $request->input('state_id');
        $country_id = $request->input('country_id');

        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        $apiUrl = config('auth_api.user_work_location_mapping_delete_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Mapping delete URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, [
                'entity_id'   => $entity_id,
                'user_id'     => $user_id,
                'state_id'    => $state_id,
                'country_id'  => $country_id,
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();
                return response()->json($apiResponse);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to the mapping delete API.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


     /**
     * API Endpoint to Fetch All BlooodBanks based on cityIds.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getBloodbanksByCity($cityIds)
    {
        // Retrieve the token from the session
        $token = session()->get('api_token');
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Get the API URL for blood banks (ensure this is set in your config, e.g., config('auth_api.bloodbanks_by_city_url'))
        $apiUrl = config('auth_api.bloodbanks_by_city_url');
        if (!$apiUrl) {
            return response()->json([
                'success' => false,
                'message' => 'Blood banks API URL not configured.'
            ], 500);
        }

        // Replace the {cityIds} placeholder with the provided comma-separated city IDs
        $apiUrl = str_replace('{cityIds}', $cityIds, $apiUrl);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('bloodbanks_by_city apiUrl.', ['apiUrl' => $apiUrl]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                Log::info('bloodbanks_by_city apiResponse.', ['apiResponse' => $apiResponse]);

                return response()->json([
                    'success' => true,
                    'data'    => $apiResponse['data'] ?? []
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch blood banks.'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }


    public function getCitiesByMultipleStateId($stateId)
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

        $apiUrl = config('auth_api.cities_by_multiple_stateIds_url');
       

        if (!$apiUrl) {
            Log::error('Cities API URL not configured.');
            return back()->withErrors(['api_error' => 'Cities API URL is not configured.']);
        }

        // Replace {stateId} placeholder with the actual value
     
        $apiUrl = str_replace('{id}', $stateId, $apiUrl);
        // Log::info('Cities API Multiple STate Ids.', ['api_url' => $apiUrl]);
       
    
        try {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ])->get($apiUrl);

            Log::info('External API Response for Cities', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

    
            if ($response->successful()) {
                $apiResponse = $response->json();

               // Log::info('Fetching cities for state ID', ['apiResponse' => $apiResponse]);
                return response()->json([
                    'success' => true,
                    'data' => $apiResponse['data'] ?? []
                ]);
            }
    
            Log::error('Failed to fetch cities', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return response()->json(['success' => false, 'message' => 'Failed to fetch cities.'], $response->status());
        } catch (\Exception $e) {
            Log::error('Exception while fetching cities', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'An error occurred.'], 500);
        }
    }

}
