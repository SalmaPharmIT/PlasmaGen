@extends('include.dashboardLayout')

@section('title', 'View Visits')

@section('content')

{{-- <div class="pagetitle">
    <h1>Visits for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('visits.index') }}">Visits</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title --> --}}

<div class="pagetitle d-flex justify-content-between align-items-center">
    <div>
        <h1>Visits for {{ \Carbon\Carbon::parse($date)->format('F d, Y') }}</h1>
        <nav>
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('visits.index') }}">Visits</a></li>
            <li class="breadcrumb-item active">View</li>
          </ol>
        </nav>
    </div>
    <div>
        {{-- <button id="finalDcrSubmit" class="btn btn-success" style="display: none;">Final DCR Submit</button> --}}
        <form id="finalDcrSubmitForm" action="{{ route('visits.finalDCRsubmit') }}" method="POST" style="display: none;">
            @csrf
            <input type="hidden" name="visit_date" value="{{ $date }}">
            <button type="submit" class="btn btn-success">Final DCR Submit</button>
        </form>
    </div>
  </div>

<section class="section">
    <div class="row">
      <div class="col-lg-4">
        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Visits List</h5>
            <div id="visitsList">
                <!-- Visits will be loaded here via AJAX -->
                <div class="text-center my-5">
                    <div class="spinner-border" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>

              <!-- Display Success Message -->
              @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                  <i class="bi bi-check-circle me-1"></i>
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
  
              <!-- Display Error Messages -->
              @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <i class="bi bi-exclamation-octagon me-1"></i>
                  <ul class="mb-0">
                      @foreach($errors->all() as $error)
                          <li>{{ $error }}</li>
                      @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif
  

          </div>
        </div>
      </div>
      
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body" id="visitDetails">
            <h5 class="card-title">Visit Details</h5>
            <div id="visitDetailsContent">
                <p>Select a visit to see details here.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<!-- Update Visit Modal -->
<div class="modal fade" id="updateVisitModal" tabindex="-1" aria-labelledby="updateVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="updateVisitForm" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="updateVisitModalLabel">DCR Submit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <!-- Visit Details in Header -->
            {{-- <div id="modalVisitDetails">
                <!-- Populated dynamically -->
            </div>
            <hr> --}}
             <!-- Two-Column Layout -->
             <div class="row">
                <!-- Left Column: Editable Visit Details -->
                <div class="col-md-6">
                    <h5><strong>Visit Information</strong></h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 40%;">Blood Bank</th>
                                <td id="bloodBankDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Planned Qty</th>
                                <td id="plannedQuantityDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Time</th>
                                <td id="timeDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Remarks</th>
                                <td id="tpRemarksDisplay">N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- Right Column: Read-Only Driver Information -->
                <div class="col-md-6">
                    <h5><strong>Transport Information</strong></h5>
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th scope="row" style="width: 40%;">Driver Name</th>
                                <td id="driverNameDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Driver Contact</th>
                                <td id="driverContactDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Vehicle Number</th>
                                <td id="vehicleNumberDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Remarks</th>
                                <td id="driverRemarksDisplay">N/A</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            <!-- Update Form Fields -->
            <input type="hidden" name="visit_id" id="visitId">
            <input type="hidden" name="blood_bank_latitude" id="blood_bank_latitude">
            <input type="hidden" name="blood_bank_longitude" id="blood_bank_longitude">
            <input type="hidden" name="user_latitude" id="user_latitude">
            <input type="hidden" name="user_longitude" id="user_longitude">
            <div class="mb-3">
                <label for="quantityCollected" class="form-label">Quantity Collected <span style="color:red">*</span></label>
                <input type="number" class="form-control" id="quantityCollected" name="quantity_collected" min="0" required>
            </div>
            <div class="mb-3">
                <label for="remainingQuantity" class="form-label">Remaining Quantity</label>
                <input type="number" class="form-control" id="remainingQuantity" name="remaining_quantity" readonly>
            </div>
            <div class="mb-3">
                <label for="quantityPrice" class="form-label">Price</label>
                <input type="number" class="form-control" id="quantityPrice" name="quantity_price" min="0">
            </div>
            
            <!-- Attachment Sections -->
          
          <!-- Certificate of Quality -->
          <div class="mb-3">
            <label for="certificate_of_quality" class="form-label">Certificate of Quality</label>
            <input type="file" class="form-control" id="certificate_of_quality" name="certificate_of_quality[]" multiple accept="image/*,application/pdf">
            <div class="mt-2 d-flex flex-nowrap overflow-auto" id="certificate_of_qualityPreview">
                <!-- Previews will appear here -->
            </div>
          </div>
          
          <!-- Donor Report -->
          <div class="mb-3">
            <label for="donor_report" class="form-label">Donor Report</label>
            <input type="file" class="form-control" id="donor_report" name="donor_report[]" multiple accept="image/*,application/pdf">
            <div class="mt-2 d-flex flex-nowrap overflow-auto" id="donor_reportPreview">
              <!-- Previews will appear here -->
            </div>
          </div>
          
          <!-- Invoice Copy -->
          <div class="mb-3">
            <label for="invoice_copy" class="form-label">Invoice Copy</label>
            <input type="file" class="form-control" id="invoice_copy" name="invoice_copy[]" multiple accept="image/*,application/pdf">
            <div class="mt-2 d-flex flex-nowrap overflow-auto" id="invoice_copyPreview" >
              <!-- Previews will appear here -->
            </div>
          </div>
          
          <!-- Pending Documents -->
          <div class="mb-3" id="pending_documents_section" style="display: none;">
            <label for="pending_documents" class="form-label">Pending Documents</label>
            <input type="file" class="form-control" id="pending_documents" name="pending_documents[]" multiple accept="image/*,application/pdf">
            <div class="mt-2 d-flex flex-nowrap overflow-auto" id="pending_documentsPreview">
              <!-- Previews will appear here -->
            </div>
          </div>
        
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        </form>
    </div>
</div>

<!-- View DCR Details Modal -->
{{-- <div class="modal fade" id="viewDCRVisitModal" tabindex="-1" aria-labelledby="viewDCRVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DCR Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Visit Information -->
                <div class="row">
                    <!-- Left Column: Visit Details -->
                    <div class="col-md-6">
                        <h5><strong>Visit Information</strong></h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 40%;">Blood Bank</th>
                                    <td id="view_bloodBankDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Planned Qty</th>
                                    <td id="view_plannedQuantityDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Available Quantity</th>
                                    <td id="view_availableQuantityDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Remaining Quantity</th>
                                    <td id="view_remainingQuantityDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Price</th>
                                    <td id="view_priceDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Time</th>
                                    <td id="view_timeDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Remarks</th>
                                    <td id="view_tpRemarksDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Pending Documents</th>
                                    <td id="view_pendingDocumentsDisplay">None</td>
                                </tr>
                                <tr>
                                    <th scope="row">Added By</th>
                                    <td id="view_addedByDisplay">N/A</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Right Column: Transport Details -->
                    <div class="col-md-6">
                        <h5><strong>Transport Information</strong></h5>
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th scope="row" style="width: 40%;">Driver Name</th>
                                    <td id="view_driverNameDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Driver Contact</th>
                                    <td id="view_driverContactDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Vehicle Number</th>
                                    <td id="view_vehicleNumberDisplay">N/A</td>
                                </tr>
                                <tr>
                                    <th scope="row">Remarks</th>
                                    <td id="view_driverRemarksDisplay">N/A</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <hr>
                <!-- Attachments Section -->
                <div id="view_dcrAttachmentsSection">
                    <h5><strong>DCR Attachments</strong></h5>
                    
                    <!-- Certificate of Quality -->
                    <div class="mb-3">
                        <h6>1. Certificate of Quality</h6>
                        <div class="d-flex flex-nowrap overflow-auto" id="view_certificateOfQualityAttachments">
                            <!-- Attachments will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Donor Report -->
                    <div class="mb-3">
                        <h6>2. Donor Report</h6>
                        <div class="d-flex flex-nowrap overflow-auto" id="view_donorReportAttachments">
                            <!-- Attachments will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Invoice Copy -->
                    <div class="mb-3">
                        <h6>3. Invoice Copy</h6>
                        <div class="d-flex flex-nowrap overflow-auto" id="view_invoiceCopyAttachments">
                            <!-- Attachments will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Pending Documents -->
                    <div class="mb-3">
                        <h6>4. Pending Documents</h6>
                        <div class="d-flex flex-nowrap overflow-auto" id="view_pendingDocumentsAttachments">
                            <!-- Attachments will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- You can add buttons here if needed -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div> --}}


<!-- View DCR Details Modal -->
<div class="modal fade" id="viewDCRVisitModal" tabindex="-1" aria-labelledby="viewDCRVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Increased size for better visibility -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Collection Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Visit Information Card -->
                <div class="card">
                    {{-- <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Visit Information</strong></h6>
                    </div> --}}
                    <div class="card-body">
                        <div class="row mb-3 mt-3">
                            <!-- Blood Bank -->
                            <div class="col-md-4">
                                <strong>Blood Bank:</strong>
                                <p id="view_bloodBankDisplay">N/A</p>
                            </div>
                            <!-- Planned Quantity -->
                            <div class="col-md-4">
                                <strong>Planned Quantity:</strong>
                                <p id="view_plannedQuantityDisplay">N/A</p>
                            </div>
                            <!-- Time -->
                            <div class="col-md-4">
                                <strong>Time:</strong>
                                <p id="view_timeDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Available Quantity -->
                            <div class="col-md-4">
                                <strong>Available Quantity:</strong>
                                <p id="view_availableQuantityDisplay">N/A</p>
                            </div>
                            <!-- Remaining Quantity -->
                            <div class="col-md-4">
                                <strong>Remaining Quantity:</strong>
                                <p id="view_remainingQuantityDisplay">N/A</p>
                            </div>
                            <!-- Price -->
                            <div class="col-md-4">
                                <strong>Price:</strong>
                                <p id="view_priceDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Remarks -->
                            <div class="col-md-4">
                                <strong>Remarks:</strong>
                                <p id="view_tpRemarksDisplay">N/A</p>
                            </div>
                            <!-- Pending Documents -->
                            <div class="col-md-4">
                                <strong>Pending Documents:</strong>
                                <p id="view_pendingDocumentsDisplay">None</p>
                            </div>
                            <!-- Added By -->
                            <div class="col-md-4">
                                <strong>Added By:</strong>
                                <p id="view_addedByDisplay">N/A</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transport Information Card -->
                <div class="card">
                   
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Transport Information</strong></h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-3">
                            <!-- Driver Name -->
                            <div class="col-md-4">
                                <strong>Driver Name:</strong>
                                <p id="view_driverNameDisplay">N/A</p>
                            </div>
                            <!-- Driver Contact -->
                            <div class="col-md-4">
                                <strong>Driver Contact:</strong>
                                <p id="view_driverContactDisplay">N/A</p>
                            </div>
                            <!-- Vehicle Number -->
                            <div class="col-md-4">
                                <strong>Vehicle Number:</strong>
                                <p id="view_vehicleNumberDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Transport Remarks -->
                            <div class="col-md-12">
                                <strong>Remarks:</strong>
                                <p id="view_driverRemarksDisplay">N/A</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attachments Card -->
                <div class="card">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>DCR Attachments</strong></h6>
                    </div>
                    <div class="card-body">
                        <!-- Certificate of Quality -->
                        <div class="mb-4">
                            <h6><strong>1. Certificate of Quality</strong></h6>
                            <div class="d-flex flex-wrap" id="view_certificateOfQualityAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                        <!-- Donor Report -->
                        <div class="mb-4">
                            <h6><strong>2. Donor Report</strong></h6>
                            <div class="d-flex flex-wrap" id="view_donorReportAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                        <!-- Invoice Copy -->
                        <div class="mb-4">
                            <h6><strong>3. Invoice Copy</strong></h6>
                            <div class="d-flex flex-wrap" id="view_invoiceCopyAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                        <!-- Pending Documents -->
                        <div class="mb-4">
                            <h6><strong>4. Pending Documents</strong></h6>
                            <div class="d-flex flex-wrap" id="view_pendingDocumentsAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <!-- Close Button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Update Sourcing Visit Modal -->
<div class="modal fade" id="updateSourcingVisitModal" tabindex="-1" aria-labelledby="updateSourcingVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="updateSourcingVisitForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Submit DCR</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Existing Sourcing Details -->
                    <h5><strong>Sourcing Details</strong></h5>
                    <table class="table table-bordered">
                        <tbody>
                            {{-- <tr>
                                <th scope="row" style="width: 40%;">Event Type</th>
                                <td id="sourcingEventTypeDisplay">Sourcing</td>
                            </tr> --}}
                            <tr>
                                <th scope="row">Sourcing City</th>
                                <td id="sourcingCityDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Remarks</th>
                                <td id="sourcingRemarksDisplay">N/A</td>
                            </tr>
                            <tr>
                                <th scope="row">Added By</th>
                                <td id="sourcingAddedByDisplay">N/A</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    
                    <!-- Form to Add Additional Information -->
                    <h5><strong>Add Blood Bank Information</strong></h5>
                    <input type="hidden" name="visit_id" id="sourcingVisitId">
                    <input type="hidden" name="sourcing_user_latitude" id="sourcing_user_latitude">
                    <input type="hidden" name="sourcing_user_longitude" id="sourcing_user_longitude">
                    
                    <div class="mb-3">
                        <label for="bloodBankName" class="form-label">Blood Bank Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="bloodBankName" name="blood_bank_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="contactPersonName" class="form-label">Contact Person Name <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="contactPersonName" name="contact_person_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="mobileNo" class="form-label">Mobile No <span style="color:red">*</span></label>
                        <input type="tel" class="form-control" id="mobileNo" name="mobile_no" pattern="[0-9]{10}" required>
                        <div class="form-text">Enter a 10-digit mobile number.</div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="FFP_procurement_company" class="form-label">Past/Current FFP Pocurement Company <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="FFPProcurementCompany" name="FFPProcurementCompany" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_terms" class="form-label">Current Plasma Price/Ltr <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="currentPlasmaPrice" name="currentPlasmaPrice" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_terms" class="form-label">Potential Per Month <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="potentialPerMonth" name="potentialPerMonth" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_terms" class="form-label">Payment Terms <span style="color:red">*</span></label>
                        <input type="text" class="form-control" id="paymentTerms" name="paymentTerms" required>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Remarks</label>
                        <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit DCR</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- View Sourcing DCR Details Modal -->
<div class="modal fade" id="viewSourcingDCRVisitModal" tabindex="-1" aria-labelledby="viewSourcingDCRVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Adjust size as needed -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sourcing Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sourcing Information Card -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row mt-3">
                            <!-- Sourcing City -->
                            <div class="col-md-4">
                                <strong>Sourcing City:</strong>
                                <p id="sourcing_city_name_display">N/A</p>
                            </div>
                            <!-- Status -->
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <p id="sourcing_status_display">N/A</p>
                            </div>
                            <!-- Added By -->
                            <div class="col-md-4">
                                <strong>Added By:</strong>
                                <p id="sourcing_added_by_display">N/A</p>
                            </div>
                           
                        </div>
                      
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <!-- Close Button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
    <style>
        /* Optional: Style the visits list */
        .visit-item {
            cursor: pointer;
            border: 1px solid #dee2e6;  /* Default border */
            border-radius: 0.25rem; /* Optional: Match Bootstrap's border radius */
        }
        /* .visit-item:hover {
            background-color: #f8f9fa;
        } */
        .visit-item.active {
            border-color: #007bff; /* Bootstrap Primary Color */
            /* Optionally, add a subtle background change */
            /* background-color: #e9ecef; */
        }

        /* Style for the preview container */
        .preview-item {
            position: relative;
            width: 100px;
            height: 100px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            background-color: #f9f9f9;
            margin-right: 10px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Style for images in preview */
        .preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Style for the delete button */
        .preview-item .delete-btn {
            position: absolute;
            top: 2px;
            right: 2px;
            background-color: rgba(0, 0, 0, 0.6);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        /* Hover effect for delete button */
        .preview-item .delete-btn:hover {
            background-color: rgba(255, 0, 0, 0.8);
        }

        /* Style for embedded PDFs */
        .preview-item embed {
            object-fit: cover;
        }

        /* Attachment Container Styling */
        .attachment-container a {
            margin-right: 10px;
            margin-bottom: 10px;
            display: inline-block;
        }
        .attachment-container img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 5px;
            transition: transform 0.2s;
        }
        .attachment-container img:hover {
            transform: scale(1.05);
        }

        /* Optional: Add cursor pointer to attachments */
        .attachment-container a img {
            cursor: pointer;
        }

        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            .attachment-container img {
                width: 80px;
                height: 80px;
            }
        }
    </style>
@endpush

@push('scripts')

<!-- Include Bootstrap Icons (if not already included) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<!-- Include SweetAlert2 (if not already included) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to initialize file input previews
        function initializeFilePreview(inputId, previewId) {
            const inputElement = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewId);
            
            // Initialize a DataTransfer object to manage files
            const dt = new DataTransfer();

            inputElement.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);

                // Add new files to the DataTransfer object
                files.forEach(file => {
                    dt.items.add(file);
                });

                // Update the input's files to the DataTransfer's files
                inputElement.files = dt.files;

                // Clear existing previews
                previewContainer.innerHTML = '';

                // Display previews for each file
                Array.from(dt.files).forEach((file, index) => {
                    const fileReader = new FileReader();

                    fileReader.onload = function(e) {
                        const fileURL = e.target.result;
                        const fileType = file.type;

                        const previewItem = document.createElement('div');
                        previewItem.classList.add('preview-item');

                        // Create delete button
                        const deleteBtn = document.createElement('button');
                        deleteBtn.classList.add('delete-btn');
                        deleteBtn.innerHTML = '&times;';
                        deleteBtn.title = 'Remove this file';

                        // Event listener for delete button
                        deleteBtn.addEventListener('click', function() {
                            // Remove the file from DataTransfer
                            dt.items.remove(index);
                            // Update the input's files
                            inputElement.files = dt.files;
                            // Remove the preview
                            previewContainer.removeChild(previewItem);
                            // Re-render the remaining previews to update indices
                            renderPreviews();
                        });

                        // Append delete button
                        previewItem.appendChild(deleteBtn);

                        if (fileType.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = fileURL;
                            img.alt = file.name;
                            previewItem.appendChild(img);
                        } else if (fileType === 'application/pdf') {
                            const embed = document.createElement('embed');
                            embed.src = fileURL;
                            embed.type = 'application/pdf';
                            embed.style.width = '100px';
                            embed.style.height = '100px';
                            previewItem.appendChild(embed);
                        } else {
                            // For other file types, display file name
                            const fileName = document.createElement('p');
                            fileName.textContent = file.name;
                            previewItem.appendChild(fileName);
                        }

                        previewContainer.appendChild(previewItem);
                    }

                    fileReader.readAsDataURL(file);
                });
            });

            // Function to re-render previews (useful after deletion to update indices)
            function renderPreviews() {
                // Clear existing previews
                previewContainer.innerHTML = '';

                // Display previews for each file
                Array.from(dt.files).forEach((file, index) => {
                    const fileReader = new FileReader();

                    fileReader.onload = function(e) {
                        const fileURL = e.target.result;
                        const fileType = file.type;

                        const previewItem = document.createElement('div');
                        previewItem.classList.add('preview-item');

                        // Create delete button
                        const deleteBtn = document.createElement('button');
                        deleteBtn.classList.add('delete-btn');
                        deleteBtn.innerHTML = '&times;';
                        deleteBtn.title = 'Remove this file';

                        // Event listener for delete button
                        deleteBtn.addEventListener('click', function() {
                            // Remove the file from DataTransfer
                            dt.items.remove(index);
                            // Update the input's files
                            inputElement.files = dt.files;
                            // Remove the preview
                            previewContainer.removeChild(previewItem);
                            // Re-render the remaining previews
                            renderPreviews();
                        });

                        // Append delete button
                        previewItem.appendChild(deleteBtn);

                        if (fileType.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = fileURL;
                            img.alt = file.name;
                            previewItem.appendChild(img);
                        } else if (fileType === 'application/pdf') {
                            const embed = document.createElement('embed');
                            embed.src = fileURL;
                            embed.type = 'application/pdf';
                            embed.style.width = '100px';
                            embed.style.height = '100px';
                            previewItem.appendChild(embed);
                        } else {
                            // For other file types, display file name
                            const fileName = document.createElement('p');
                            fileName.textContent = file.name;
                            previewItem.appendChild(fileName);
                        }

                        previewContainer.appendChild(previewItem);
                    }

                    fileReader.readAsDataURL(file);
                });
            }
        }

        // Initialize previews for all attachment categories
        initializeFilePreview('certificate_of_quality', 'certificate_of_qualityPreview');
        initializeFilePreview('donor_report', 'donor_reportPreview');
        initializeFilePreview('invoice_copy', 'invoice_copyPreview');
        initializeFilePreview('pending_documents', 'pending_documentsPreview');

    });
</script>

<script>
    $(document).ready(function() {
        const selectedDate = "{{ $date }}";
        // Get the server's current date in 'YYYY-MM-DD' format
        const currentDate = "{{ \Carbon\Carbon::now()->toDateString() }}";
        const visitsListEl = $('#visitsList');
        const visitDetailsContentEl = $('#visitDetailsContent');

        let entityFeatures = {}; // To store km_bound and location_enabled
        let userLocation = null; // To store user's current location

        console.log('currentDate: '+currentDate);

        // Function to fetch visits data
        function fetchVisits() {
            $.ajax({
                url: "{{ route('visits.fetch', ['date' => $date]) }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response:', response); // Debugging

                    visitsListEl.empty(); // Clear the loading spinner

                    if(response.success) {
                        const visits = response.data;
                        if(visits.length === 0) {
                            visitsListEl.html('<div class="alert alert-info">No visits for the selected day.</div>');
                            visitDetailsContentEl.html('<p>Select a visit to see details here.</p>'); // Clear details
                            $("#finalDcrSubmitForm").hide();
                            return;
                        }

                        // Check if all visits have status "updated"
                        // const allUpdated = visits.every(visit => {
                        //     return visit.extendedProps &&
                        //         visit.extendedProps.status &&
                        //         visit.extendedProps.status.toLowerCase() === "updated";
                        // });

                        // Check if any visit has status "updated"
                        // const anyUpdated = visits.some(visit => {
                        //     return visit.extendedProps &&
                        //         visit.extendedProps.status &&
                        //         visit.extendedProps.status.toLowerCase() === "updated";
                        // });

                        const anyUpdated = visits.some(visit => {
                            if (!visit.extendedProps || !visit.extendedProps.status) return false;
                            const status = visit.extendedProps.status.toLowerCase();
                            const tourPlanType = Number(visit.extendedProps.tour_plan_type);
                            // For tour_plan_type 1, status should be "updated"
                            // For tour_plan_type 2, status should be "initiated"
                            if (tourPlanType === 1 && status === "updated" && visit.extendedProps.tp_status == 'accepted') {
                                return true;
                            }
                            if (tourPlanType === 2 && status === "initiated" && visit.extendedProps.tp_status == 'accepted') {
                                return true;
                            }
                            return false;
                        }); 

                        if(anyUpdated) {
                            $("#finalDcrSubmitForm").show();
                        } else {
                            $("#finalDcrSubmitForm").hide();
                        }

                        // Create a list group
                        const listGroup = $('<div class="list-group"></div>');

                        visits.forEach(function(visit, index) {
                            // Safely access 'extendedProps'
                            const extendedProps = visit.extendedProps || {};

                            const tourPlanType = Number(extendedProps.tour_plan_type);
                          //  const visitType = tourPlanType === 1 ? 'Collection' : (tourPlanType === 2 ? 'Sourcing' : 'Unknown');
                            const status = extendedProps.status 
                                ? extendedProps.status.charAt(0).toUpperCase() + extendedProps.status.slice(1) 
                                : 'Unknown';

                            var visitType = 'Unknown';
                            if(tourPlanType == 1) {
                                visitType = 'Collection';
                            } else  if(tourPlanType == 2) {
                                visitType = 'Sourcing';
                            } else  if(tourPlanType == 3) {
                                visitType = 'Both';
                            }
                            else {
                                visitType = 'Unknown'; 
                            }

                            // const visitItem = $(`
                            //     <a href="#" class="list-group-item list-group-item-action visit-item" data-index="${index}">
                            //         <div><strong>${escapeHtml(visit.title)}</strong></div>
                            //         <div>Type: ${visitType}</div>
                            //          <div class="mt-2">
                            //             ${extendedProps.status === 'dcr_submitted' ? (
                            //                 tourPlanType === 1 ? 
                            //                 `<button type="button" class="btn btn-info btn-sm view-dcr-btn" data-visit='${JSON.stringify(visit)}'>View DCR Details</button>` :
                            //                 tourPlanType === 2 ? 
                            //                 `<button type="button" class="btn btn-info btn-sm view-sourcing-dcr-btn" data-visit='${JSON.stringify(visit)}'>View DCR Details</button>` :
                            //                 ''
                            //             ) : ''}
                            //         </div>
                            //     </a>
                            // `);

                            // const visitItem = $(`
                            //     <a href="#" class="list-group-item list-group-item-action visit-item" data-index="${index}">
                            //         <div><strong>${escapeHtml(visit.title)}</strong></div>
                            //         <div>Type: ${visitType}</div>
                            //         <div class="mt-2">
                            //             ${
                            //                 ['dcr_submitted', 'accepted', 'rejected', 'approved'].includes(extendedProps.status) 
                            //                 ? (
                            //                     tourPlanType === 1 
                            //                     ? `<button type="button" class="btn btn-info btn-sm view-dcr-btn" data-visit='${JSON.stringify(visit)}'>View DCR Details</button>` 
                            //                     : tourPlanType === 2 
                            //                         ? `<button type="button" class="btn btn-info btn-sm view-sourcing-dcr-btn" data-visit='${JSON.stringify(visit)}'>View DCR Details</button>` 
                            //                         : ''
                            //                 )
                            //                 : ''
                            //             }
                            //         </div>
                            //     </a>
                            // `);

                            const visitItem = (function() {
                            let buttonsHtml = '';

                            // Condition: only show a "View" button if status is among these
                            const showViewButton = ['updated', 'dcr_submitted', 'accepted', 'rejected', 'approved'].includes(extendedProps.status);

                            if (showViewButton) {
                            if (tourPlanType === 1) {
                                buttonsHtml = `
                                <button type="button" 
                                        class="btn btn-info btn-sm view-dcr-btn" 
                                        data-visit='${JSON.stringify(visit)}'>
                                    View Visit Details
                                </button>`;
                            } else if (tourPlanType === 2) {
                                buttonsHtml = `
                                <button type="button" 
                                        class="btn btn-info btn-sm view-sourcing-dcr-btn" 
                                        data-visit='${JSON.stringify(visit)}'>
                                    View Visit Details
                                </button>`;
                            } else if (tourPlanType === 3) {
                                // Show two buttons: "View Collection DCR Details" & "View Sourcing DCR Details"
                                const encodedVisit = JSON.stringify(visit);
                                buttonsHtml = `
                                <button type="button" 
                                        class="btn btn-info btn-sm view-dcr-btn me-2 mb-2" 
                                        data-visit='${encodedVisit}'>
                                    View Collection Details
                                </button>
                                <button type="button" 
                                        class="btn btn-info btn-sm view-sourcing-dcr-btn" 
                                        data-visit='${encodedVisit}'>
                                    View Sourcing Details
                                </button>
                                `;
                            }
                            }

                            return $(`
                            <a href="#" class="list-group-item list-group-item-action visit-item" data-index="${index}">
                                <div><strong>${escapeHtml(visit.title)}</strong></div>
                                <div>Type: ${visitType}</div>
                                <div class="mt-2">
                                ${buttonsHtml}
                                </div>
                            </a>
                            `);
                        }());

                            // Attach data to the element for easy access
                            visitItem.data('visit', visit);

                            listGroup.append(visitItem);
                        });

                        visitsListEl.append(listGroup);
                    } else {
                        visitsListEl.html('<div class="alert alert-danger">' + escapeHtml(response.message) + '</div>');
                        visitDetailsContentEl.html('<p>Select a visit to see details here.</p>'); // Clear details
                        $("#finalDcrSubmitForm").hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching visits:", error);
                    visitsListEl.html('<div class="alert alert-danger">An error occurred while fetching visits.</div>');
                    visitDetailsContentEl.html('<p>Select a visit to see details here.</p>'); // Clear details
                    $("#finalDcrSubmitForm").hide();
                }
            });
        }

        // Function to display visit details
        function displayVisitDetails(visit) {
            // Safely access 'extendedProps'
            const extendedProps = visit.extendedProps || {};

            const tourPlanType = Number(extendedProps.tour_plan_type);
           // const visitType = tourPlanType === 1 ? 'Collection' : (tourPlanType === 2 ? 'Sourcing' : 'Unknown');

            var visitType = 'Unknown';
            if(tourPlanType == 1) {
                visitType = 'Collection';
            } else  if(tourPlanType == 2) {
                visitType = 'Sourcing';
            } else  if(tourPlanType == 3) {
                visitType = 'Both';
            }
            else {
                visitType = 'Unknown'; 
            }


            // Initialize details HTML with common fields
            let detailsHtml = `
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <th scope="row">Name</th>
                            <td>${escapeHtml(visit.title) || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Status</th>
                            <td>${extendedProps.status ? capitalizeFirstLetter(escapeHtml(extendedProps.status)) : 'Unknown'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Event Type</th>
                            <td>${visitType}</td>
                        </tr>
            `;

            if(tourPlanType === 1) { // Collection
                detailsHtml += `
                        <tr>
                            <th scope="row">Time (24Hrs)</th>
                            <td>${escapeHtml(formatTime(visit.time)) ? escapeHtml(formatTime(visit.time)) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Quantity</th>
                            <td>${extendedProps.quantity !== null && extendedProps.quantity !== undefined ? extendedProps.quantity : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Remarks</th>
                            <td>${escapeHtml(extendedProps.remarks) ? escapeHtml(extendedProps.remarks) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pending Documents</th>
                            <td>${visit.pending_document_names && visit.pending_document_names.length > 0 ? escapeHtml(visit.pending_document_names.join(', ')) : 'None'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Added By</th>
                            <td>${escapeHtml(extendedProps.created_by_name) ? escapeHtml(extendedProps.created_by_name) : 'N/A'}</td>
                        </tr>
                `;
            } else if(tourPlanType === 2) { // Sourcing
                detailsHtml += `
                        <tr>
                            <th scope="row">Remarks</th>
                            <td>${escapeHtml(extendedProps.remarks) ? escapeHtml(extendedProps.remarks) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Sourcing City</th>
                            <td>${escapeHtml(extendedProps.sourcing_city_name) ? escapeHtml(extendedProps.sourcing_city_name) : 'N/A'}</td>
                        </tr>
                         <tr>
                            <th scope="row">Added By</th>
                            <td>${escapeHtml(extendedProps.created_by_name) ? escapeHtml(extendedProps.created_by_name) : 'N/A'}</td>
                        </tr>
                `;
            }
           else if(tourPlanType === 3) { // Both
                detailsHtml += `
                        <tr>
                            <th scope="row">Time (24Hrs)</th>
                            <td>${escapeHtml(formatTime(visit.time)) ? escapeHtml(formatTime(visit.time)) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Quantity</th>
                            <td>${extendedProps.quantity !== null && extendedProps.quantity !== undefined ? extendedProps.quantity : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Remarks</th>
                            <td>${escapeHtml(extendedProps.remarks) ? escapeHtml(extendedProps.remarks) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Pending Documents</th>
                            <td>${visit.pending_document_names && visit.pending_document_names.length > 0 ? escapeHtml(visit.pending_document_names.join(', ')) : 'None'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Sourcing City</th>
                            <td>${escapeHtml(extendedProps.sourcing_city_name) ? escapeHtml(extendedProps.sourcing_city_name) : 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Added By</th>
                            <td>${escapeHtml(extendedProps.created_by_name) ? escapeHtml(extendedProps.created_by_name) : 'N/A'}</td>
                        </tr>
                `;
            }

            // Close the table
            detailsHtml += `
                    </tbody>
                </table>
            `;

            // Check if the visit_date is the current date
            const visitDate = visit.visit_date ? formatDate(visit.visit_date) : null;

            if(visitDate && visitDate === currentDate && tourPlanType === 1 && visit.extendedProps.status == 'submitted' && visit.extendedProps.tp_status == 'accepted') {
                // Add Update button centered
                const updateButton = `<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateVisitModal" data-visit-id="${visit.id}" data-visit='${JSON.stringify(visit)}'>Update Visit</button>`;
                detailsHtml += `<div class="mt-3 text-center">${updateButton}</div>`;

                // The Fetch Entity Features is now handled within the modal show event
            }

            if(visitDate && visitDate === currentDate && tourPlanType === 2  && visit.extendedProps.tp_status == 'accepted') {   // For Sourcing DCR Submit Button
              // Encode the visit data to safely include in the data attribute
                const encodedVisit = encodeURIComponent(JSON.stringify(visit));

                // Create the button with the encoded data
                const submitButton = `
                    <button 
                        type="button" 
                        class="btn btn-primary submit-sourcing-dcr-btn" 
                        data-visit-id="${visit.id}" 
                        data-visit="${encodedVisit}">
                        Update Visit
                    </button>
                `;
                detailsHtml += `<div class="mt-3 text-center">${submitButton}</div>`;
            }

            if(visitDate && visitDate === currentDate && tourPlanType === 3  && visit.extendedProps.tp_status == 'accepted') {
        

                // Collection Submit - For Both 
                const updateCollectionButton = `<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateVisitModal" data-visit-id="${visit.id}" data-visit='${JSON.stringify(visit)}'>Update Collection Visits</button>`;
                
                
                // Sourcing Submit - For Both 
                const encodedVisit = encodeURIComponent(JSON.stringify(visit));

                // Create the button with the encoded data
                const updateSourcingButton = `
                    <button 
                        type="button" 
                        class="btn btn-primary submit-sourcing-dcr-btn" 
                        data-visit-id="${visit.id}" 
                        data-visit="${encodedVisit}">
                        Update Sourcing Visits
                    </button>
                `;
                
             //   detailsHtml += `<div class="mt-3 text-center">${updateCollectionButton} ${updateSourcingButton}</div>`;

               // Decide which button(s) to show based on status
                let buttonsHtml = '';
                if (visit.extendedProps.status === 'submitted') {
                    buttonsHtml = updateCollectionButton;
                    buttonsHtml += updateSourcingButton;
                } else {
                    buttonsHtml = updateSourcingButton;
                }

                // If you want to handle a scenario where no button is shown for other statuses,
                // you could add an `else` or just let buttonsHtml remain empty.

                // Append the buttons to detailsHtml if there's at least one
                if (buttonsHtml) {
                    detailsHtml += `<div class="mt-3 text-center">${buttonsHtml}</div>`;
                }

                // The Fetch Entity Features is now handled within the modal show event
            }

            // Update the details section
            visitDetailsContentEl.html(detailsHtml);
        }

        // Helper function to capitalize the first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

         // Helper function to format date to 'YYYY-MM-DD'
         function formatDate(dateString) {
            const date = new Date(dateString);
            if (isNaN(date)) {
                console.error('Invalid date string:', dateString);
                return null;
            }
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        // Event listener for visit items
        visitsListEl.on('click', '.visit-item', function(e) {
            e.preventDefault();
            const visit = $(this).data('visit');

            // Highlight the selected item by adding 'active' class
            $('.visit-item').removeClass('active');
            $(this).addClass('active');

            // Display details
            displayVisitDetails(visit);
        });

        // Initial fetch
        fetchVisits();


        // // Function to populate fetch EntityFeatures Settings
         // Function to fetch Entity Features when modal opens
         function fetchEntityFeatures(modal, latitudeFieldId, longitudeFieldId) {
            return $.ajax({
                url: "{{ route('visits.getFeatureSettings') }}",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if(response.success) {
                        entityFeatures = response.data;
                        console.log('Entity Features:', entityFeatures);

                        if(entityFeatures.location_enabled.toLowerCase() === 'yes') {
                            // Request user location
                            if (navigator.geolocation) {
                                navigator.geolocation.getCurrentPosition(function(position) {
                                    userLocation = {
                                        latitude: position.coords.latitude,
                                        longitude: position.coords.longitude
                                    };
                                    console.log('User Location:', userLocation);

                                    
                                    // modal.find('#user_latitude').val(userLocation.latitude || '');
                                    // modal.find('#user_longitude').val(userLocation.longitude || '');

                                     // Populate the hidden input fields within the modal
                                    modal.find(`#${latitudeFieldId}`).val(userLocation.latitude || '');
                                    modal.find(`#${longitudeFieldId}`).val(userLocation.longitude || '');
                                }, function(error) {
                                    console.error('Error obtaining location:', error);
                                    Swal.fire('Location Error', 'Unable to retrieve your location. Please allow location access.', 'error');
                                });
                            } else {
                                Swal.fire('Geolocation Not Supported', 'Your browser does not support geolocation.', 'error');
                            }
                        }
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching entity features:", error);
                    Swal.fire('Error', 'An error occurred while fetching entity features.', 'error');
                }
            });
        }


         // Event listener for Update Visit Modal to populate data
         $('#updateVisitModal').on('show.bs.modal', function (event) {
            const button = $(event.relatedTarget); // Button that triggered the modal
            const visitData = button.data('visit'); // Extract info from data-* attributes

            // Populate the modal with visit details in the header
            const modal = $(this);
            let modalDetailsHtml = `
                <h5><strong>Driver Information</strong></h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th scope="row">Driver Name</th>
                            <td>${escapeHtml(visitData.extendedProps.transport_details.driver_name) || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Driver Contact</th>
                            <td>${escapeHtml(visitData.extendedProps.transport_details.contact_number) || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th scope="row">Vehicle Number</th>
                            <td>${escapeHtml(visitData.extendedProps.transport_details.vehicle_number) || 'N/A'}</td>
                        </tr>
                        <!-- Add more driver info fields as needed -->
                    </tbody>
                </table>
            `;

            // Append to modalVisitDetails
            modal.find('#modalVisitDetails').html(modalDetailsHtml);

            // Populate form fields with visit data
            modal.find('#bloodBankName').val(visitData.extendedProps.blood_bank_name || '');
            modal.find('#quantityCollected').val(visitData.extendedProps.quantity || '');
            // Assuming 'originalQuantity' is the initial 'quantity_collected'
            modal.find('#remainingQuantity').val(visitData.extendedProps.quantity || 0);

            // Store originalQuantity in a hidden input for dynamic calculation
            // Alternatively, you can set it as a data attribute
            modal.find('#updateVisitForm').data('originalQuantity', visitData.extendedProps.quantity || 0);

            // Populate Driver Information Read-Only Fields
            modal.find('#bloodBankDisplay').text(visitData.extendedProps.blood_bank_name || '-');
            modal.find('#plannedQuantityDisplay').text(visitData.extendedProps.quantity || '-');
            modal.find('#timeDisplay').text(formatTime(visitData.time) || '-');
            modal.find('#tpRemarksDisplay').text(visitData.extendedProps.remarks || '-');

            modal.find('#driverNameDisplay').text(visitData.extendedProps.transport_details.driver_name || '-');
            modal.find('#driverContactDisplay').text(visitData.extendedProps.transport_details.contact_number || '-');
            modal.find('#vehicleNumberDisplay').text(visitData.extendedProps.transport_details.vehicle_number || '-');
            modal.find('#driverRemarksDisplay').text(visitData.extendedProps.transport_details.remarks || '-');


            // Set the visit ID in the hidden input
            modal.find('#visitId').val(visitData.id || '');
            modal.find('#blood_bank_latitude').val(visitData.extendedProps.latitude || '');
            modal.find('#blood_bank_longitude').val(visitData.extendedProps.longitude || '');
  

            // Reset the form fields for file inputs
            $('#updateVisitForm')[0].reset();

            // Reset preview containers
            ['certificate_of_qualityPreview', 'donor_reportPreview', 'invoice_copyPreview', 'pending_documentsPreview'].forEach(function(previewId) {
                $('#' + previewId).empty();
            });

            // Hide pending documents section initially
            $('#pending_documents_section').hide();

            // Show pending documents section if there are pending documents
            if(visitData.pending_document_names && visitData.pending_document_names.length > 0) {
                $('#pending_documents_section').show();
            }

            // Fetch Entity Features and handle location
          //  fetchEntityFeatures(modal);
            fetchEntityFeatures(modal, 'user_latitude', 'user_longitude');
        });

         // Event listener for Update Sourcing Visit Modal to populate data
         $('#updateSourcingVisitModal').on('show.bs.modal', function (event) {
            const modal = $(this);
            const visitData = modal.data('data-visit'); // Extract info from data-* attributes

            // Check if visitData exists
            if (!visitData) {
                console.error("Visit data is missing for the Update Sourcing Visit Modal.");
                Swal.fire('Error', 'Visit data is missing.', 'error');
                return;
            }

            // Safely access 'extendedProps'
            const extendedProps = visitData.extendedProps || {};

     
            modal.find('#sourcingRemarksDisplay').text(escapeHtml(extendedProps.remarks) || 'N/A');
            modal.find('#sourcingCityDisplay').text(escapeHtml(extendedProps.sourcing_city_name) || 'N/A');
            modal.find('#sourcingAddedByDisplay').text(escapeHtml(extendedProps.created_by_name) || 'N/A');

            // Set the visit ID in the hidden input
            modal.find('#sourcingVisitId').val(visitData.id || '');

            // Reset the form fields
            modal.find('#updateSourcingVisitForm')[0].reset();
            

            // Fetch Entity Features and handle location
            fetchEntityFeatures(modal, 'sourcing_user_latitude', 'sourcing_user_longitude');
        });


        // Calculate Remaining Quantity when Quantity Collected changes
        $('#quantityCollected').on('input', function() {
            const quantityCollected = parseInt($(this).val()) || 0;
            const originalQuantity = parseInt($('#updateVisitForm').data('originalQuantity')) || 0;

            const remainingQuantity = originalQuantity - quantityCollected;
            $('#remainingQuantity').val(remainingQuantity >= 0 ? remainingQuantity : 0);
        });

        // Calculate Remaining Quantity when Quantity Collected changes
        $('#quantityCollected').on('input', function() {
            const quantityCollected = parseInt($(this).val()) || 0;
            const plannedQuantity = parseInt($('#plannedQuantityDisplay').text()) || 0;

            const remainingQuantity = plannedQuantity - quantityCollected;
            $('#remainingQuantity').val(remainingQuantity >= 0 ? remainingQuantity : 0);
        });

        // Handle form submission via AJAX
        $('#updateVisitForm').on('submit', function(e) {
            e.preventDefault();

               // Check if location is enabled
               if(entityFeatures.location_enabled.toLowerCase() === 'yes') {
                if(!userLocation) {
                    Swal.fire('Location Required', 'Please allow location access to proceed.', 'warning');
                    return;
                }

                // Get blood bank location from extendedProps (assumed to be part of visitData)
                // Since visitData is not directly accessible here, retrieve it from the hidden input or a data attribute
                // To simplify, store blood bank latitude and longitude as data attributes on the form

                // Fetch blood bank coordinates
                // const bloodBankLat = parseFloat($('#bloodBankDisplay').data('latitude'));
                // const bloodBankLon = parseFloat($('#bloodBankDisplay').data('longitude'));
                const bloodBankLat = $('#blood_bank_latitude').val();
                const bloodBankLon = $('#blood_bank_longitude').val();

                console.log('Blood Bank Latitude:', bloodBankLat);
                console.log('Blood Bank Longitude:', bloodBankLon);

                console.log('User Latitude:', userLocation.latitude);
                console.log('User Longitude:',  userLocation.longitude);

                if(isNaN(bloodBankLat) || isNaN(bloodBankLon)) {
                    Swal.fire('Error', 'Blood Bank location data is missing.', 'error');
                    return;
                }

                // Calculate distance
                const distance = calculateDistance(userLocation.latitude, userLocation.longitude, bloodBankLat, bloodBankLon);
                console.log('Distance to Blood Bank:', distance, 'km');

                const kmBound = parseFloat(entityFeatures.km_bound);

                if(distance > kmBound) {
                    Swal.fire('Distance Restriction', `You are not within ${kmBound} km of the Blood Bank.`, 'error');
                    return;
                }
            }

            const formData = new FormData(this);
            // Log each key and value in the FormData
            for (let [key, value] of formData.entries()) {
                console.log(key + ':', value);
            }
            // Get the visit ID from the hidden input
            const visitId = $('#visitId').val();

            if(!visitId) {
                Swal.fire('Error', 'Visit ID is missing.', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('visits.custom_update') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        Swal.fire('Success', response.message, 'success');
                        $('#updateVisitModal').modal('hide');
                        fetchVisits(); // Refresh the visits list
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error updating visit:", error);
                    Swal.fire('Error', 'An error occurred while updating the visit.', 'error');
                }
            });
        });

        // Helper function to escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }


          // Function to calculate distance between two coordinates using Haversine formula
          function calculateDistance(lat1, lon1, lat2, lon2) {
            function toRad(x) {
                return x * Math.PI / 180;
            }

            const R = 6371; // Earth's radius in km
            const dLat = toRad(lat2 - lat1);
            const dLon = toRad(lon2 - lon1);
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                      Math.cos(toRad(lat1)) * Math.cos(toRad(lat2)) *
                      Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            const d = R * c;
            return d; // Distance in km
        }


         // Event listener for "View DCR Details" button
         visitsListEl.on('click', '.view-dcr-btn', function(e) {
            e.preventDefault();
            const visit = $(this).data('visit');

            // Populate the modal with visit details
            populateViewDCRModal(visit);

            // Show the modal
            $('#viewDCRVisitModal').modal('show');

        
        });

          // Event listener for "View DCR Details" button
          visitsListEl.on('click', '.view-sourcing-dcr-btn', function(e) {
            e.preventDefault();
            const visit = $(this).data('visit');

            // Populate the modal with visit details
            populateViewSourcingDCRModal(visit);

            // Show the modal
            $('#viewSourcingDCRVisitModal').modal('show');

        
        });

        // Function to populate the View DCR Details Modal
        function populateViewDCRModal(visit) {
            console.log("populateViewDCRModal");
            console.log(visit);
            // Safely access 'extendedProps'
            const extendedProps = visit.extendedProps || {};

            // Populate Visit Information
            $('#view_bloodBankDisplay').text(escapeHtml(extendedProps.blood_bank_name) || '-');
            $('#view_plannedQuantityDisplay').text(extendedProps.quantity || '0');
            $('#view_availableQuantityDisplay').text(extendedProps.available_quantity || '0');
            $('#view_remainingQuantityDisplay').text(extendedProps.remaining_quantity || '0');
            $('#view_priceDisplay').text(extendedProps.price !== null ? escapeHtml(extendedProps.price) : 'N/A');
            $('#view_timeDisplay').text(formatTime(visit.time) || 'N/A');
            $('#view_tpRemarksDisplay').text(escapeHtml(extendedProps.remarks) || 'N/A');
            $('#view_pendingDocumentsDisplay').text(visit.pending_document_names && visit.pending_document_names.length > 0 ? escapeHtml(visit.pending_document_names.join(', ')) : 'None');
            $('#view_addedByDisplay').text(escapeHtml(extendedProps.created_by_name) || 'N/A');

            // Populate Transport Information
            if(extendedProps.transport_details) {
                $('#view_driverNameDisplay').text(escapeHtml(extendedProps.transport_details.driver_name) || 'N/A');
                $('#view_driverContactDisplay').text(escapeHtml(extendedProps.transport_details.contact_number) || 'N/A');
                $('#view_vehicleNumberDisplay').text(escapeHtml(extendedProps.transport_details.vehicle_number) || 'N/A');
                $('#view_driverRemarksDisplay').text(escapeHtml(extendedProps.transport_details.remarks) || 'N/A');
            } else {
                $('#view_driverNameDisplay').text('N/A');
                $('#view_driverContactDisplay').text('N/A');
                $('#view_vehicleNumberDisplay').text('N/A');
                $('#view_driverRemarksDisplay').text('N/A');
            }

            // Clear existing attachments
            $('#view_certificateOfQualityAttachments').empty();
            $('#view_donorReportAttachments').empty();
            $('#view_invoiceCopyAttachments').empty();
            $('#view_pendingDocumentsAttachments').empty();

            // Map attachment_type to their respective sections
            const attachmentMap = {
                1: '#view_certificateOfQualityAttachments',
                2: '#view_donorReportAttachments',
                3: '#view_invoiceCopyAttachments',
                4: '#view_pendingDocumentsAttachments'
            };

            // Iterate over dcr_attachments and append them to respective sections
            if(extendedProps.dcr_attachments && extendedProps.dcr_attachments.length > 0) {
                extendedProps.dcr_attachments.forEach(function(att) {
                    const attachmentURL = "{{ config('auth_api.base_image_url') }}" + att.attachment;
                    const attachmentType = att.attachment_type;
                    const attachmentSection = attachmentMap[attachmentType];

                    if(attachmentSection) {
                        // Create the attachment element
                        let attachmentElement = '';

                        // Determine file type for proper rendering
                        const fileExtension = att.attachment.split('.').pop().toLowerCase();
                        const imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp'];
                        const pdfExtensions = ['pdf'];

                        if(imageExtensions.includes(fileExtension)) {
                            attachmentElement = `<a href="${attachmentURL}" target="_blank"><img src="${attachmentURL}" alt="Attachment" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;"></a>`;
                        } else if(pdfExtensions.includes(fileExtension)) {
                            attachmentElement = `<a href="${attachmentURL}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/337/337946.png" alt="PDF" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;"></a>`;
                        } else {
                            // For other file types, use a generic icon
                            attachmentElement = `<a href="${attachmentURL}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/136/136549.png" alt="File" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; margin-right: 10px;"></a>`;
                        }

                        $(attachmentSection).append(attachmentElement);
                    }
                });
            } else {
                // If no attachments, you can choose to display a message or leave it empty
                $('#view_dcrAttachmentsSection').hide();
            }
        }

        // Function to populate the View Sourcing DCR Details Modal
        function populateViewSourcingDCRModal(visit) {

            // Clear or hide any previous dynamic sections
            $('#viewSourcingDCRVisitModal .dynamic-sourcing-sections').remove();

            const extendedProps = visit.extendedProps || [];
            const sourcingVisits = extendedProps.tour_plan_visits || [];

            // If no visits exist, you can show some "No data" message
            if (sourcingVisits.length === 0) {
            // Example: Just display a message in the existing card
            $('#sourcing_city_name_display').text(extendedProps.sourcing_city_name || 'N/A');
            $('#sourcing_blood_bank_name_display').text(extendedProps.sourcing_blood_bank_name || 'N/A');
                // ... etc ...
                return;
            }

            // Option 1: Build HTML strings
            let html = '';
            sourcingVisits.forEach((sv, index) => {
            html += `
                <div class="card mb-3 dynamic-sourcing-sections">
                <div class="card-header text-white bg-secondary">
                    <strong>Sourcing Visit #${index+1}</strong>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                    <div class="col-md-6 mt-2">
                        <strong>Contact Person:</strong> ${escapeHtml(sv.sourcing_contact_person) || 'N/A'}
                    </div>
                    <div class="col-md-6 mt-2">
                        <strong>Mobile No:</strong> ${escapeHtml(sv.sourcing_mobile_number) || 'N/A'}
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Email:</strong> ${escapeHtml(sv.sourcing_email) || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Address:</strong> ${escapeHtml(sv.sourcing_address) || 'N/A'}
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>FFP Company:</strong> ${escapeHtml(sv.sourcing_ffp_company) || 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Plasma Price/Ltr:</strong> ${sv.sourcing_plasma_price != null ? sv.sourcing_plasma_price : 'N/A'}
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Potential/Month:</strong> ${sv.sourcing_potential_per_month != null ? sv.sourcing_potential_per_month : 'N/A'}
                    </div>
                    <div class="col-md-6">
                        <strong>Payment Terms:</strong> ${escapeHtml(sv.sourcing_payment_terms) || 'N/A'}
                    </div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-12">
                        <strong>Remarks:</strong> ${escapeHtml(sv.sourcing_remarks) || 'N/A'}
                    </div>
                    </div>
                </div>
                </div>
            `;
            });

            // Append the HTML to the existing modal body (or a special container)
            $('#viewSourcingDCRVisitModal .modal-body').append(html);

            // Also, if you want to populate the top-level items (like city name, added by):
            $('#sourcing_city_name_display').text(extendedProps.sourcing_city_name || 'N/A');
            $('#sourcing_status_display').text(extendedProps.status || 'N/A');
            $('#sourcing_added_by_display').text(extendedProps.created_by_name || 'N/A');

        /*    // Safely access 'extendedProps'
            const extendedProps = visit.extendedProps || {};

            // Populate Sourcing Information
            $('#sourcing_city_name_display').text(extendedProps.sourcing_city_name || 'N/A');
            $('#sourcing_blood_bank_name_display').text(extendedProps.sourcing_blood_bank_name || 'N/A');
            $('#sourcing_contact_person_display').text(extendedProps.sourcing_contact_person || 'N/A');
            $('#sourcing_mobile_number_display').text(extendedProps.sourcing_mobile_number || 'N/A');
            $('#sourcing_email_display').text(extendedProps.sourcing_email || 'N/A');
            $('#sourcing_address_display').text(extendedProps.sourcing_address || 'N/A');
            $('#sourcing_ffp_company_display').text(extendedProps.sourcing_ffp_company || 'N/A');
            $('#sourcing_plasma_price_display').text(extendedProps.sourcing_plasma_price !== null ? extendedProps.sourcing_plasma_price : 'N/A');
            $('#sourcing_potential_per_month_display').text(extendedProps.sourcing_potential_per_month !== null ? extendedProps.sourcing_potential_per_month : 'N/A');
            $('#sourcing_payment_terms_display').text(extendedProps.sourcing_payment_terms || 'N/A');
            $('#sourcing_remarks_display').text(extendedProps.sourcing_remarks || 'N/A');
            $('#sourcing_status_display').text(extendedProps.status || 'N/A');
            $('#sourcing_added_by_display').text(extendedProps.created_by_name || 'N/A');  */

        }


        // Event listener for "Submit DCR" button for Sourcing (tourPlanType === 2)
        visitDetailsContentEl.on('click', '.submit-sourcing-dcr-btn', function(e) {
            e.preventDefault();
            const encodedVisit = $(this).attr('data-visit');

            if(!encodedVisit) {
                console.error("Data-visit attribute is missing on the button.");
                Swal.fire('Error', 'Visit data is missing.', 'error');
                return;
            }

            let visit;
            try {
                visit = JSON.parse(decodeURIComponent(encodedVisit));
                console.log('Clicked Sourcing Visit:', visit); // Debugging
            } catch (error) {
                console.error("Error parsing visit data:", error);
                Swal.fire('Error', 'Failed to parse visit data.', 'error');
                return;
            }

            // Set the visit data on the modal
            $('#updateSourcingVisitModal').data('data-visit', visit);

            // Populate the Sourcing Modal with existing data
            populateUpdateSourcingModal(visit);

            // Show the modal
            $('#updateSourcingVisitModal').modal('show');

        });


        // Function to populate the Update Sourcing Visit Modal
        function populateUpdateSourcingModal(visit) {
            console.log("populateUpdateSourcingModal called with visit:", visit); // Debugging
            const extendedProps = visit.extendedProps || {};

            // Populate Existing Sourcing Details
            $('#sourcingRemarksDisplay').text(extendedProps.remarks || 'N/A');
            $('#sourcingCityDisplay').text(extendedProps.sourcing_city_name || 'N/A');
            $('#sourcingAddedByDisplay').text(extendedProps.created_by_name || 'N/A');

            // Set the visit ID in the hidden input
            $('#sourcingVisitId').val(visit.id || '');

            // Reset the form fields
            $('#updateSourcingVisitForm')[0].reset();
        }

        // Handle Sourcing Form Submission via AJAX
        $('#updateSourcingVisitForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const visitId = $('#sourcingVisitId').val();

            if(!visitId) {
                Swal.fire('Error', 'Visit ID is missing.', 'error');
                return;
            }

            $.ajax({
                url: "{{ route('visits.custom_sourcing_update') }}",
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log('AJAX Submit Response:', response); // Debugging
                    if(response.success) {
                        Swal.fire('Success', response.message, 'success');
                        $('#updateSourcingVisitModal').modal('hide');
                        fetchVisits(); // Refresh the visits list
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error submitting sourcing DCR:", error);
                    Swal.fire('Error', 'An error occurred while submitting the sourcing DCR.', 'error');
                }
            });
        });

         // Function to format time (e.g., "14:30:00" to "14:30")
         function formatTime(timeStr) {
                if (!timeStr) return '-';
                var time = timeStr.split(':');
                return `${time[0]}:${time[1]}`;
            }


            $('#finalDcrSubmitForm').on('submit', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Are you sure want to send DCR submission for today?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes Proceed',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Proceed with form submission via AJAX or regular submission.
                        // For AJAX, you could do:
                        const form = $(this);
                        $.ajax({
                            url: form.attr('action'),
                            type: 'POST',
                            data: form.serialize(), // Since the data is simple, serialize works.
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire('Success', response.message, 'success');
                                    // Optionally, refresh visits or perform other actions.
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error in Final DCR Submission:", error);
                                Swal.fire('Error', 'An error occurred while sending final DCR submission.', 'error');
                            }
                        });
                    }
                });
            });


    });
</script>


@endpush
