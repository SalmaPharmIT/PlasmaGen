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
        $roles = \App\Models\Role::all();
        $user = \App\Models\User::all();
        $userStatuses = \App\Models\User::$userStatuses;
        $userGender = \App\Models\User::$userGender;

        // Log::info('Fetching userStatuses.', ['userStatuses' => $userStatuses]);

        return view('users.register', compact('entity', 'countries', 'states', 'cities', 'roles', 'user', 'userStatuses', 'userGender'));
        
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
           // 'account_status' => 'required|in:active,inactive,suspended',
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
            'gender'                => $request->input('gender'),
            'mobile_no'            => $request->input('mobile_no'),
            'email'              => $request->input('email'),
            'dob'               => $request->input('dob'),
            'pan_id'               => $request->input('pan_id'),
            'aadhar_id'               => $request->input('aadhar_id'),
           // 'account_status'            => $request->input('account_status'),
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

                    // Fetch related data for dropdowns
                    $entities = \App\Models\Entity::all();
                    $countries = \App\Models\Country::all();
                    $states = \App\Models\State::all();
                    $cities = \App\Models\City::all();
                    $roles = \App\Models\Role::all();
                 //   $user = \App\Models\User::all();
                    $userStatuses = \App\Models\User::$userStatuses;
                    $userGender = \App\Models\User::$userGender;   

                    Log::info('Fetching single user data from external API.', ['user' => $user]);

                    return view('users.edit', compact('user','entities', 'countries', 'states', 'cities', 'roles',  'userStatuses', 'userGender'));
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
       // Log::info('Cities API from external API.', ['api_url' => $apiUrl]);
       
    
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
