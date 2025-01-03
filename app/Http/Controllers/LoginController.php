<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http; // Import the HTTP client
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;
use App\Models\Entity;
use App\Models\Token;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LoginController extends Controller
{
    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function login()
    {
        return view('login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function loginPost(Request $request)
    {

        // Validate input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);


         // Define credentials
         $credentials = [
            'username' => $request->input('username'),
            'password' => $request->input('password'),
        ];

        try {
            // Call external API
           // $response = Http::post('http://127.0.0.1/PlasmaGenAPIs/api/login.php', $credentials);

            // Retrieve the API URL from the configuration
            $apiUrl = config('auth_api.login_url');
            // Call external API with JSON data
            $response = Http::post($apiUrl, $credentials);

            if ($response->successful()) {
                $data = $response->json();

                if ($data['success']) {
                    // Assuming the API returns user details
                    $externalUser = $data['user'];
                    $token = $data['token'];

                    // Sync or create the user in the local database
                    $user = User::updateOrCreate(
                        ['username' => $externalUser['username']],
                        [
                            'name' => $externalUser['name'],
                            'username' => $externalUser['username'],
                            'role_id' => $externalUser['role_id'],
                            'role_name' => $externalUser['role_name'],
                            'entity_id' => $externalUser['entity_id'],
                            'entity_name' => $externalUser['entity_name'],
                            'email' => $externalUser['email'] ?? null, // Handle if email is provided
                            'password' => null,  // Since authentication is handled externally
                            'mobile' => $externalUser['mobile'] ?? null,
                            'gender' => $externalUser['gender'] ?? null,
                            'account_status' => $externalUser['account_status'] ?? null,
                            'token' => $data['token'],
                            'profile_pic' => $externalUser['profile_pic'] ?? null,
                            // Add other fields as necessary
                        ]
                    );

                    // Fetch the Role based on role_id
                    $role = Role::find($externalUser['role_id']);
                    if (!$role) {
                        // Log the error
                        Log::warning('User role not found', ['role_id' => $externalUser['role_id']]);

                        // Handle unknown role, perhaps assign a default role or return an error
                        return back()->withErrors(['role_error' => 'User role not recognized.'])->withInput();
                    }

                    // Fetch the Entity based on entity_id
                    $entity = Entity::find($externalUser['entity_id']);
                    if (!$entity) {
                        // Log the error
                        Log::warning('User entity not found', ['entity_id' => $externalUser['entity_id']]);

                        // Handle unknown entity, perhaps assign a default entity or return an error
                        return back()->withErrors(['entity_error' => 'User entity not recognized.'])->withInput();
                    }

                    // Log in the user
                    Auth::login($user, $request->filled('remember'));

                    // Regenerate session to prevent fixation
                    $request->session()->regenerate();

                    // Log the successful login
                    Log::info('User logged in', ['username' => $externalUser['username']]);
                    Log::info('User logged in using token', ['token' => $token]);

                    // Save the plain token in the session
                    $request->session()->put('api_token', $token);


                    // Redirect to dashboard
                    return redirect()->intended(route('dashboard'));
                } else {
                     // Log the failed login attempt
                     Log::warning('Failed login attempt', ['username' => $credentials['username'], 'message' => $data['message']]);

                    return back()->withErrors(['invalid_credentials' => $data['message']])->withInput();
                }
            } else {

                  // Log the API connection failure
                  Log::error('API connection failed', ['status' => $response->status()]);

                return back()->withErrors(['api_error' => 'Unable to connect to the authentication server.'])->withInput();
            }
        } catch (\Exception $e) {

             // Log the exception
             Log::error('Authentication exception', ['error' => $e->getMessage()]);

            // Handle exceptions (e.g., connection errors)
            return back()->withErrors(['exception' => 'An error occurred: ' . $e->getMessage()])->withInput();
        }
    }
    
    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

}
