<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth; 

class ReportVisitsController extends Controller
{
   
    /**
     * Show the visits list page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('visits.index');
    }

     /**
     * Show the detailed view for a specific date.
     *
     * @param  string  $date
     * @return \Illuminate\View\View|\Illuminate\Http\JsonResponse
     */
    public function showView($date)
    {
        // Validate the date format
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return redirect()->back()->with('error', 'Invalid date format.');
        }

        return view('visits.view', compact('date'));
    }

    /**
     * Fetch visit data for a specific date via API.
     *
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */
    public function fetchVisits($date)
    {
        // Validate the date format
        if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid date format.'
            ], 400);
        }

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
        $apiUrl = config('auth_api.visits_per_day_all_url');

        if (!$apiUrl) {
            Log::error('Day Visits fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'Day Visits fetch URL is not configured.'
            ], 500);
        }

        $queryParams['date'] = $date;

        Log::info('Fetch visits request API', [
            'api_url' => $apiUrl,
            'data' => $queryParams,
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, $queryParams);

            Log::info('External API Response', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if (Arr::get($apiResponse, 'success')) {
                    // Ensure only 'success' and 'data' are returned
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
                Log::error('Failed to fetch day visits from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch day visits from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching day visits from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching day visits.',
            ], 500);
        }
    }


    /**
     * Fetch visit data for a specific date via API.
     *
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
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

            Log::info('Entity Features Settings Fetch API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
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
                Log::error('Failed to fetch entity features settings from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch entity features settings from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching entity features settings from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching entity features settings.',
            ], 500);
        }
    }


    // Update visit details
    public function updateVisit(Request $request)
    {
       // Optional: Validate that the visit_id from the request matches the {id} parameter
        if (!$request->input('visit_id')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Visit ID.'
            ], 400);
        }

         // Validate the form data with conditional rules
         $validatedData = $request->validate([
            'quantity_collected' => 'required|integer|min:0',
            'remaining_quantity' => 'required|integer|min:0',
            'quantity_price'     => 'nullable|numeric|min:0',
            'certificate_of_quality' => 'nullable|array',
            'certificate_of_quality.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'donor_report' => 'nullable|array',
            'donor_report.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'invoice_copy' => 'nullable|array',
            'invoice_copy.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'pending_documents' => 'nullable|array',
            'pending_documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'user_latitude' => 'nullable|numeric',
            'user_longitude' => 'nullable|numeric',
        ]);


        // Log the data being sent
        Log::info('Update Collection DCR API validatedData', [
            'validatedData' => $validatedData,
        ]);
      
        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

         // Prepare the data to send to the external API
         $postData = [
            'visit_id'              => $request->input('visit_id'),
            'quantity_collected'    => $request->input('quantity_collected'),  
            'remaining_quantity'    => $request->input('remaining_quantity'),
            'quantity_price'        => $request->input('quantity_price'),
            'user_latitude'         => $request->input('user_latitude'),
            'user_longitude'        => $request->input('user_longitude'),
            'created_by'            => Auth::id(), // Assuming you want to capture the authenticated user
            'modified_by'           => Auth::id(),
        ];
        
        // Handle multiple documents certificate_of_quality
        if ($request->hasFile('certificate_of_quality')) {
            $postData['certificate_of_quality'] = []; // Initialize as an array
            foreach ($request->file('certificate_of_quality') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['certificate_of_quality'][] = $documentBase64;
            }
        }

        // Handle multiple documents donor_report
        if ($request->hasFile('donor_report')) {
            $postData['donor_report'] = []; // Initialize as an array
            foreach ($request->file('donor_report') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['donor_report'][] = $documentBase64;
            }
        }


        // Handle multiple documents invoice_copy
        if ($request->hasFile('invoice_copy')) {
            $postData['invoice_copy'] = []; // Initialize as an array
            foreach ($request->file('invoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['invoice_copy'][] = $documentBase64;
            }
        }

        // Handle multiple documents invoice_copy
        if ($request->hasFile('pending_documents')) {
            $postData['pending_documents'] = []; // Initialize as an array
            foreach ($request->file('pending_documents') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['pending_documents'][] = $documentBase64;
            }
        }
        

        // Define the external API URL for saving the tour plan
        $apiUrl = config('auth_api.drc_collections_submit_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('DCR Collections Submit URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'DCR Collections Submit URL is not configured.'
            ], 500);
        }

        try {

    
            // Log the data being sent
            Log::info('Sending data to DCR Collections Submit API', [
                'data' => $postData,
            ]);

           
            // Make the API request with the Bearer token
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('DCR Collections Submit API Response', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);


            if ($response->successful()) {
                $apiResponse = $response->json();

                if ($apiResponse['success']) {
                    return response()->json([
                        'success' => true,
                        'message' => $apiResponse['message'],
                        'data'    => $apiResponse['data'], // Optional: include data if needed
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $apiResponse['message'] ?? 'Unknown error from API.'
                    ], 400);
                }
            } else {
                // Log the HTTP status code and response body
                Log::error('DCR Collections Submit API failed', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
    
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to the update server.'
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Log the exception message
            Log::error('DCR Collections Submit exception', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the visit: ' . $e->getMessage()
            ], 500);
        }
    }


      // sourcing DCR Submit 
      public function sourcingDCRSubmit(Request $request)
      {
         // Optional: Validate that the visit_id from the request matches the {id} parameter
          if (!$request->input('visit_id')) {
              return response()->json([
                  'success' => false,
                  'message' => 'Invalid Visit ID.'
              ], 400);
          }
  
            // Validate the form data with appropriate rules
            $validatedData = $request->validate([
                'blood_bank_name'           => 'required|string|max:255',
                'contact_person_name'       => 'required|string|max:255',
                'mobile_no'                 => 'required|digits:10',
                'email'                     => 'nullable|email|max:255',
                'address'                   => 'nullable|string|max:500',
                'FFPProcurementCompany'     => 'nullable|string|max:255',
                'currentPlasmaPrice'        => 'nullable|numeric|min:0',
                'potentialPerMonth'         => 'nullable|string|max:255',
                'paymentTerms'              => 'nullable|string|max:255',
                'remarks'                   => 'nullable|string|max:1000',
                'sourcing_user_latitude'    => 'nullable|numeric',
                'sourcing_user_longitude'   => 'nullable|numeric',
            ]);
  
  
          // Log the data being sent
          Log::info('Update Collection DCR API validatedData', [
              'validatedData' => $validatedData,
          ]);
        
          // Retrieve the token from the session
          $token = session()->get('api_token');
  
          if (!$token) {
              Log::warning('API token missing in session.');
              return response()->json([
                  'success' => false,
                  'message' => 'Authentication token missing. Please log in again.'
              ], 401);
          }
  
            // Prepare the data to send to the external API
            $postData = [
                'visit_id'              => $request->input('visit_id'),
                'blood_bank_name'       => $validatedData['blood_bank_name'],
                'contact_person_name'   => $validatedData['contact_person_name'],
                'mobile_no'             => $validatedData['mobile_no'],
                'email'                 => $validatedData['email'],
                'address'               => $validatedData['address'] ?? '',
                'FFP_procurement_company' => $validatedData['FFPProcurementCompany'],
                'current_plasma_price'  => $validatedData['currentPlasmaPrice'],
                'potential_per_month'   => $validatedData['potentialPerMonth'],
                'payment_terms'         => $validatedData['paymentTerms'],
                'remarks'               => $validatedData['remarks'] ?? '',
                'user_latitude'         => $validatedData['sourcing_user_latitude'] ?? null,
                'user_longitude'        => $validatedData['sourcing_user_longitude'] ?? null,
                'created_by'            => Auth::id(), // Assuming you want to capture the authenticated user
                'modified_by'           => Auth::id(),
            ];

  
          // Define the external API URL for saving the tour plan
          $apiUrl = config('auth_api.drc_sourcing_submit_url'); // Ensure this is set in your config
  
          if (!$apiUrl) {
                Log::error('DCR Sourcing Submit URL not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'DCR Sourcing Submit URL is not configured.'
                ], 500);
          }
  
          try {
              // Log the data being sent
              Log::info('Sending data to DCR Sourcing Submit API', [
                    'api_url' => $apiUrl,
                    'data'    => $postData,
              ]);
  
             
              // Make the API request with the Bearer token
              $response = Http::withHeaders([
                  'Authorization' => 'Bearer ' . $token,
                  'Content-Type' => 'application/json',
              ])->post($apiUrl, $postData);
  
                // Log the API response
                Log::info('DCR Sourcing Submit API Response', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
  
  
              if ($response->successful()) {
                  $apiResponse = $response->json();
  
                  if ($apiResponse['success']) {
                      return response()->json([
                          'success' => true,
                          'message' => $apiResponse['message'],
                          'data'    => $apiResponse['data'] ?? null, // Optional: include data if needed
                      ]);
                  } else {
                      return response()->json([
                          'success' => false,
                          'message' => $apiResponse['message'] ?? 'Unknown error from API.'
                      ], 400);
                  }
              } else {
                  // Log the HTTP status code and response body
                  Log::error('DCR Sourcing Submit API failed', [
                      'status' => $response->status(),
                      'body'   => $response->body(),
                  ]);
      
                  return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to the DCR Sourcing Submit server.'
                ], $response->status());
              }
          } catch (\Exception $e) {
              // Log the exception message
              Log::error('DCR Sourcing Submit exception', ['error' => $e->getMessage()]);
              return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the sourcing DCR: ' . $e->getMessage()
            ], 500);
          }
      }


       // Final DCR Submit 
       public function finalDCRsubmit(Request $request)
       {
    
             // Validate the form data with appropriate rules
             $validatedData = $request->validate([
                 'visit_date'              => 'required|date',
             ]);

            // Fetch the visit date from the validated data and the user_id from the authenticated user.
            $visit_date = $validatedData['visit_date'];
            $user_id    = Auth::id();
   
   
            // Log the validated data for debugging.
            Log::info('Final DCR submission validatedData', [
                'visit_date' => $visit_date,
                'user_id' => $user_id,
            ]);

           // Retrieve the token from the session
           $token = session()->get('api_token');
   
           if (!$token) {
               Log::warning('API token missing in session.');
               return response()->json([
                   'success' => false,
                   'message' => 'Authentication token missing. Please log in again.'
               ], 401);
           }
   
             // Prepare the data to send to the external API
             $postData = [
                 'visit_date'            => $visit_date,
                 'user_id'               => $user_id,
                 'created_by'            => Auth::id(), // Assuming you want to capture the authenticated user
                 'modified_by'           => Auth::id(),
             ];
 
   
           // Define the external API URL for saving the tour plan
           $apiUrl = config('auth_api.final_dcr_submit_url'); // Ensure this is set in your config
   
           if (!$apiUrl) {
                 Log::error('Final DCR Submit URL not configured.');
                 return response()->json([
                     'success' => false,
                     'message' => 'Final DCR Submit URL is not configured.'
                 ], 500);
           }
   
           try {
               // Log the data being sent
               Log::info('Sending data to Final DCR Submit API', [
                     'api_url' => $apiUrl,
                     'data'    => $postData,
               ]);
   
              
               // Make the API request with the Bearer token
               $response = Http::withHeaders([
                   'Authorization' => 'Bearer ' . $token,
                   'Content-Type' => 'application/json',
               ])->post($apiUrl, $postData);
   
                 // Log the API response
                 Log::info('Final DCR Submit API Response', [
                     'status' => $response->status(),
                     'body'   => $response->body(),
                 ]);
   
   
               if ($response->successful()) {
                   $apiResponse = $response->json();
   
                   if ($apiResponse['success']) {
                       return response()->json([
                           'success' => true,
                           'message' => $apiResponse['message'],
                           'data'    => $apiResponse['data'] ?? null, // Optional: include data if needed
                       ]);
                   } else {
                       return response()->json([
                           'success' => false,
                           'message' => $apiResponse['message'] ?? 'Unknown error from API.'
                       ], 400);
                   }
               } else {
                   // Log the HTTP status code and response body
                   Log::error('Final DCR Submit API failed', [
                       'status' => $response->status(),
                       'body'   => $response->body(),
                   ]);
       
                   return response()->json([
                     'success' => false,
                     'message' => 'Failed to connect to the Final DCR Submit server.'
                 ], $response->status());
               }
           } catch (\Exception $e) {
               // Log the exception message
               Log::error('Final DCRSubmit exception', ['error' => $e->getMessage()]);
               return response()->json([
                 'success' => false,
                 'message' => 'An error occurred while submitting the Final DCR: ' . $e->getMessage()
             ], 500);
           }
       }
}