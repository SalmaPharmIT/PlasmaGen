<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class CityMasterController extends Controller
{

    /**
     * Show the entities list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        $states = \App\Models\State::all();
        return view('citymaster.index', compact('states'));
    }

    public function getCities()
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
        $apiUrl = config('auth_api.getAllCities_url');

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
     * Store a newly created city in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate incoming request
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'state_id' => 'required|exists:states,id',
            'pin_code' => 'required|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            // Add other fields as necessary
        ]);

        // Retrieve the token from the session
        $token = $request->session()->get('api_token');

        if (!$token) {
            // Log the missing token
            Log::warning('API token missing in session.');

            // Redirect back with error message
            return redirect()->back()->withErrors(['Authentication token missing. Please log in again.'])->withInput();
        }

        // Prepare the data to send to the external API
        $postData = [
            'name'      => trim($validatedData['name']), // Convert to lowercase and trim
            'state_id'  => intval($validatedData['state_id']),
            'pin_code'  => trim($validatedData['pin_code']),
            'latitude'  => isset($validatedData['latitude']) ? floatval($validatedData['latitude']) : null,
            'longitude' => isset($validatedData['longitude']) ? floatval($validatedData['longitude']) : null,
            // Add other fields as necessary
        ];

        // Define the external API URL
        $apiUrl = config('auth_api.add_city_url'); // Ensure this is correctly set in config/auth_api.php

        try {
            // Log the data being sent
            Log::info('Sending data to City Add API', [
                'data' => $postData,
            ]);

            // Make the API request with the Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('City Add API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    // Retrieve the created city data from the API response
                    $cityData = Arr::get($apiResponse, 'data');

                    // Optionally, format the city name back to title case for display
                    if (isset($cityData['name'])) {
                        $cityData['name'] = ucwords($cityData['name']);
                    }

                    // Optionally, save the city locally if needed
                    // City::create($cityData);

                    return redirect()->route('citymaster.index')->with('success', 'City created successfully.');
                } else {
                    // Log the failure message from the API
                    Log::warning('City creation failed via API.', ['message' => Arr::get($apiResponse, 'message')]);

                    return redirect()->back()->withErrors([Arr::get($apiResponse, 'message', 'Failed to create city. Please try again.')])->withInput();
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('City Add API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return redirect()->back()->withErrors(['Failed to create city. Please try again.'])->withInput();
            }

        } catch (\Exception $e) {
            Log::error('Error creating city: ' . $e->getMessage());

            return redirect()->back()->withErrors(['Failed to create city. Please try again.'])->withInput();
        }
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name'      => 'required|string|max:255',
            'state_id'  => 'required|exists:states,id',
            'pin_code'  => 'required|string|max:10',
            'latitude'  => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $token = $request->session()->get('api_token');
        if (!$token) {
            return redirect()->back()->withErrors(['Authentication token missing. Please log in again.']);
        }

        $postData = [
            'id'        => $id,
            'name'      => trim($validatedData['name']),
            'state_id'  => intval($validatedData['state_id']),
            'pin_code'  => trim($validatedData['pin_code']),
            'latitude'  => isset($validatedData['latitude']) ? floatval($validatedData['latitude']) : null,
            'longitude' => isset($validatedData['longitude']) ? floatval($validatedData['longitude']) : null,
        ];

        $apiUrl = config('auth_api.update_city_url') . '/' . $id;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type'  => 'application/json',
            ])->put($apiUrl, $postData);

            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success')) {
                    return redirect()->route('citymaster.index')->with('success', 'City updated successfully.');
                } else {
                    return redirect()->back()->withErrors([Arr::get($apiResponse, 'message', 'Failed to update city.')]);
                }
            } else {
                return redirect()->back()->withErrors(['Failed to update city. Please try again.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['Failed to update city. Please try again.']);
        }
    }

    public function destroy(Request $request, $id)
    {
        $token = $request->session()->get('api_token');
        if (!$token) {
            return response()->json(['success' => false, 'message' => 'Authentication token missing.'], 401);
        }

        $apiUrl = config('auth_api.delete_city_url') . '/' . $id;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->delete($apiUrl, ['id' => $id]);
            
            if ($response->successful()) {
                $apiResponse = $response->json();
                if (Arr::get($apiResponse, 'success')) {
                    return response()->json(['success' => true, 'message' => 'City deleted successfully.']);
                } else {
                    return response()->json(['success' => false, 'message' => Arr::get($apiResponse, 'message', 'Failed to delete city.')]);
                }
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to delete city.'], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'An error occurred while deleting the city.'], 500);
        }
    }

}
