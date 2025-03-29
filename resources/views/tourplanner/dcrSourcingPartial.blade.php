{{-- resources/views/partials/dcrSourcingPartial.blade.php --}}
<div class="card">
    <div class="card-body">

        <!-- Overall Sourcing Information Card -->
        <div class="card mb-4 mt-2">
            <div class="card-header text-black">
                <h5 class="mb-0"><strong>Sourcing Information</strong></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mt-2">
                        <strong>Sourcing City:</strong>
                        <p>{{ $dcr['extendedProps']['sourcing_city_name'] ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4 mt-2">
                        <strong>Status:</strong>
                        <p>{{ ucfirst(str_replace('_', ' ', $dcr['extendedProps']['status'])) ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-4 mt-2">
                        <strong>Added By:</strong>
                        <p>{{ $dcr['extendedProps']['created_by_name'] ?? 'N/A' }}</p>
                    </div>
                </div>
               
            </div>
        </div>

        <!-- Tour Plan Visits Section -->
        @if(isset($dcr['extendedProps']['tour_plan_visits']) && count($dcr['extendedProps']['tour_plan_visits']) > 0)
            <div class="card mb-4">
                <div class="card-header text-black">
                    <h5 class="mb-0"><strong>Visit Information</strong></h5>
                </div>
                <div class="card-body">
                    @foreach($dcr['extendedProps']['tour_plan_visits'] as $visit)
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                <strong>Sourcing Visit #{{ $loop->iteration }}</strong>
                                @if($dcr['extendedProps']['tour_plan_type'] == 2 && (strtolower($dcr['extendedProps']['manager_status']) == 'accepted' || strtolower($dcr['extendedProps']['manager_status']) == 'approved') && Auth::user()->role_id == 2)
                                    @php
                                        $registerUrl = route('bloodbank.register', ['id' => $visit['id']]);
                                    @endphp
                                    <a href="{{ $registerUrl }}" class="btn btn-sm btn-light">Register</a>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="row mb-2">
                                    <div class="col-md-12 mt-2">
                                        <p><strong>Blood Bank Name:</strong> {{ $visit['blood_bank_name'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Contact Person:</strong> {{ $visit['sourcing_contact_person'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Mobile No:</strong> {{ $visit['sourcing_mobile_number'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Email:</strong> {{ $visit['sourcing_email'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Address:</strong> {{ $visit['sourcing_address'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>FFP Company:</strong> {{ $visit['sourcing_ffp_company'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Plasma Price/Ltr:</strong> {{ isset($visit['sourcing_plasma_price']) ? number_format($visit['sourcing_plasma_price'], 2) : 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Potential/Month:</strong> {{ $visit['sourcing_potential_per_month'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Payment Terms:</strong> {{ $visit['sourcing_payment_terms'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <p><strong>Remarks:</strong> {{ $visit['sourcing_remarks'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Part-A Price:</strong> {{ $visit['sourcing_part_a_price'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Part-B Price:</strong> {{ $visit['sourcing_part_b_price'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>Part-C Price:</strong> {{ $visit['sourcing_part_c_price'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Include GST:</strong> {{ $visit['include_gst'] == 1 ? 'Yes' : 'No' }}</p>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-md-6">
                                        <p><strong>GST Rate (%):</strong> {{ $visit['gst_rate'] ?? 'N/A' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Total Plasma Price:</strong> {{ $visit['sourcing_total_plasma_price'] ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <p>No tour plan visits available.</p>
        @endif

        <!-- In sourcing partial (dcrSourcingPartial.blade.php) -->
        @if(isset($dcr['extendedProps']['latitude']) && isset($dcr['extendedProps']['longitude']))
        <div class="card mb-4">
            <!-- Hidden inputs for lat & lng -->
            <input type="hidden" id="sourcingLatitude" value="{{ $dcr['extendedProps']['latitude'] }}">
            <input type="hidden" id="sourcingLongitude" value="{{ $dcr['extendedProps']['longitude'] }}">
            <input type="hidden" id="sourcingMapTitle" value="{{ $dcr['title'] ?? 'DCR Location' }}">
            <div class="card-header text-black">
                <h5 class="mb-0"><strong>View Map (Sourcing)</strong></h5>
            </div>
            <div class="card-body">
                <div id="sourcingMap" style="width: 100%; height: 300px;"></div>
            </div>
        </div>
        @endif


              <!-- Expenses Information Card -->
       @if(isset($dcr['extendedProps']['expenses']) && count($dcr['extendedProps']['expenses']) > 0)
       <div class="card mb-4 mt-4">
           <div class="card-header text-black">
               <h5 class="mb-0"><strong>Expenses Information</strong></h5>
           </div>
           <div class="card-body">
               @foreach($dcr['extendedProps']['expenses'] as $expense)
               <div class="row mb-3 mt-2">
                   <!-- Description -->
                   <div class="col-md-12">
                       <strong>Description:</strong>
                       <p>{{ $expense['description'] ?? 'N/A' }}</p>
                   </div>
                   <!-- Food -->
                   <div class="col-md-4">
                       <strong>Food:</strong>
                       <p>{{ number_format($expense['food'], 2) }}</p>
                   </div>
                    <!-- Convention -->
                    <div class="col-md-4">
                        <strong>Convention:</strong>
                        <p>{{ number_format($expense['convention'], 2) }}</p>
                    </div>
                    <!-- Tel/Fax -->
                    <div class="col-md-4">
                        <strong>Tel/Fax:</strong>
                        <p>{{ number_format($expense['tel_fax'], 2) }}</p>
                    </div>
                    <!-- Lodging/Boarding -->
                    <div class="col-md-4">
                        <strong>Lodging/Boarding:</strong>
                        <p>{{ number_format($expense['lodging'], 2) }}</p>
                    </div>
                    <!-- Sundry -->
                    <div class="col-md-4">
                        <strong>Sundry:</strong>
                        <p>{{ number_format($expense['sundry'], 2) }}</p>
                    </div>
                    
                   <!-- Total Price -->
                   <div class="col-md-4">
                       <strong>Total Price:</strong>
                       <p>{{ number_format($expense['total_price'], 2) }}</p>
                   </div>
               </div>
               <div class="row mb-3">
                   <!-- Attachments -->
                   <div class="col-md-12">
                       <strong>Attachments:</strong>
                       <div class="d-flex flex-wrap">
                           @foreach($expense['attachments'] as $attachment)
                               <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                   <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Expense Attachment" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                               </a>
                           @endforeach
                       </div>
                   </div>
               </div>
               @endforeach
           </div>
       </div>
       @else
       <div class="card mb-4 mt-4">
           <div class="card-header text-black">
               <h5 class="mb-0"><strong>No Expenses Available</strong></h5>
           </div>
           <div class="card-body">
               <p>No expenses have been recorded for this DCR.</p>
           </div>
       </div>
       @endif

    </div>
</div>
