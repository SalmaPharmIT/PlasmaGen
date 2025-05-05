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
            'collectionUpdatePartAPrice'     => 'nullable|numeric|min:0',
            'collectionUpdatePartBPrice'     => 'nullable|numeric|min:0',
            'collectionUpdatePartCPrice'     => 'nullable|numeric|min:0',
            'boxes_collected' => 'required|integer|min:0',
            'units_collected' => 'required|integer|min:0',
            'litres_collected' => 'required|integer|min:0',
            'different_transport_partner' => 'nullable',
            'transportation_name' => 'nullable|string',
            'transportation_contact_person' => 'nullable|string',
            'transportation_contact_number' => 'nullable|string',
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
            'collectionUpdatePartAPrice'        => $request->input('collectionUpdatePartAPrice'),
            'collectionUpdatePartBPrice'        => $request->input('collectionUpdatePartBPrice'),
            'collectionUpdatePartCPrice'        => $request->input('collectionUpdatePartCPrice'),
            'collection_total_plasma_price'      => $request->input('total_collection_price'),
            'include_collection_gst'      => $request->has('include_collection_gst') ? 1 : 0,
            'collection_gst_rate'         => $request->input('collection_gst_rate'),
            'boxes_collected'    => $request->input('boxes_collected'),  
            'units_collected'    => $request->input('units_collected'),  
            'litres_collected'    => $request->input('litres_collected'),  
             // Additional transportation details:
            'different_transport_partner' => $request->has('different_transport_partner') ? 1 : 0,
            'transportation_name'         => $request->input('transportation_name'),
            'transportation_contact_person' => $request->input('transportation_contact_person'),
            'transportation_contact_number' => $request->input('transportation_contact_number'),
        ];
        
        // Handle multiple documents certificate_of_quality
        if ($request->hasFile('certificate_of_quality')) {
            $files = $request->file('certificate_of_quality');
            Log::info('Number of files certificate_of_quality uploaded:', ['count' => count($files)]);
            $postData['certificate_of_quality'] = []; // Initialize as an array
            foreach ($request->file('certificate_of_quality') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['certificate_of_quality'][] = $documentBase64;
            }
        }

        // Handle multiple documents donor_report
        if ($request->hasFile('donor_report')) {
            $files2 = $request->file('donor_report');
            Log::info('Number of files donor_report uploaded:', ['count' => count($files2)]);
            $postData['donor_report'] = []; // Initialize as an array
            foreach ($request->file('donor_report') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['donor_report'][] = $documentBase64;
            }
        }


        // Handle multiple documents invoice_copy
        if ($request->hasFile('invoice_copy')) {
            $files3 = $request->file('invoice_copy');
            Log::info('Number of files invoice_copy uploaded:', ['count' => count($files3)]);
            $postData['invoice_copy'] = []; // Initialize as an array
            foreach ($request->file('invoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['invoice_copy'][] = $documentBase64;
            }
        }

        // Handle multiple documents pending_documents
        if ($request->hasFile('pending_documents')) {
            $files4 = $request->file('pending_documents');
            Log::info('Number of files pending_documents uploaded:', ['count' => count($files4)]);
            $postData['pending_documents'] = []; // Initialize as an array
            foreach ($request->file('pending_documents') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['pending_documents'][] = $documentBase64;
            }
        }

        // Handle multiple documents collectionPartAInvoice_copy
        if ($request->hasFile('collectionPartAInvoice_copy')) {
            $files5 = $request->file('collectionPartAInvoice_copy');
            Log::info('Number of files collectionPartAInvoice_copy uploaded:', ['count' => count($files5)]);
            $postData['collectionPartAInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartAInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartAInvoice_copy'][] = $documentBase64;
            }
        }

        // Handle multiple documents collectionPartBInvoice_copy
        if ($request->hasFile('collectionPartBInvoice_copy')) {
            $files6 = $request->file('collectionPartBInvoice_copy');
            Log::info('Number of files collectionPartBInvoice_copy uploaded:', ['count' => count($files6)]);
            $postData['collectionPartBInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartBInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartBInvoice_copy'][] = $documentBase64;
            }
        }

         // Handle multiple documents collectionPartCInvoice_copy
         if ($request->hasFile('collectionPartCInvoice_copy')) {
            $files7 = $request->file('collectionPartCInvoice_copy');
            Log::info('Number of files collectionPartCInvoice_copy uploaded:', ['count' => count($files7)]);
            $postData['collectionPartCInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartCInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartCInvoice_copy'][] = $documentBase64;
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
            $response = Http::timeout(120)->connectTimeout(30)
            ->withHeaders([
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
                'mobile_no'                 => 'required|digits_between:10,20',
                'email'                     => 'nullable|email|max:255',
                'address'                   => 'nullable|string|max:500',
                'FFPProcurementCompany'     => 'nullable|string|max:255',
                'currentPlasmaPrice'        => 'nullable|numeric|min:0',
                'potentialPerMonth'         => 'nullable|string|max:255',
                'paymentTerms'              => 'nullable|string|max:255',
                'remarks'                   => 'nullable|string|max:1000',
                'sourcing_user_latitude'    => 'nullable|numeric',
                'sourcing_user_longitude'   => 'nullable|numeric',
                 // New extra fields
                'part_a_price'              => 'nullable|numeric|min:0',
                'part_b_price'              => 'nullable|numeric|min:0',
                'part_c_price'              => 'nullable|numeric|min:0',
                'include_gst'               => 'sometimes|accepted', // if checkbox is ticked, its value should be "on" or "1"
                'gst_rate'                  => 'nullable|numeric',
                'total_plasma_price'        => 'nullable|numeric|min:0'
            ]);
  
            // Convert the 'include_gst' field to 1 or 0
           // $includeGST = $request->has('include_gst') && $request->input('include_gst') === 'on' ? 1 : 0;

  
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
                 // Extra fields
                'part_a_price'            => $validatedData['part_a_price'] ?? 0,
                'part_b_price'            => $validatedData['part_b_price'] ?? 0,
                'part_c_price'            => $validatedData['part_c_price'] ?? 0,
                'include_gst'             => $request->has('include_gst') ? 1 : 0,
                'gst_rate'                => $validatedData['gst_rate'] ?? 0,
                'total_plasma_price'      => $validatedData['total_plasma_price'] ?? 0,
            ];

             // Log the data being sent
            Log::info('Sourcing  DCR postData API validatedData', [
                'validatedData' => $validatedData,
            ]);

  
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
                        if ($apiResponse['message'] === "DCR already submitted.") {
                            return response()->json([
                                'success' => false,
                                'message' => "DCR already submitted."
                            ], 200);
                        }
                        return response()->json([
                            'success' => false,
                            'message' => $apiResponse['message'] ?? 'Unknown error from API.'
                        ], 200); // You can change this status if needed.
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


    /**
     * API Endpoint to Fetch Employee TP Status Lists.
     *
     * @return \Illuminate\Http\JsonResponse
    */
    public function getEmployeesTPStatus(Request $request)
    {
        // Retrieve the auth user id from the query parameter (if needed)
        $employeeId = $request->input('auth_user_id');
        $selectedMonth = $request->input('selectedMonth');
        Log::info('Received auth_user_id for employee TP Status fetch', ['employeeId' => $employeeId, 'selectedMonth' => $selectedMonth]);

        // Retrieve the token from the session
        $token = session()->get('api_token');

        if (!$token) {
            Log::warning('API token missing in session.');
            return response()->json([
                'success' => false,
                'message' => 'Authentication token missing. Please log in again.'
            ], 401);
        }

        // Define the external API URL for fetching TP Status
        $apiUrl = config('auth_api.employee_tp_status_url');

        if (!$apiUrl) {
            Log::error('TP Status fetch URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'TP Status fetch URL is not configured.'
            ], 500);
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->get($apiUrl, ['employeeId' => $employeeId, 'month' => $selectedMonth]); // Pass auth_user_id to external API

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
                Log::error('Failed to fetch TP Status from external API.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch TP Status from the external API.',
                ], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Exception while fetching TP Status from external API.', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching TP Status.',
            ], 500);
        }
    }


     /**
     * Fetch core blood banks data for a specific date via API.
     *
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */

     public function getCoreSourcingBloodBanks(Request $request)
     {
        $visitId = $request->input('visit_id');
        Log::info('Fetching core sourcing blood banks for visit:', ['visit_id' => $visitId]);

         // Retrieve the token from the session
         $token = session()->get('api_token');
 
         if (!$token) {
             Log::warning('API token missing in session.');
             return redirect()->route('login')->withErrors(['token_error' => 'Authentication token missing. Please log in again.']);
         }
 
         // Define the external API URL for fetching Core Sourcing Blood Banks
         $apiUrl = config('auth_api.core_sourcing_bloodbanks_fetch_url');
 
         if (!$apiUrl) {
             Log::error('Core Sourcing Blood Banks Fetch URL not configured.');
             return back()->withErrors(['api_error' => 'Core Sourcing Blood Banks Fetch URL is not configured.']);
         }
 
         try {
             // Make the API request to fetch Core Sourcing Blood Banks
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . session('api_token'),
                'Accept'        => 'application/json',
            ])->get($apiUrl, [
                'visit_id' => $visitId,   // ← forward it here
            ]);

            Log::info('Core Blood Banks API call', [
                'url'    => $apiUrl,
                'params' => ['visit_id' => $visitId],
                'status' => $response->status(),
            ]);    
 
             Log::info('Core Sourcing Blood Banks Fetch API Response', [
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
                 Log::error('Failed to fetch Core Sourcing Blood Banks from external API.', [
                     'status' => $response->status(),
                     'body' => $response->body(),
                 ]);
                 return response()->json([
                     'success' => false,
                     'message' => 'Failed to fetch Core Sourcing Blood Banks from the external API.',
                 ], $response->status());
             }
         } catch (\Exception $e) {
             Log::error('Exception while fetching Core Sourcing Blood Banks from external API.', ['error' => $e->getMessage()]);
             return response()->json([
                 'success' => false,
                 'message' => 'An error occurred while fetching Core Sourcing Blood Banks.',
             ], 500);
         }
     }

     /**
     * Fetch GST Rates data for a specific date via API.
     *
     * @param  string  $date
     * @return \Illuminate\Http\JsonResponse
     */

     public function getSourcingGSTRates()
     {
         // Retrieve the token from the session
         $token = session()->get('api_token');
 
         if (!$token) {
             Log::warning('API token missing in session.');
             return redirect()->route('login')->withErrors(['token_error' => 'Authentication token missing. Please log in again.']);
         }
 
         // Define the external API URL for fetching GST Rates
         $apiUrl = config('auth_api.gst_rates_fetch_url');
 
         if (!$apiUrl) {
             Log::error('GST Rates Fetch URL not configured.');
             return back()->withErrors(['api_error' => 'GST Rates Fetch URL is not configured.']);
         }
 
         try {
             // Make the API request to fetch GST Rates
             $response = Http::withHeaders([
                 'Authorization' => 'Bearer ' . $token,
                 'Accept'        => 'application/json',
             ])->get($apiUrl);
 
             Log::info('GST Rates Fetch API Response', [
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
                 Log::error('Failed to fetch GST Rates from external API.', [
                     'status' => $response->status(),
                     'body' => $response->body(),
                 ]);
                 return response()->json([
                     'success' => false,
                     'message' => 'Failed to fetch GST Rates from the external API.',
                 ], $response->status());
             }
         } catch (\Exception $e) {
             Log::error('Exception while fetching GST Rates from external API.', ['error' => $e->getMessage()]);
             return response()->json([
                 'success' => false,
                 'message' => 'An error occurred while fetching GST Rates.',
             ], 500);
         }
     }


    // Edit Colllection Visits  details
    public function collectionEditSubmit(Request $request)
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
            'quantity_remaining' => 'required|integer|min:0',
            'editPrice'     => 'nullable|numeric|min:0',
            'certificate_of_quality' => 'nullable|array',
            'certificate_of_quality.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'donor_report' => 'nullable|array',
            'donor_report.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'invoice_copy' => 'nullable|array',
            'invoice_copy.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'pending_documents' => 'nullable|array',
            'pending_documents.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx|max:2048',
            'edit_part_a_price'     => 'nullable|numeric|min:0',
            'edit_part_b_price'     => 'nullable|numeric|min:0',
            'edit_part_c_price'     => 'nullable|numeric|min:0',
            'edit_boxes_collected' => 'required|integer|min:0',
            'edit_units_collected' => 'required|integer|min:0',
            'edit_litres_collected' => 'required|integer|min:0',
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
            'visit_id' => $request->input('visit_id'),
            'quantity_collected' => $request->input('quantity_collected'),
            'remaining_quantity' => $request->input('quantity_remaining'),
            'quantity_price' => $request->input('editPrice'),
            'created_by' => Auth::id(),
            'modified_by' => Auth::id(),
            'collectionUpdatePartAPrice' => $request->input('edit_part_a_price'),
            'collectionUpdatePartBPrice' => $request->input('edit_part_b_price'),
            'collectionUpdatePartCPrice' => $request->input('edit_part_c_price'),
            'collection_total_plasma_price' => $request->input('edit_total_price'),
            'include_collection_gst' => $request->has('edit_collection_include_gst') ? 1 : 0,
            'collection_gst_rate' => $request->input('edit_collection_gst_rate'),
            'boxes_collected' => $request->input('edit_boxes_collected'),
            'units_collected' => $request->input('edit_units_collected'),
            'litres_collected' => $request->input('edit_litres_collected'),
            'existing_attachments' => $request->input('existing_attachments', '[]'),
            'different_transport_partner' => $request->has('edit_collection_other_transport_partner') ? 1 : 0,
            'transportation_name' => $request->input('edit_transport_name'),
            'transportation_contact_person' => $request->input('edit_transport_contact_person'),
            'transportation_contact_number' => $request->input('edit_transport_contact_number'),
        ];
        
          // Retrieve existing attachments (if any)
   

        // Handle multiple Retrieve existing attachments (if any)
        $existingAttachmentsJson = $request->input('existing_attachments', '[]');
        $existingAttachments = json_decode($existingAttachmentsJson, true);
        Log::info('Number of existing attachments:', ['count' => count($existingAttachments)]);


        // Handle multiple documents certificate_of_quality
        if ($request->hasFile('certificate_of_quality')) {
            $files = $request->file('certificate_of_quality');
            Log::info('Number of files certificate_of_quality uploaded:', ['count' => count($files)]);
            $postData['certificate_of_quality'] = []; // Initialize as an array
            foreach ($request->file('certificate_of_quality') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['certificate_of_quality'][] = $documentBase64;
            }
        }

        // Handle multiple documents donor_report
        if ($request->hasFile('donor_report')) {
            $files2 = $request->file('donor_report');
            Log::info('Number of files donor_report uploaded:', ['count' => count($files2)]);
            $postData['donor_report'] = []; // Initialize as an array
            foreach ($request->file('donor_report') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['donor_report'][] = $documentBase64;
            }
        }


        // Handle multiple documents invoice_copy
        if ($request->hasFile('invoice_copy')) {
            $files3 = $request->file('invoice_copy');
            Log::info('Number of files invoice_copy uploaded:', ['count' => count($files3)]);
            $postData['invoice_copy'] = []; // Initialize as an array
            foreach ($request->file('invoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['invoice_copy'][] = $documentBase64;
            }
        }

        // Handle multiple documents pending_documents
        if ($request->hasFile('pending_documents')) {
            $files4 = $request->file('pending_documents');
            Log::info('Number of files pending_documents uploaded:', ['count' => count($files4)]);
            $postData['pending_documents'] = []; // Initialize as an array
            foreach ($request->file('pending_documents') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['pending_documents'][] = $documentBase64;
            }
        }

        // Handle multiple documents collectionPartAInvoice_copy
        if ($request->hasFile('collectionPartAInvoice_copy')) {
            $files5 = $request->file('collectionPartAInvoice_copy');
            Log::info('Number of files collectionPartAInvoice_copy uploaded:', ['count' => count($files5)]);
            $postData['collectionPartAInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartAInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartAInvoice_copy'][] = $documentBase64;
            }
        }

        // Handle multiple documents collectionPartBInvoice_copy
        if ($request->hasFile('collectionPartBInvoice_copy')) {
            $files6 = $request->file('collectionPartBInvoice_copy');
            Log::info('Number of files collectionPartBInvoice_copy uploaded:', ['count' => count($files6)]);
            $postData['collectionPartBInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartBInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartBInvoice_copy'][] = $documentBase64;
            }
        }

         // Handle multiple documents collectionPartCInvoice_copy
         if ($request->hasFile('collectionPartCInvoice_copy')) {
            $files7 = $request->file('collectionPartCInvoice_copy');
            Log::info('Number of files collectionPartCInvoice_copy uploaded:', ['count' => count($files7)]);
            $postData['collectionPartCInvoice_copy'] = []; // Initialize as an array
            foreach ($request->file('collectionPartCInvoice_copy') as $document) {
                $documentContent = file_get_contents($document->getRealPath());
                $documentBase64 = 'data:' . $document->getMimeType() . ';base64,' . base64_encode($documentContent);
                $postData['collectionPartCInvoice_copy'][] = $documentBase64;
            }
        }
        

        // Define the external API URL for saving the tour plan
        $apiUrl = config('auth_api.dcr_collections_edit_submit_url'); // Ensure this is set in your config

        if (!$apiUrl) {
            Log::error('DCR Collections Submit URL not configured.');
            return response()->json([
                'success' => false,
                'message' => 'DCR Collections Submit URL is not configured.'
            ], 500);
        }

        try {
            // Log the data being sent
            Log::info('Sending data to Edit collection submit API', [
                'data' => $postData,
            ]);

           
            // Make the API request with the Bearer token
            $response = Http::timeout(120)->connectTimeout(30)
            ->withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
            ])->post($apiUrl, $postData);

            // Log the API response
            Log::info('Edit collection submit API Response', [
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
                Log::error('Edit collection submit API failed', [
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
            Log::error('Edit collection submit exception', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while edit submitting the visit: ' . $e->getMessage()
            ], 500);
        }
    }

    
      // Edit sourcing Update Visit Submit 
      public function sourcingEditSubmit(Request $request)
      {
         // Optional: Validate that the visit_id from the request matches the {id} parameter
          if (!$request->input('sourcing_visit_id')) {
              return response()->json([
                  'success' => false,
                  'message' => 'Invalid Sourcing Visit ID.'
              ], 400);
          }
  
            // Validate the form data with appropriate rules
            $validatedData = $request->validate([
                'blood_bank_name'           => 'required|string|max:255',
                'contact_person_name'       => 'required|string|max:255',
                'mobile_no'                 => 'required|digits_between:10,20',
                'email'                     => 'nullable|email|max:255',
                'address'                   => 'nullable|string|max:500',
                'FFPProcurementCompany'     => 'nullable|string|max:255',
                'currentPlasmaPrice'        => 'nullable|numeric|min:0',
                'potentialPerMonth'         => 'nullable|string|max:255',
                'paymentTerms'              => 'nullable|string|max:255',
                'remarks'                   => 'nullable|string|max:1000',
                 // New extra fields
                'part_a_price'              => 'nullable|numeric|min:0',
                'part_b_price'              => 'nullable|numeric|min:0',
                'part_c_price'              => 'nullable|numeric|min:0',
                'include_gst'               => 'sometimes|accepted', // if checkbox is ticked, its value should be "on" or "1"
                'gst_rate'                  => 'nullable|numeric',
                'total_plasma_price'        => 'nullable|numeric|min:0'
            ]);
  
          // Log the data being sent
          Log::info('Sourcing Edit Submit API validatedData', [
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
                'sourcing_visit_id'     => $request->input('sourcing_visit_id'),
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
                'created_by'            => Auth::id(), // Assuming you want to capture the authenticated user
                'modified_by'           => Auth::id(),
                 // Extra fields
                'part_a_price'            => $validatedData['part_a_price'] ?? 0,
                'part_b_price'            => $validatedData['part_b_price'] ?? 0,
                'part_c_price'            => $validatedData['part_c_price'] ?? 0,
                'include_gst'             => $request->has('include_gst') ? 1 : 0,
                'gst_rate'                => $validatedData['gst_rate'] ?? 0,
                'total_plasma_price'      => $validatedData['total_plasma_price'] ?? 0,
            ];

             // Log the data being sent
            Log::info('Sourcing Edit Submit PostData API validatedData', [
                'validatedData' => $validatedData,
            ]);

  
          // Define the external API URL for saving the tour plan
          $apiUrl = config('auth_api.drc_sourcing_edit_submit_url'); // Ensure this is set in your config
  
          if (!$apiUrl) {
                Log::error('Sourcing Edit Submit URL not configured.');
                return response()->json([
                    'success' => false,
                    'message' => 'Sourcing Edit Submit URL is not configured.'
                ], 500);
          }
  
          try {
              // Log the data being sent
              Log::info('Sending data to Sourcing Edit Submit API', [
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
                  Log::error('Sourcing Edit Submit API failed', [
                      'status' => $response->status(),
                      'body'   => $response->body(),
                  ]);
      
                  return response()->json([
                    'success' => false,
                    'message' => 'Failed to connect to the Sourcing Edit Submit server.'
                ], $response->status());
              }
          } catch (\Exception $e) {
              // Log the exception message
              Log::error('Sourcing Edit Submit exception', ['error' => $e->getMessage()]);
              return response()->json([
                'success' => false,
                'message' => 'An error occurred while submitting the sourcing edit submit: ' . $e->getMessage()
            ], 500);
          }
      }
}