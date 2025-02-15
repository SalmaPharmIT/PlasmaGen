<div class="card">
    <div class="card-body">

    

      <!-- Visit Information Card -->
      <div class="card mb-4 mt-4">
          <div class="card-header text-black">
              <h5 class="mb-0"><strong>Collection Information</strong></h5>
          </div>
          <div class="card-body">
              <div class="row mb-3 mt-3">
                  <!-- Blood Bank -->
                  <div class="col-md-4">
                      <strong>Blood Bank:</strong>
                      <p>{{ $dcr['extendedProps']['blood_bank_name'] ?? 'N/A' }}</p>
                  </div>
                  <!-- Planned Quantity -->
                  <div class="col-md-4">
                      <strong>Planned Quantity:</strong>
                      <p>{{ $dcr['extendedProps']['quantity'] ?? '0' }}</p>
                  </div>
                  <!-- Time -->
                  <div class="col-md-4">
                      <strong>Time:</strong>
                      <p>{{ isset($dcr['time']) ? date('h:i A', strtotime($dcr['time'])) : 'N/A' }}</p>
                  </div>
              </div>
              <div class="row mb-3">
                  <!-- Available Quantity -->
                  <div class="col-md-4">
                      <strong>Available Quantity:</strong>
                      <p>{{ $dcr['extendedProps']['available_quantity'] ?? '0' }}</p>
                  </div>
                  <!-- Remaining Quantity -->
                  <div class="col-md-4">
                      <strong>Remaining Quantity:</strong>
                      <p>{{ $dcr['extendedProps']['remaining_quantity'] ?? '0' }}</p>
                  </div>
                  <!-- Price -->
                  <div class="col-md-4">
                      <strong>Price:</strong>
                      <p>{{ isset($dcr['extendedProps']['price']) ? number_format($dcr['extendedProps']['price'], 2) : 'N/A' }}</p>
                  </div>
              </div>
              <div class="row mb-3">
                  <!-- Remarks -->
                  <div class="col-md-4">
                      <strong>Remarks:</strong>
                      <p>{{ $dcr['extendedProps']['remarks'] ?? 'N/A' }}</p>
                  </div>
                  <!-- Pending Documents -->
                  <div class="col-md-4">
                      <strong>Pending Documents:</strong>
                      <p>
                          @if(isset($dcr['pending_document_names']) && count($dcr['pending_document_names']) > 0)
                              {{ implode(', ', $dcr['pending_document_names']) }}
                          @else
                              None
                          @endif
                      </p>
                  </div>
                  <!-- Added By -->
                  <div class="col-md-4">
                      <strong>Added By:</strong>
                      <p>{{ $dcr['extendedProps']['created_by_name'] ?? 'N/A' }}</p>
                  </div>
              </div>
          </div>
      </div>

      <!-- Transport Information Card -->
      @if(isset($dcr['extendedProps']['transport_details']) && !empty($dcr['extendedProps']['transport_details']))
          <div class="card mb-4">
              <div class="card-header text-black">
                  <h5 class="mb-0"><strong>Transport Information</strong></h5>
              </div>
              <div class="card-body">
                  <div class="row mb-3 mt-3">
                      <!-- Driver Name -->
                      <div class="col-md-4">
                          <strong>Driver Name:</strong>
                          <p>{{ $dcr['extendedProps']['transport_details']['driver_name'] ?? 'N/A' }}</p>
                      </div>
                      <!-- Driver Contact -->
                      <div class="col-md-4">
                          <strong>Driver Contact:</strong>
                          <p>{{ $dcr['extendedProps']['transport_details']['contact_number'] ?? 'N/A' }}</p>
                      </div>
                      <!-- Vehicle Number -->
                      <div class="col-md-4">
                          <strong>Vehicle Number:</strong>
                          <p>{{ $dcr['extendedProps']['transport_details']['vehicle_number'] ?? 'N/A' }}</p>
                      </div>
                  </div>
                  <div class="row mb-3">
                      <!-- Transport Remarks -->
                      <div class="col-md-12">
                          <strong>Remarks:</strong>
                          <p>{{ $dcr['extendedProps']['transport_details']['remarks'] ?? 'N/A' }}</p>
                      </div>
                  </div>
              </div>
          </div>
      @endif

    <!-- In collection partial (dcrCollectionsPartial.blade.php) -->
    @if(isset($dcr['extendedProps']['latitude']) && isset($dcr['extendedProps']['longitude']))
    <div class="card mb-4">
        <!-- Hidden inputs for lat & lng -->
        <input type="hidden" id="collectionLatitude" value="{{ $dcr['extendedProps']['latitude'] }}">
        <input type="hidden" id="collectionLongitude" value="{{ $dcr['extendedProps']['longitude'] }}">
        <input type="hidden" id="collectionMapTitle" value="{{ $dcr['title'] ?? 'DCR Location' }}">
        <div class="card-header text-black">
            <h5 class="mb-0"><strong>View Map (Collection)</strong></h5>
        </div>
        <div class="card-body">
            <div id="collectionMap" style="width: 100%; height: 300px;"></div>
        </div>
    </div>
    @endif

      <!-- Attachments Card -->
      <div class="card mb-4 mt-4">
          <div class="card-header text-black">
              <h5 class="mb-0"><strong>DCR Attachments</strong></h5>
          </div>
          <div class="card-body">
              <!-- Certificate of Quality -->
              <div class="mb-4 mt-4">
                  <h6><strong>1. Certificate of Quality</strong></h6>
                  <div class="d-flex flex-wrap">
                      @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                          @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                              @if($attachment['attachment_type'] == 1)
                                  <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                      <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Certificate of Quality" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                  </a>
                              @endif
                          @endforeach
                      @else
                          <p>No Certificates of Quality available.</p>
                      @endif
                  </div>
              </div>

              <!-- Donor Report -->
              <div class="mb-4">
                  <h6><strong>2. Donor Report</strong></h6>
                  <div class="d-flex flex-wrap">
                      @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                          @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                              @if($attachment['attachment_type'] == 2)
                                  <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                      <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Donor Report" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                  </a>
                              @endif
                          @endforeach
                      @else
                          <p>No Donor Reports available.</p>
                      @endif
                  </div>
              </div>

              <!-- Invoice Copy -->
              <div class="mb-4">
                  <h6><strong>3. Invoice Copy</strong></h6>
                  <div class="d-flex flex-wrap">
                      @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                          @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                              @if($attachment['attachment_type'] == 3)
                                  <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                      <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Invoice Copy" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                  </a>
                              @endif
                          @endforeach
                      @else
                          <p>No Invoice Copies available.</p>
                      @endif
                  </div>
              </div>

              <!-- Pending Documents -->
              <div class="mb-4">
                  <h6><strong>4. Pending Documents</strong></h6>
                  <div class="d-flex flex-wrap">
                      @if(isset($dcr['extendedProps']['dcr_attachments']) && count($dcr['extendedProps']['dcr_attachments']) > 0)
                          @foreach($dcr['extendedProps']['dcr_attachments'] as $attachment)
                              @if($attachment['attachment_type'] == 4)
                                  <a href="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" target="_blank">
                                      <img src="{{ config('auth_api.base_image_url') . $attachment['attachment'] }}" alt="Pending Document" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px; margin-bottom: 10px;">
                                  </a>
                              @endif
                          @endforeach
                      @else
                          <p>No Pending Documents available.</p>
                      @endif
                  </div>
              </div>
          </div>
      </div>

    </div>
  </div>

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
                                @if($dcr['extendedProps']['tour_plan_type'] == 3 && (strtolower($dcr['extendedProps']['manager_status']) == 'accepted' || strtolower($dcr['extendedProps']['manager_status']) == 'approved'))
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



    </div>
</div>
