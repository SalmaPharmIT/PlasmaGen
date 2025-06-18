@extends('include.dashboardLayout')

@section('title', 'Manage Tour Planner')

@section('content')

<div class="pagetitle">
    <h1>Manage Tour Planner</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.index') }}">Tour Planner</a></li>
        <li class="breadcrumb-item active">Manage</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">

            <!-- Filters Row -->
            <div class="row mb-4 mt-2 align-items-end">
                <!-- Collecting Agent Dropdown -->
                <div class="col-md-4">
                    <label for="collectingAgentDropdown" class="form-label">Collecting/Sourcing Executives</label>
                    <select id="collectingAgentDropdown" class="form-select select2">
                        <option value="">Choose Collecting/Sourcing Executives</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

                <!-- Month Picker -->
                <div class="col-md-4">
                    <label for="monthPicker" class="form-label">Select Month</label>
                    <input type="month" id="monthPicker" class="form-control" value="{{ date('Y-m') }}"/>
                </div>

                <!-- Submit Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button id="filterButton" class="btn btn-success w-100">
                        <i class="bi bi-filter me-1"></i> Submit
                   </button>
                </div>
            </div>
            <!-- End Filters Row -->

            <!-- New Row for Action Buttons -->
            <div class="row mb-3" id="editRequestRow" style="display: none;">
                <div class="col-12 text-center">
                    <button id="acceptButton" class="btn btn-success btn-sm me-2">
                        <i class="bi bi-check-circle me-1"></i> Accept
                    </button>
                    <button id="rejectButton" class="btn btn-danger btn-sm me-2">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                    <button id="enableButton" class="btn btn-primary btn-sm">
                        <i class="bi bi-play-circle me-1"></i> Enable
                    </button>
                </div>
                <div class="col-12 text-center">
                    <small id="editRequestReasonDisplay" class="text-muted"></small>
                  </div>
            </div>
            

            <!-- Dates and Events Accordion Section -->
            <div class="row">
                <div class="col-12">
                    <div class="accordion" id="datesAccordion">
                        <!-- Dynamic Accordion Items Will Be Injected Here -->
                    </div>
                </div>
            </div>
            <!-- End Accordion Section -->

          </div>
        </div>

      </div>
    </div>
</section>

<!-- resources/views/tourplanner/modals/viewTourPlviewTourPlanModalanModal.blade.php -->
<div class="modal fade" id="viewTourPlanModal" tabindex="-1" aria-labelledby="viewTourPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">
                Tour Plan Details - <span id="detailTourPlanType" class="event-title">N/A</span>
              </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <!-- Common Fields -->
           
            <div class="mb-3">
              <strong>Collecting Agent:</strong> <span id="detailCollectingAgent">N/A</span>
            </div>
            <div class="mb-3">
              <strong>Date:</strong> <span id="detailDate">N/A</span>
            </div>
          
            <!-- Collection-specific Fields -->
            <div class="mb-3 collection-fields" style="display: none;">
                <strong>Blood Bank:</strong> <span id="detailBloodBank">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
                <strong>Time:</strong> <span id="detailVisitTime">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Quantity:</strong> <span id="detailQuantity">N/A</span>
            </div>
            {{-- <div class="mb-3 collection-fields" style="display: none;">
              <strong>Available Quantity:</strong> <span id="detailAvailableQuantity">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Remaining Quantity:</strong> <span id="detailRemainingQuantity">N/A</span>
            </div> --}}
            {{-- <div class="mb-3 collection-fields" style="display: none;">
              <strong>Latitude:</strong> <span id="detailLatitude">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Longitude:</strong> <span id="detailLongitude">N/A</span>
            </div> --}}
            <div class="mb-3 collection-fields" style="display: none;">
                <strong>Pending Documents:</strong>
                <ul id="detailPendingDocuments" class="list-unstyled">
                    <li class="text-muted">N/A</li>
                </ul>
            </div>
              
          
            <!-- Sourcing-specific Field -->
            <div class="mb-3 sourcing-fields" style="display: none;">
              <strong>City:</strong> <span id="detailCity">N/A</span>
            </div>

            
            <!-- Common Fields Continued -->
            {{-- <div class="mb-3">
              <strong>Status:</strong> <span id="detailStatus">N/A</span>
            </div> --}}
            <div class="mb-3">
              <strong>Remarks:</strong> <span id="detailRemarks">N/A</span>
            </div>
            <div class="mb-3">
              <strong>Created By:</strong> <span id="detailCreatedBy">N/A</span>
            </div>
            <div class="mb-3">
              <strong>Created At:</strong> <span id="detailCreatedAt">N/A</span>
            </div>
          </div>
          
        <div class="modal-footer">
          {{-- <button type="button" id="deleteTourPlanButton" class="btn btn-danger">Delete Tour Plan</button> --}}
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- View DCR Details Modal -->
<div class="modal fade" id="viewDCRVisitModal" tabindex="-1" aria-labelledby="viewDCRVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Increased size for better visibility -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DCR Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Visit Information Card -->
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Visit Information</strong></h5>
                    </div>
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
                            <!-- No. of Boxes -->
                            <div class="col-md-4">
                                <strong>No. of Boxes:</strong>
                                <p id="view_numBoxesDisplay">N/A</p>
                            </div>
                            <!-- No. of Units -->
                            <div class="col-md-4">
                                <strong>No. of Units:</strong>
                                <p id="view_numUnitsDisplay">N/A</p>
                            </div>
                            <!-- No. of Litres -->
                            <div class="col-md-4">
                                <strong>No. of Litres:</strong>
                                <p id="view_numLitresDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Part-A Invoice Price -->
                            <div class="col-md-4">
                                <strong>Part-A Invoice Price:</strong>
                                <p id="view_partAPriceDisplay">N/A</p>
                            </div>
                            <!--  Part-B Invoice Price -->
                            <div class="col-md-4">
                                <strong>Part-B Invoice Price:</strong>
                                <p id="view_partBPriceDisplay">N/A</p>
                            </div>
                            <!-- Part-C Invoice Price -->
                            <div class="col-md-4">
                                <strong>Part-C Invoice Price:</strong>
                                <p id="view_partCPriceDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Is GST Included? -->
                            <div class="col-md-4">
                                <strong>GST Included:</strong>
                                <p id="view_isGSTIncludeDisplay">N/A</p>
                            </div>
                            <!-- GST Rate -->
                            <div class="col-md-4">
                                <strong>GST Rate:</strong>
                                <p id="view_gstRateDisplay">N/A</p>
                            </div>
                            <!-- Total Invoice Price -->
                            <div class="col-md-4">
                                <strong>Total Invoice Price:</strong>
                                <p id="view_totalInvoicePriceDisplay">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Opt Other transport partner -->
                            <div class="col-md-4">
                                <strong>Other Trasnport Partner:</strong>
                                <p id="view_isOtherTrasnportPartnerDisplay">N/A</p>
                            </div>
                            <!-- Transport Name -->
                            <div class="col-md-4">
                                <strong>Transport Name:</strong>
                                <p id="view_transportNameDisplay">N/A</p>
                            </div>
                            <!-- Transport Contact Person Name  -->
                            <div class="col-md-4">
                                <strong>Transport Contact Person:</strong>
                                <p id="view_transportContactPersoneDisplay">N/A</p>
                            </div>
                            
                        </div>
                        <div class="row mb-3">
                            <!-- Transport Contact Number  -->
                            <div class="col-md-4">
                                <strong>Transport Contact Number:</strong>
                                <p id="view_transportContactNumberDisplay">N/A</p>
                            </div>
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
                            {{-- <!-- Added By -->
                            <div class="col-md-4">
                                <strong>Added By:</strong>
                                <p id="view_addedByDisplay">N/A</p>
                            </div> --}}
                        </div>
                    </div>
                </div>

                <!-- Transport Information Card -->
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Transport Information</strong></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-3">
                            <!-- Warehouse Name -->
                            <div class="col-md-4">
                                <strong>Warehouse:</strong>
                                <p id="view_warehouseNameDisplay">N/A</p>
                            </div>
                            <!-- Transport Partner Name -->
                            <div class="col-md-4">
                                <strong>Transport Partner:</strong>
                                <p id="view_transportPartnerNameDisplay">N/A</p>
                            </div>
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
                        <h5 class="mb-0"><strong>DCR Attachments</strong></h5>
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

                         <!-- Part-A Invoice Documents -->
                         <div class="mb-4">
                            <h6><strong>5. Part-A Invoices</strong></h6>
                            <div class="d-flex flex-wrap" id="view_partAInvoiceAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                        <!-- Part-B Invoice Documents -->
                        <div class="mb-4">
                            <h6><strong>6. Part-B Invoices</strong></h6>
                            <div class="d-flex flex-wrap" id="view_partBInvoiceAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                        <!-- Part-C Invoice Documents -->
                        <div class="mb-4">
                            <h6><strong>7. Part-C Invoices</strong></h6>
                            <div class="d-flex flex-wrap" id="view_partCInvoiceAttachments">
                                <!-- Attachments will be loaded here -->
                            </div>
                        </div>

                    </div>
                </div>

            </div>
            <div class="modal-footer">
                 <!-- Edit button: This button will trigger the edit modal -->
                 <button type="button" class="btn btn-warning edit-collection-visit-btn d-none" id="viewCollectionEditBtn" data-bs-dismiss="modal">Edit</button>
               
                <!-- Close Button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
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



<!-- Edit Collection Visit Modal -->
<div class="modal fade" id="editCollectionVisitModal" tabindex="-1" aria-labelledby="editCollectionVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="editVisitForm" method="POST" action="{{ route('visits.collection_edit_submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editCollectionVisitModalLabel">Edit Collection Visit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Hidden field for visit ID -->
            <input type="hidden" id="editVisitId" name="visit_id">
             <!-- Hidden input to hold existing attachment data -->
            <input type="hidden" id="editRemainingAttachments" name="existing_attachments">
  
            <!-- Basic Visit Information -->
            <div class="row mb-3">
                <input type="hidden" class="form-control" id="editPlannedQuantity" name="quantity_planned" required>
                <div class="col-md-4">
                    <label for="editQuantityCollected" class="form-label">Quantity Collected</label>
                    <input type="number" class="form-control" id="editQuantityCollected" name="quantity_collected" required>
                  </div>
              <div class="col-md-4">
                <label for="editRemainingQuantity" class="form-label">Remaining Quantity</label>
                <input type="number" class="form-control" id="editRemainingQuantity" name="quantity_remaining" required readonly>
              </div>
              <div class="col-md-4">
                <label for="editPrice" class="form-label">Price</label>
                <input type="number" class="form-control" id="editPrice" name="editPrice">
              </div>
            </div>
                          
            <!-- GST and Pricing Details -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="editPartAPrice" class="form-label">Part-A Invoice Price</label>
                    <input type="text" class="form-control" id="editPartAPrice" name="edit_part_a_price">
                </div>
                  <div class="col-md-4">
                    <label for="editPartBPrice" class="form-label">Part-B Invoice Price</label>
                    <input type="text" class="form-control" id="editPartBPrice" name="edit_part_b_price">
                </div>
                  <div class="col-md-4">
                    <label for="editPartCPrice" class="form-label">Part-C Invoice Price</label>
                    <input type="text" class="form-control" id="editPartCPrice" name="edit_part_c_price" >
                </div>
              <div class="col-md-4 mt-2">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="editIncludeGST" name="edit_collection_include_gst">
                  <label class="form-check-label" for="editIncludeGST">Include GST</label>
                </div>
              </div>
              <div class="col-md-4 mt-2" id="editGSTRateSection" style="display: none;">
                <label for="editGSTRateSelect" class="form-label">GST Rate (%)</label>
                <select class="form-select" id="editGSTRateSelect" name="edit_collection_gst_rate">
                  <option value="">-- Select GST Rate --</option>
                  <!-- Options to be populated dynamically -->
                </select>
              </div>
              <div class="col-md-4 mt-2">
                <label for="editTotalPrice" class="form-label">Total Price</label>
                <input type="text" class="form-control" id="editTotalPrice" name="edit_total_price" readonly>
              </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="editNumBoxes" class="form-label">Number of boxes</label>
                    <input type="text" class="form-control" id="editNumBoxes" name="edit_boxes_collected">
                </div>
                  <div class="col-md-4">
                    <label for="editNumUnits" class="form-label">Number of Units</label>
                    <input type="text" class="form-control" id="editNumUnits" name="edit_units_collected">
                </div>
                  <div class="col-md-4">
                    <label for="editNumLitres" class="form-label">Number of Litres</label>
                    <input type="text" class="form-control" id="editNumLitres" name="edit_litres_collected" >
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4 mt-2">
                    <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="editOtherTransportPartner" name="edit_collection_other_transport_partner">
                    <label class="form-check-label" for="editOtherTransportPartner">Other Transport Partner?</label>
                    </div>
                </div>

            </div>
            <div class="row mb-3" id="editOtherTransportPartnerSection" style="display: none;">
                <div class="col-md-4">
                    <label for="editTransportName" class="form-label">Transport Name</label>
                    <input type="text" class="form-control" id="editTransportName" name="edit_transport_name">
                </div>
                    <div class="col-md-4">
                    <label for="editTransportContactPerson" class="form-label">Transport Contact Person</label>
                    <input type="text" class="form-control" id="editTransportContactPerson" name="edit_transport_contact_person">
                </div>
                    <div class="col-md-4">
                    <label for="editTransportContactNumber" class="form-label">Transport Contact Number</label>
                    <input type="text" class="form-control" id="editTransportContactNumber" name="edit_transport_contact_number" >
                </div>
            </div>
  
            <!-- Attachments Section -->
            <h5 class="mb-3">Attachments</h5>
            <!-- Certificate of Quality -->
            <div class="mb-3">
              <label for="edit_certificate_of_quality" class="form-label">Certificate of Quality</label>
              <input type="file" class="form-control" id="edit_certificate_of_quality" name="certificate_of_quality[]" multiple accept="image/*,application/pdf">
              <div class="mt-2 d-flex flex-wrap" id="edit_certificate_of_qualityPreview">
                <!-- Existing Certificate of Quality attachments will be appended here -->
              </div>
            </div>
            <!-- Donor Report -->
            <div class="mb-3">
              <label for="edit_donor_report" class="form-label">Donor Report</label>
              <input type="file" class="form-control" id="edit_donor_report" name="donor_report[]" multiple accept="image/*,application/pdf">
              <div class="mt-2 d-flex flex-wrap" id="edit_donor_reportPreview">
                <!-- Existing Donor Report attachments -->
              </div>
            </div>
            <!-- Invoice Copy -->
            <div class="mb-3">
              <label for="edit_invoice_copy" class="form-label">Invoice Copy</label>
              <input type="file" class="form-control" id="edit_invoice_copy" name="invoice_copy[]" multiple accept="image/*,application/pdf">
              <div class="mt-2 d-flex flex-wrap" id="edit_invoice_copyPreview">
                <!-- Existing Invoice Copy attachments -->
              </div>
            </div>
            <!-- Pending Documents -->
            <div class="mb-3">
              <label for="edit_pending_documents" class="form-label">Pending Documents</label>
              <input type="file" class="form-control" id="edit_pending_documents" name="pending_documents[]" multiple accept="image/*,application/pdf">
              <div class="mt-2 d-flex flex-wrap" id="edit_pending_documentsPreview">
                <!-- Existing Pending Documents attachments -->
              </div>
            </div>
            <!-- Part-A, Part-B, Part-C Invoice Copies -->
            <div class="row mb-3">
              <div class="col-md-12">
                <label for="edit_part_a_invoice" class="form-label">Part-A Invoice Copy</label>
                <input type="file" class="form-control" id="edit_part_a_invoice" name="collectionPartAInvoice_copy[]" multiple accept="image/*,application/pdf">
                <div class="mt-2 d-flex flex-wrap" id="edit_part_a_invoicePreview">
                  <!-- Existing Part-A Invoice Copy attachments -->
                </div>
              </div>
              <div class="col-md-12">
                <label for="edit_part_b_invoice" class="form-label">Part-B Invoice Copy</label>
                <input type="file" class="form-control" id="edit_part_b_invoice" name="collectionPartBInvoice_copy[]" multiple accept="image/*,application/pdf">
                <div class="mt-2 d-flex flex-wrap" id="edit_part_b_invoicePreview">
                  <!-- Existing Part-B Invoice Copy attachments -->
                </div>
              </div>
              <div class="col-md-12">
                <label for="edit_part_c_invoice" class="form-label">Part-C Invoice Copy</label>
                <input type="file" class="form-control" id="edit_part_c_invoice" name="collectionPartCInvoice_copy[]" multiple accept="image/*,application/pdf">
                <div class="mt-2 d-flex flex-wrap" id="edit_part_c_invoicePreview">
                  <!-- Existing Part-C Invoice Copy attachments -->
                </div>
              </div>
            </div>
            <!-- Hidden input for attachments marked for deletion -->
            <input type="hidden" id="editDeletedAttachments" name="deleted_attachments">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit Changes</button>
          </div>
        </div>
      </form>
    </div>
</div>

<!-- Edit Sourcing Visit Modal -->
<div class="modal fade" id="editSourcingVisitModal" tabindex="-1" aria-labelledby="editSourcingVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="editSourcingVisitForm">
        @csrf
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="editSourcingVisitModalLabel">Edit Sourcing Visit</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <!-- Hidden field for sourcing visit id -->
            <input type="hidden" id="editSourcingVisitId" name="sourcing_visit_id">
            
            <!-- Basic Information -->
            <div class="mb-3">
              <label for="editBloodBankName" class="form-label">Blood Bank Name</label>
              <input type="text" class="form-control" id="editBloodBankName" name="blood_bank_name" required>
            </div>
            <div class="mb-3">
              <label for="editContactPerson" class="form-label">Contact Person</label>
              <input type="text" class="form-control" id="editContactPerson" name="contact_person_name" required>
            </div>
            <div class="mb-3">
              <label for="editMobileNo" class="form-label">Mobile No</label>
              <input type="text" class="form-control" id="editMobileNo" name="mobile_no" required>
            </div>
            <div class="mb-3">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label for="editAddress" class="form-label">Address</label>
              <textarea class="form-control" id="editAddress" name="address" rows="2"></textarea>
            </div>
            <div class="mb-3">
              <label for="editFFPCompany" class="form-label">FFP Procurement Company</label>
              <input type="text" class="form-control" id="editFFPCompany" name="FFPProcurementCompany">
            </div>
            
            <!-- New Fields -->
            <div class="mb-3">
              <label for="editPlasmaPrice" class="form-label">Plasma Price/Ltr</label>
              <input type="number" step="0.01" class="form-control" id="editPlasmaPrice" name="currentPlasmaPrice">
            </div>
            <div class="mb-3">
              <label for="editPotentialPerMonth" class="form-label">Potential Per Month</label>
              <input type="text" class="form-control" id="editPotentialPerMonth" name="potentialPerMonth" required>
            </div>
            <div class="mb-3">
              <label for="editPaymentTerms" class="form-label">Payment Terms</label>
              <input type="text" class="form-control" id="editPaymentTerms" name="paymentTerms" required>
            </div>
            <div class="mb-3">
              <label for="editRemarks" class="form-label">Remarks</label>
              <textarea class="form-control" id="editRemarks" name="remarks" rows="2"></textarea>
            </div>
            
            <!-- Part Prices and GST Section -->
            <div class="row mb-3">
              <div class="col-md-4">
                <label for="editSourcingPartAPrice" class="form-label">Part-A Price</label>
                <input type="number" step="0.01" class="form-control" id="editSourcingPartAPrice" name="part_a_price" value="0">
              </div>
              <div class="col-md-4">
                <label for="editSourcingPartBPrice" class="form-label">Part-B Price</label>
                <input type="number" step="0.01" class="form-control" id="editSourcingPartBPrice" name="part_b_price" value="0">
              </div>
              <div class="col-md-4">
                <label for="editSourcingPartCPrice" class="form-label">Part-C Price</label>
                <input type="number" step="0.01" class="form-control" id="editSourcingPartCPrice" name="part_c_price" value="0">
              </div>
            </div>
            <div class="row mb-3">
              <div class="col-md-4">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="editSourcingIncludeGST" name="include_gst">
                  <label class="form-check-label" for="editSourcingIncludeGST">Include GST</label>
                </div>
              </div>
              <div class="col-md-4" id="editSourcingGSTRateSection" style="display: none;">
                <label for="editSourcingGSTRateSelect" class="form-label">GST Rate (%)</label>
                <select class="form-select" id="editSourcingGSTRateSelect" name="gst_rate">
                  <option value="">-- Select GST Rate --</option>
                  <!-- GST rate options will be populated dynamically -->
                </select>
              </div>
              <div class="col-md-4">
                <label for="editTotalPlasmaPrice" class="form-label">Total Plasma Price</label>
                <input type="text" class="form-control" id="editTotalPlasmaPrice" name="total_plasma_price" readonly>
              </div>
            </div>
            
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </form>
    </div>
</div>
  

@endsection

@push('styles')
    <style>
        /* Enhance Select2 Dropdowns to Match Bootstrap */
        .select2-container--default .select2-selection--single {
            height: 38px; /* Match Bootstrap's .form-control height */
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 10px;
        }

        /* Enhance Month Picker */
        #monthPicker {
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        #monthPicker:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }


         /* === New CSS for Accordion to Match Calendar Width === */
        /* Ensure the accordion fits within the parent container */
        #datesAccordion {
            width: 100%;
            margin: 0 auto;
        }

        /* Optional: Add padding or adjust as needed */
        .accordion-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            margin-bottom: 0.5rem;
        }

        .accordion-button {
            font-weight: 500;
            /* Ensure the button doesn't exceed the parent width */
            white-space: normal;
            word-wrap: break-word;
        }

        .accordion-body {
            padding: 0.75rem 1.25rem;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .accordion-button {
                font-size: 0.9rem;
            }
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

        
    </style>
@endpush

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


        // Function to initialize file input previews
        function initializeFilePreviewEdit(inputId, previewId) {
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
             //   previewContainer.innerHTML = '';

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
        initializeFilePreview('collectionPartAInvoice_copy', 'collectionPartAInvoice_copyPreview');
        initializeFilePreview('collectionPartBInvoice_copy', 'collectionPartBInvoice_copyPreview');
        initializeFilePreview('collectionPartCInvoice_copy', 'collectionPartCInvoice_copyPreview');
        
        initializeFilePreviewEdit('edit_certificate_of_quality', 'edit_certificate_of_qualityPreview');
        initializeFilePreviewEdit('edit_donor_report', 'edit_donor_reportPreview');
        initializeFilePreviewEdit('edit_invoice_copy', 'edit_invoice_copyPreview');
        initializeFilePreviewEdit('edit_pending_documents', 'edit_pending_documentsPreview');
        initializeFilePreviewEdit('edit_part_a_invoice', 'edit_part_a_invoicePreview');
        initializeFilePreviewEdit('edit_part_b_invoice', 'edit_part_b_invoicePreview');
        initializeFilePreviewEdit('edit_part_c_invoice', 'edit_part_c_invoicePreview');

    });
</script>

@push('scripts')

    <!-- Your Custom Scripts -->
    <script>
        $(document).ready(function() {
            // Variable to store the currently selected Collecting Agent ID from filters
            var currentFilteredAgentId = null;

            // Function to populate Collecting Agents Dropdown
            function loadCollectingAgents() {
                $.ajax({
                    url: "{{ route('tourplanner.getCollectingAgents') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {

                            var agents = response.data;
                            var dropdown = $('#collectingAgentDropdown');
                            var modalDropdown = $('#tourPlanCollectingAgent'); // If modals are used
                            dropdown.empty().append('<option value="">Choose Executives</option>');
                            if (modalDropdown.length) {
                                modalDropdown.empty().append('<option value="">Choose Executives</option>');
                            }
                            $.each(agents, function(index, agent) {
                               // var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                var option = '<option value="' + agent.id + '">' + agent.name + ' (' + agent.role.role_name + ')</option>';
                                dropdown.append(option);
                                if (modalDropdown.length) {
                                    modalDropdown.append(option);
                                }
                            });
                            // Trigger Select2 to reinitialize with new options
                            dropdown.trigger('change');
                            if (modalDropdown.length) {
                                modalDropdown.trigger('change');
                            }
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching collecting agents:", error);
                        Swal.fire('Error', 'An error occurred while fetching collecting agents.', 'error');
                    }
                });
            }

            // Load Collecting Agents on page load
            loadCollectingAgents();

            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });

            // Handle Filter Button Click
            $('#filterButton').on('click', function() {
                var agentId = $('#collectingAgentDropdown').val();
                var selectedMonth = $('#monthPicker').val();

                // Store the selected agent ID
                currentFilteredAgentId = agentId ? agentId : null;

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                // Load events for the selected filters
                loadDateList(agentId, selectedMonth);
                loadTPStatus(agentId, selectedMonth)
            });

            // Function to load date list and events based on filters
            function loadDateList(agentId, selectedMonth) {
                if (!agentId) {
                    Swal.fire('Warning', 'Please select a collecting agent.', 'warning');
                    return;
                }

                // Define the API endpoint to fetch events based on filters
                var eventsApiUrl = "{{ route('tourplanner.getCalendarEvents') }}";

                $.ajax({
                    url: eventsApiUrl,
                    type: 'GET',
                    data: {
                        agent_id: agentId,
                        month: selectedMonth
                    },
                    beforeSend: function() {
                        // Show a loading indicator
                        $('#datesAccordion').html('<p class="text-center">Loading events...</p>');
                    },
                    success: function(response) {
                        if(response.success) {
                            console.log("API Response:", response); // Debugging
                            var events = response.events;

                            // Group events by date
                            var eventsByDate = {};
                            $.each(events, function(index, event) {
                                var date = event.start.split('T')[0]; // 'YYYY-MM-DD'
                                if (!eventsByDate[date]) {
                                    eventsByDate[date] = [];
                                }
                                eventsByDate[date].push(event);
                            });

                            // Generate list of all dates in the selected month
                            var allDates = getAllDatesInMonth(selectedMonth);

                            // Generate Accordion Items
                            var accordionHtml = '';

                            $.each(allDates, function(index, date) {
                                var formattedDate = formatDateDisplay(date);
                                var eventsForDate = eventsByDate[date] || [];

                                var collapseId = 'collapse-' + index;
                                var headingId = 'heading-' + index;

                                accordionHtml += `
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="${headingId}">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#${collapseId}" aria-expanded="false" aria-controls="${collapseId}">
                                                ${formattedDate} 
                                                ${eventsForDate.length > 0 
                                                    ? `<span class="badge bg-primary ms-2">Total Events: ${eventsForDate.length}</span>` 
                                                    : `<span class="badge bg-secondary ms-2">No Events</span>`}
                                            </button>
                                        </h2>
                                        <div id="${collapseId}" class="accordion-collapse collapse" aria-labelledby="${headingId}" data-bs-parent="#datesAccordion">
                                            <div class="accordion-body">
                                                ${generateEventsHtml(eventsForDate)}
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });

                            $('#datesAccordion').html(accordionHtml);

                            if (allDates.length === 0) {
                                $('#datesAccordion').html('<p class="text-center">No dates available for the selected month.</p>');
                            }
                        } else {
                            Swal.fire('Error', response.message, 'error');
                            $('#datesAccordion').empty();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching calendar events:", error);
                        Swal.fire('Error', 'An error occurred while fetching calendar events.', 'error');
                        $('#datesAccordion').empty();
                    },
                    complete: function() {
                        // Optionally, hide the loading indicator
                    }
                });
            }

            // Function to get all dates in a month (YYYY-MM)
            function getAllDatesInMonth(month) {
                var dates = [];
                var parts = month.split('-');
                var year = parseInt(parts[0], 10);
                var monthIndex = parseInt(parts[1], 10) - 1; // Months are zero-based
                var date = new Date(year, monthIndex, 1);

                while (date.getMonth() === monthIndex) {
                    var day = date.getDate();
                    var monthStr = ('0' + (date.getMonth() + 1)).slice(-2);
                    var dayStr = ('0' + day).slice(-2);
                    var dateStr = `${date.getFullYear()}-${monthStr}-${dayStr}`;
                    dates.push(dateStr);
                    date.setDate(day + 1);
                }

                return dates;
            }

            // Function to format date for display (YYYY-MM-DD, Day)
            function formatDateDisplay(dateStr) {
                var date = new Date(dateStr);
                var year = date.getFullYear();
                var month = ('0' + (date.getMonth() + 1)).slice(-2); // Months are zero-based
                var day = ('0' + date.getDate()).slice(-2);
                var weekday = date.toLocaleDateString(undefined, { weekday: 'long' });
                return `${year}-${month}-${day}, ${weekday}`;
            }

            // Function to generate HTML for events
            function generateEventsHtml(events) {
                if (events.length === 0) {
                    return '<p class="text-muted">No events for this date.</p>';
                }

                var html = '<div class="list-group">';

                $.each(events, function(index, event) {
                    // Customize event display as needed
                    var eventColor = getEventColor(event);
                    var eventTitle = event.title;
                    var eventDate = formatDateDisplay(event.start);
                    var eventTime =  formatTime(event.time);
                  //  var eventTime = event.extendedProps.time ? formatTime(event.extendedProps.time) : 'All Day';
                    var eventStatus = event.extendedProps.status ? event.extendedProps.status.charAt(0).toUpperCase() + event.extendedProps.status.slice(1) : 'N/A';
                   // var tourPlanType = event.extendedProps.tour_plan_type === 1 ? 'Collections' : (event.extendedProps.tour_plan_type === 2 ? 'Sourcing' : 'N/A');

                    var tourPlanType = 'N/A';
                    if(event.extendedProps.tour_plan_type === 1) {
                        tourPlanType = 'Collections';
                    }
                    else  if(event.extendedProps.tour_plan_type === 2) {
                        tourPlanType = 'Sourcing';
                    }
                    else  if(event.extendedProps.tour_plan_type === 3) {
                        tourPlanType = 'Assigned Colllections';
                    }
                    else {
                        tourPlanType = 'N/A'; 
                    }
              
                    // Conditionally include 'Time' only for Collections
                    var timeHtml = (tourPlanType === 'Collections') ? `<small>Time: ${eventTime}</small>` : '';

                    // Check if status is "dcr_submitted" to show the "View DCR Details" button
                    var viewDCRButton = '';
                    if (event.extendedProps.status === 'dcr_submitted' && event.extendedProps.tour_plan_type === 1 ) {
                        viewDCRButton = `<button class="btn btn-sm btn-info mt-2 view-dcr-btn" data-event='${JSON.stringify(event)}'>
                                            View DCR Details
                                        </button>`;
                    }
                   
                    if (event.extendedProps.status === 'dcr_submitted' && event.extendedProps.tour_plan_type === 2 ) {
                        viewDCRButton = `<button class="btn btn-sm btn-info mt-2 view-sourcing-dcr-btn" data-event='${JSON.stringify(event)}'>
                                            View DCR Details
                                        </button>`;
                    }
                    

                    html += `
                        <a href="javascript:void(0)" class="list-group-item list-group-item-action flex-column align-items-start event-item" 
                           data-event-id="${event.id}" 
                           data-event-title="${eventTitle}" 
                           data-event-date="${eventDate}" 
                           data-event-time="${eventTime}" 
                           data-event-status="${eventStatus}" 
                           data-event-type="${tourPlanType}" 
                           data-event-remarks="${event.extendedProps.remarks || 'N/A'}" 
                           data-event-created-by="${event.extendedProps.created_by_name || 'N/A'}" 
                           data-event-created-at="${event.extendedProps.created_at || 'N/A'}" 
                           data-event-blood-bank="${event.extendedProps.blood_bank_name || 'N/A'}" 
                           data-event-city="${event.extendedProps.sourcing_city_name || 'N/A'}" 
                           data-event-latitude="${event.extendedProps.latitude || 'N/A'}" 
                           data-event-longitude="${event.extendedProps.longitude || 'N/A'}" 
                           data-event-quantity="${event.extendedProps.quantity || 'N/A'}" 
                           data-event-available-quantity="${event.extendedProps.available_quantity || 'N/A'}" 
                           data-event-remaining-quantity="${event.extendedProps.remaining_quantity || 'N/A'}" 
                           data-event-collecting-agent-name="${event.extendedProps.collecting_agent_name || 'N/A'}"
                           data-event-pending-documents='${JSON.stringify(event.pending_document_names || [])}'>
                           <div class="d-flex w-100 justify-content-between">
                                <h5 class="mb-1 event-title" style="color: ${eventColor};">${eventTitle}</h5>
                                ${timeHtml}
                            </div>
                            <p class="mb-1 event-details">Status: ${eventStatus} | Type: ${tourPlanType}</p>
                              ${viewDCRButton}
                        </a>
                    `;
                });

                html += '</div>';

                return html;
            }

            // Function to determine event color based on type
            function getEventColor(event) {
                // Define default colors
                var eventColor = '#6c757d'; // Gray for undefined types

                if(event.extendedProps.tour_plan_type === 1) { // Collections
                    eventColor = '#28a745'; // Green
                } else if(event.extendedProps.tour_plan_type === 2) { // Sourcing
                    eventColor = '#007bff'; // Blue
                }
                else if(event.extendedProps.tour_plan_type === 3) { // Both
                    eventColor = '#a569bd'; // Blue
                }

                return eventColor;
            }

            // Function to format time (e.g., "14:30:00" to "14:30")
            function formatTime(timeStr) {
                if (!timeStr) return '-';
                var time = timeStr.split(':');
                return `${time[0]}:${time[1]}`;
            }

            // Handle Click on Event Items to View Details
            $(document).on('click', '.event-item', function() {
                var eventId = $(this).data('event-id');
                var eventTitle = $(this).data('event-title');
                var eventDate = $(this).data('event-date');
                var eventTime = $(this).data('event-time');
                var eventStatus = $(this).data('event-status');
                var tourPlanType = $(this).data('event-type');
                var eventRemarks = $(this).data('event-remarks');
                var eventCreatedBy = $(this).data('event-created-by');
                var eventCreatedAt = $(this).data('event-created-at');
                var eventBloodBank = $(this).data('event-blood-bank');
                var eventCity = $(this).data('event-city');
                var eventLatitude = $(this).data('event-latitude');
                var eventLongitude = $(this).data('event-longitude');
                var eventQuantity = $(this).data('event-quantity');
                var eventAvailableQuantity = $(this).data('event-available-quantity');
                var eventRemainingQuantity = $(this).data('event-remaining-quantity');
                var collectingAgentName = $(this).data('event-collecting-agent-name');

                // Populate the View Tour Plan Modal with event details
                $('#detailBloodBank').text(eventBloodBank);
                $('#detailCollectingAgent').text(collectingAgentName || 'N/A');
                $('#detailDate').text(eventDate);
                $('#detailQuantity').text(eventQuantity || 'N/A');
                // $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                // $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
               // $('#detailStatus').text(eventStatus);
                $('#detailRemarks').text(eventRemarks);
                // $('#detailLatitude').text(eventLatitude || 'N/A');
                // $('#detailLongitude').text(eventLongitude || 'N/A');
                $('#detailCity').text(eventCity);
                $('#detailCreatedBy').text(eventCreatedBy);
                $('#detailCreatedAt').text(eventCreatedAt.split(' ')[0]);
                $('#detailVisitTime').text(eventTime || 'N/A');

                var pendingDocuments = $(this).data('event-pending-documents') || [];
                var pendingDocumentsHtml = pendingDocuments.length 
                    ? pendingDocuments.map(doc => `<li>${doc}</li>`).join('')
                    : '<li class="text-muted">N/A</li>';
                            
             
                // Tour Plan Type
                $('#detailTourPlanType').text(tourPlanType);

                // Assign text color based on Tour Plan Type
                if(tourPlanType === 'Collections') {
                    $('#detailTourPlanType')
                        .removeClass('text-primary') // Remove blue if previously set
                        .addClass('text-success');   // Add green
                } else if(tourPlanType === 'Sourcing') {
                    $('#detailTourPlanType')
                        .removeClass('text-success') // Remove green if previously set
                        .addClass('text-primary');   // Add blue
                } else {
                    // For undefined or other types, remove both color classes
                    $('#detailTourPlanType')
                        .removeClass('text-success text-primary');
                }

                console.log('tourPlanType **************', tourPlanType);

                  // Conditionally show/hide fields based on Tour Plan Type
                if(tourPlanType === 'Collections') {
                    $('.collection-fields').show();
                    $('.sourcing-fields').hide();

                    $('#detailQuantity').text(eventQuantity || 'N/A');
                    $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                    $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
                    $('#detailLatitude').text(eventLatitude || 'N/A');
                    $('#detailLongitude').text(eventLongitude || 'N/A');
                    $('#detailCity').text('N/A'); // Not applicable
                    $('#detailPendingDocuments').html(pendingDocumentsHtml);
                } else if(tourPlanType === 'Sourcing') {
                    $('.collection-fields').hide();
                    $('.sourcing-fields').show();

                    $('#detailQuantity').text('N/A'); // Not applicable
                    $('#detailAvailableQuantity').text('N/A'); // Not applicable
                    $('#detailRemainingQuantity').text('N/A'); // Not applicable
                    $('#detailLatitude').text('N/A'); // Not applicable
                    $('#detailLongitude').text('N/A'); // Not applicable
                    $('#detailCity').text(eventCity || 'N/A');
                    $('#detailPendingDocuments').empty(); // Clear for sourcing
                } else if(tourPlanType === 'Assigned Colllections') {
                    // $('.collection-fields').show();
                    // $('.sourcing-fields').show();

                    // $('#detailQuantity').text(eventQuantity || 'N/A');
                    // $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                    // $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
                    // $('#detailLatitude').text(eventLatitude || 'N/A');
                    // $('#detailLongitude').text(eventLongitude || 'N/A');
                    // $('#detailPendingDocuments').html(pendingDocumentsHtml);
                    // $('#detailCity').text(eventCity || 'N/A');
                    $('.collection-fields').show();
                    $('.sourcing-fields').hide();

                    $('#detailQuantity').text(eventQuantity || 'N/A');
                    $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                    $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
                    $('#detailLatitude').text(eventLatitude || 'N/A');
                    $('#detailLongitude').text(eventLongitude || 'N/A');
                    $('#detailCity').text('N/A'); // Not applicable
                    $('#detailPendingDocuments').html(pendingDocumentsHtml);
                }
                else {
                    // If Tour Plan Type is undefined or 'N/A', hide all conditional fields
                    $('.collection-fields').hide();
                    $('.sourcing-fields').hide();

                    $('#detailQuantity').text('N/A');
                    $('#detailAvailableQuantity').text('N/A');
                    $('#detailRemainingQuantity').text('N/A');
                    $('#detailLatitude').text('N/A');
                    $('#detailLongitude').text('N/A');
                    $('#detailCity').text('N/A');
                    $('#detailPendingDocuments').empty(); 
                }

                // Store the tourPlanId in the modal's data attribute
                $('#viewTourPlanModal').data('tourPlanId', eventId);

                // Show the modal
                $('#viewTourPlanModal').modal('show');
            });

             // Handle Click on "View DCR Details" Button
             $(document).on('click', '.view-dcr-btn', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent the event from bubbling up to the parent <a> tag

                // Retrieve the event data from the button's data-event attribute
                var eventData = $(this).data('event');

                if (!eventData) {
                    Swal.fire('Error', 'No event data available.', 'error');
                    return;
                }

                // Populate the DCR Modal with event data
                populateViewDCRModal(eventData);

                // Show the DCR Modal
                $('#viewDCRVisitModal').modal('show');
            });

            $(document).on('click', '.view-sourcing-dcr-btn', function(e) {
                e.preventDefault();
                e.stopPropagation(); // Prevent the event from bubbling up to the parent <a> tag

                // Retrieve the event data from the button's data-event attribute
                var eventData = $(this).data('event');

                if (!eventData) {
                    Swal.fire('Error', 'No event data available.', 'error');
                    return;
                }

                // Populate the DCR Modal with event data
                populateViewSourcingDCRModal(eventData);

                // Show the DCR Modal
                $('#viewSourcingDCRVisitModal').modal('show');
            });

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
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>Sourcing Visit #${index+1} : ${escapeHtml(sv.blood_bank_name) || '-'}</strong>
                        ${ (extendedProps.status && (extendedProps.status.toLowerCase() === 'updated' || extendedProps.status.toLowerCase() === 'dcr_submitted' || extendedProps.status.toLowerCase() === 'rejected'))
                            ? `<button class="btn btn-light btn-sm edit-sourcing-visit-btn" data-sv='${JSON.stringify(sv)}'>Edit</button>`
                            : '' }
                    </div>
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
                    <!-- Extra Fields for Part Prices and GST -->
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>Part-A Price:</strong> ${sv.sourcing_part_a_price != null ? sv.sourcing_part_a_price : '-'}</div>
                        <div class="col-md-6"><strong>Part-B Price:</strong> ${sv.sourcing_part_b_price != null ? sv.sourcing_part_b_price : '-'}</div>
                    </div>
                    <div class="row mb-3">
                    <div class="col-md-6"><strong>Part-C Price:</strong> ${sv.sourcing_part_c_price != null ? sv.sourcing_part_c_price : '-'}</div>
                        <div class="col-md-6"><strong>Include GST:</strong> ${sv.include_gst == 1 ? 'Yes' : 'No'}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6"><strong>GST Rate (%):</strong> ${sv.gst_rate != null ? sv.gst_rate : '-'}</div>
                        <div class="col-md-6"><strong>Total Plasma Price:</strong> ${sv.sourcing_total_plasma_price != null ? sv.sourcing_total_plasma_price : '-'}</div>
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

            // Function to populate the View DCR Details Modal
            function populateViewDCRModal(event) {
                console.log("populateViewDCRModal");
                console.log(event);

                
                // Check if the visit's status meets the condition.
                if (event.extendedProps && event.extendedProps.status) {
                    const status = event.extendedProps.status.toLowerCase();
                    if (status === 'updated' || status === 'dcr_submitted' || status === 'rejected') {
                        $('#viewCollectionEditBtn').removeClass('d-none');
                    } else {
                        $('#viewCollectionEditBtn').addClass('d-none');
                    }
                } else {
                    $('#viewCollectionEditBtn').addClass('d-none');
                }

                // Safely access 'extendedProps'
                const extendedProps = event.extendedProps || {};

                // Populate Visit Information
                $('#view_bloodBankDisplay').text(extendedProps.blood_bank_name || 'N/A');
                $('#view_plannedQuantityDisplay').text(extendedProps.quantity !== null ? extendedProps.quantity : '0');
                $('#view_availableQuantityDisplay').text(extendedProps.available_quantity !== null ? extendedProps.available_quantity : '0');
                $('#view_remainingQuantityDisplay').text(extendedProps.remaining_quantity !== null ? extendedProps.remaining_quantity : '0');
                $('#view_priceDisplay').text(extendedProps.price !== null ? extendedProps.price : 'N/A');
                $('#view_numBoxesDisplay').text(extendedProps.num_boxes !== null ? extendedProps.num_boxes : '-');
                $('#view_numUnitsDisplay').text(extendedProps.num_units !== null ? extendedProps.num_units : '-');
                $('#view_numLitresDisplay').text(extendedProps.num_litres !== null ? extendedProps.num_litres : '-');
                $('#view_partAPriceDisplay').text(extendedProps.part_a_invoice_price !== null ? extendedProps.part_a_invoice_price : '0');
                $('#view_partBPriceDisplay').text(extendedProps.part_b_invoice_price !== null ? extendedProps.part_b_invoice_price : '0');
                $('#view_partCPriceDisplay').text(extendedProps.part_c_invoice_price !== null ? extendedProps.part_c_invoice_price : '0');
                $('#view_isGSTIncludeDisplay').text(extendedProps.include_gst == 1 ? 'Yes' : 'No');
                $('#view_gstRateDisplay').text(extendedProps.gst_rate !== null ? extendedProps.gst_rate : '0');
                $('#view_totalInvoicePriceDisplay').text(extendedProps.collection_total_plasma_price !== null ? extendedProps.collection_total_plasma_price : '0');
                $('#view_isOtherTrasnportPartnerDisplay').text(extendedProps.other_transportation == 1 ? 'Yes' : 'No');
                $('#view_transportNameDisplay').text(extendedProps.transportation_name !== null ? extendedProps.transportation_name : '0');
                $('#view_transportContactPersoneDisplay').text(extendedProps.transportation_contact_person !== null ? extendedProps.transportation_contact_person : '0');
                $('#view_transportContactNumberDisplay').text(extendedProps.transportation_contact_number !== null ? extendedProps.transportation_contact_number : '0');
                $('#view_timeDisplay').text(event.time || 'N/A');
                $('#view_tpRemarksDisplay').text(extendedProps.remarks || 'N/A');
                $('#view_pendingDocumentsDisplay').text(
                    event.pending_document_names && event.pending_document_names.length > 0 
                        ? event.pending_document_names.join(', ') 
                        : 'None'
                );
                $('#view_addedByDisplay').text(extendedProps.created_by_name || 'N/A');

                // Populate Transport Information
                if(extendedProps.transport_details) {
                    $('#view_warehouseNameDisplay').text(extendedProps.transport_details.warehouse_name || 'N/A');
                    $('#view_transportPartnerNameDisplay').text(extendedProps.transport_details.transport_partner_name || 'N/A');
                    $('#view_driverNameDisplay').text(extendedProps.transport_details.driver_name || 'N/A');
                    $('#view_driverContactDisplay').text(extendedProps.transport_details.contact_number || 'N/A');
                    $('#view_vehicleNumberDisplay').text(extendedProps.transport_details.vehicle_number || 'N/A');
                    $('#view_driverRemarksDisplay').text(extendedProps.transport_details.remarks || 'N/A');
                } else {
                    $('#view_warehouseNameDisplay').text('N/A');
                    $('#view_transportPartnerNameDisplay').text('N/A');
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
                $('#view_partAInvoiceAttachments').empty();
                $('#view_partBInvoiceAttachments').empty();
                $('#view_partCInvoiceAttachments').empty();


                // Map attachment_type to their respective sections
                const attachmentMap = {
                    1: '#view_certificateOfQualityAttachments',
                    2: '#view_donorReportAttachments',
                    3: '#view_invoiceCopyAttachments',
                    4: '#view_pendingDocumentsAttachments',
                    5: '#view_partAInvoiceAttachments',
                    6: '#view_partBInvoiceAttachments',
                    7: '#view_partCInvoiceAttachments'
                };

                // Iterate over dcr_attachments and append them to respective sections
                if(extendedProps.dcr_attachments && extendedProps.dcr_attachments.length > 0) {
                    extendedProps.dcr_attachments.forEach(function(att) {
                        const baseImageUrl = "{{ config('auth_api.base_image_url') }}"; // Ensure this config is set correctly
                        const attachmentURL = baseImageUrl + att.attachment;
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
                    $('#view_certificateOfQualityAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_donorReportAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_invoiceCopyAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_pendingDocumentsAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_partAInvoiceAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_partBInvoiceAttachments').html('<p class="text-muted">No attachments available.</p>');
                    $('#view_partCInvoiceAttachments').html('<p class="text-muted">No attachments available.</p>');
                }

                $("#viewDCRVisitModal").data("visit", event);
            }



             // Function to populate Blood Banks Dropdown
             function loadTPStatus(agentId, selectedMonth) {
                    if (!agentId) {
                        // Don't attempt to call the API if the employeeId is empty.
                        return;
                    }
                    console.log("loadTPStatus employeeId: ", agentId);
                    console.log("loadTPStatus selectedMonth: ", selectedMonth);

                    $.ajax({
                        url: "{{ route('visits.getEmployeesTPStatus') }}",
                        type: 'GET',
                        data: { auth_user_id: agentId, selectedMonth: selectedMonth },
                        success: function(response) {
                            console.log("loadTPStatus response: ", response);
                            // First, hide all action buttons and the edit request row.
                            $("#acceptButton, #rejectButton, #enableButton, #editRequestRow").hide();

                            if (response.success) {
                                var status = response.data.tp_status;
                                var editRequest = response.data.edit_request; // Expected 1 or 0
                                var editRequestReason = response.data.edit_request_reason || '';

                                // If tp_status is "submitted", show Accept and Reject buttons.
                                if (status.toLowerCase() === 'submitted') {
                                    $("#acceptButton, #rejectButton").show();
                                    $("#editRequestRow").show();
                                }

                                // If edit_request equals 1, show the Enable button and display the reason below.
                                if (parseInt(editRequest) === 1) {
                                    $("#enableButton").show();
                                    $("#editRequestReasonDisplay").text(editRequestReason);
                                    $("#editRequestRow").show();
                                }
                            } else {
                                // If the API call was not successful, you may choose to set a default state.
                                $("#acceptButton, #rejectButton, #enableButton, #editRequestRow").hide();
                            }       
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching TPStatus:", error);
                            Swal.fire('Error', 'An error occurred while fetching TPStatus.', 'error');
                        }
                    });
            }



            // Handle Enable Button Click
            $("#enableButton").on('click', function() {
                var agentId = currentFilteredAgentId;
                var selectedMonth = $('#monthPicker').val();

                console.log('enableButton agentId', agentId);
                console.log('enableButton selectedMonth', selectedMonth);
                
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to enable this month's tour plan?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, enable it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if(result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tourplanner.submitEditRequest') }}", // Make sure this route is defined in your web.php
                            type: 'POST',
                            data: {
                                agent_id: agentId,
                                month: selectedMonth,
                                edit_request: 0,  // Set to 0 to "enable"
                                edit_request_reason: "-", // Reason can be empty or you can send a default message
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function(){
                                Swal.fire({
                                    title: 'Enabling...',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); }
                                });
                            },
                            success: function(response) {
                                console.log("Enable Response: ", response);
                                if(response.success){
                                    Swal.fire('Enabled!', 'Tour plan has been enabled.', 'success');
                                    loadTPStatus(agentId, selectedMonth);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error enabling tour plan:", error);
                                Swal.fire('Error', 'An error occurred while enabling the tour plan.', 'error');
                            }
                        });
                    }
                });
            });

            // Handle Accept Button Click
            $("#acceptButton").on('click', function() {
                var agentId = currentFilteredAgentId;
                var selectedMonth = $('#monthPicker').val();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to accept this month's tour plan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745', // green
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Accept it!'
                }).then((result) => {
                    if(result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tourplanner.submitMonthlyTourPlan') }}",
                            type: 'POST',
                            data: {
                                agent_id: agentId,
                                month: selectedMonth,
                                status: 'accepted',
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Accepting...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                console.log("Accept response: ", response);
                                if(response.success) {
                                    let msg = response.message;
                                    if(response.data.updated_rows == 0) {
                                        msg += " (No records were updated.)";
                                    }
                                    Swal.fire('Accepted!', msg, 'success');
                                    loadTPStatus(agentId, selectedMonth);
                                    loadCalendarEvents(agentId, selectedMonth);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error accepting tour plan:", error);
                                Swal.fire('Error', 'An error occurred while accepting the tour plan.', 'error');
                            }
                        });
                    }
                });
            });

            // Handle Reject Button Click
            $("#rejectButton").on('click', function() {
                var agentId = currentFilteredAgentId;
                var selectedMonth = $('#monthPicker').val();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to reject this month's tour plan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, Reject it!'
                }).then((result) => {
                    if(result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tourplanner.submitMonthlyTourPlan') }}",
                            type: 'POST',
                            data: {
                                agent_id: agentId,
                                month: selectedMonth,
                                status: 'rejected',
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Rejecting...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                console.log("Reject response: ", response);
                                if(response.success) {
                                    let msg = response.message;
                                    if(response.data.updated_rows == 0) {
                                        msg += " (No records were updated.)";
                                    }
                                    Swal.fire('Rejected!', msg, 'success');
                                    loadTPStatus(agentId, selectedMonth);
                                    loadCalendarEvents(agentId, selectedMonth);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error rejecting tour plan:", error);
                                Swal.fire('Error', 'An error occurred while rejecting the tour plan.', 'error');
                            }
                        });
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



             // Edit Collection Update Visit section ***************************************************

            // Global variable to store the list of remaining existing attachments.
            var existingAttachments = [];

            // Function to update the hidden input field with the current list of remaining attachments.
            function updateRemainingAttachmentsField() {
                // You can send the data as JSON (or as a comma-separated string if you prefer)
                $('#editRemainingAttachments').val(JSON.stringify(existingAttachments));
            }

            // Define the function in the global scope so that inline onclick can call it
            window.deleteExistingAttachment = function(button) {
                var previewItem = $(button).closest('.preview-item');
                var attachmentId = previewItem.data('attachment-id');
                
                // Remove this attachment from the global array
                existingAttachments = existingAttachments.filter(function(item) {
                    return item !== attachmentId;
                });
                
                // Update the hidden input field with the final list
                updateRemainingAttachmentsField();
                
                // Remove the preview from the UI
                previewItem.remove();
            };


            // This function is called when the user clicks the "Edit" button in your view modal.
            $(document).on('click', '.edit-collection-visit-btn', function() {
            // Retrieve the visit data stored on the view modal.
            var visit = $('#viewDCRVisitModal').data('visit');
            if (!visit) {
                console.error("No visit data found!");
                return;
            }
            var extended = visit.extendedProps || {};


            // Fetch GST Rates Details
            var modal = $('#editCollectionVisitModal');
            fetchGSTRates(modal);

            // Populate basic fields.
            $('#editVisitId').val(visit.id || '');
            $('#editBloodBankName').val(extended.blood_bank_name || '');
            $('#editPlannedQuantity').val(extended.quantity || '');
            $('#editTime').val(visit.time ? formatTime(visit.time) : '');
            $('#editPrice').val(extended.price || '');
            $('#editRemarks').val(extended.remarks || '');
            $('#editQuantityCollected').val(extended.available_quantity || '');
            $('#editRemainingQuantity').val(extended.remaining_quantity || '');


            // Populate GST section (example):
            if (extended.include_gst == 1) {
                $('#editIncludeGST').prop('checked', true);
                $('#editGSTRateSection').show();
                $('#editGSTRateSelect').val(extended.gst_rate || '');
            } else {
                $('#editIncludeGST').prop('checked', false);
                $('#editGSTRateSection').hide();
                $('#editGSTRateSelect').val('');
            }
            $('#editPartAPrice').val(extended.part_a_invoice_price || '');
            $('#editPartBPrice').val(extended.part_b_invoice_price || '');
            $('#editPartCPrice').val(extended.part_c_invoice_price || '');
            $('#editTotalPrice').val(extended.collection_total_plasma_price || '');
            $('#editNumBoxes').val(extended.num_boxes || '');
            $('#editNumUnits').val(extended.num_units || '');
            $('#editNumLitres').val(extended.num_litres || '');

            // Populate GST section (example):
            if (extended.other_transportation == 1) {
                $('#editOtherTransportPartner').prop('checked', true);
                $('#editOtherTransportPartnerSection').show();
                $('#editTransportName').val(extended.transportation_name || '');
                $('#editTransportContactPerson').val(extended.transportation_contact_person || '');
                $('#editTransportContactNumber').val(extended.transportation_contact_number || '');
            } else {
                $('#editOtherTransportPartner').prop('checked', false);
                $('#editOtherTransportPartnerSection').hide();
                $('#editTransportName').val('');
                $('#editTransportContactPerson').val('');
                $('#editTransportContactNumber').val('');
            }



            // (You can also compute and set total price if needed.)

            // Clear existing previews in all attachment preview containers.
            $('#edit_certificate_of_qualityPreview, #edit_donor_reportPreview, #edit_invoice_copyPreview, #edit_pending_documentsPreview, #edit_part_a_invoicePreview, #edit_part_b_invoicePreview, #edit_part_c_invoicePreview').empty();

            // Reset the global array.
            existingAttachments = [];

            // Base URL for attachments.
            var baseImageUrl = "{{ config('auth_api.base_image_url') }}";

            // A helper function to create a preview element for an existing attachment.
            function createPreviewElement(att) {
                var attachmentURL = baseImageUrl + att.attachment;
                var fileExt = att.attachment.split('.').pop().toLowerCase();
                var previewElement = '';
                if (['jpg', 'jpeg', 'png', 'gif', 'bmp'].includes(fileExt)) {
                    previewElement = `
                        <div class="preview-item" data-attachment-id="${att.attachment}">
                            <button class="delete-btn" onclick="deleteExistingAttachment(this)">×</button>
                            <img src="${attachmentURL}" alt="Attachment">
                        </div>`;
                } else if (fileExt === 'pdf') {
                    previewElement = `
                        <div class="preview-item" data-attachment-id="${att.attachment}">
                            <button class="delete-btn" onclick="deleteExistingAttachment(this)">×</button>
                            <embed src="${attachmentURL}" type="application/pdf" style="width:100px; height:100px;">
                        </div>`;
                } else {
                    previewElement = `
                        <div class="preview-item" data-attachment-id="${att.attachment}">
                            <button class="delete-btn" onclick="deleteExistingAttachment(this)">×</button>
                            <span>${att.attachment}</span>
                        </div>`;
                }
                return previewElement;
            }

                // Loop through existing attachments and render them
                if (extended.dcr_attachments && extended.dcr_attachments.length > 0) {
                    extended.dcr_attachments.forEach(function(att) {
                        existingAttachments.push(att.attachment);
                        var previewHtml = createPreviewElement(att);
                        // Append to the correct container based on attachment type
                        switch (att.attachment_type) {
                            case 1:
                                $('#edit_certificate_of_qualityPreview').append(previewHtml);
                                break;
                            case 2:
                                $('#edit_donor_reportPreview').append(previewHtml);
                                break;
                            case 3:
                                $('#edit_invoice_copyPreview').append(previewHtml);
                                break;
                            case 4:
                                $('#edit_pending_documentsPreview').append(previewHtml);
                                break;
                            case 5:
                                $('#edit_part_a_invoicePreview').append(previewHtml);
                                break;
                            case 6:
                                $('#edit_part_b_invoicePreview').append(previewHtml);
                                break;
                            case 7:
                                $('#edit_part_c_invoicePreview').append(previewHtml);
                                break;
                        }
                    });
                } else {
                    $('#edit_certificate_of_qualityPreview').html('<p>No attachments found.</p>');
                }

                // Update the hidden field with the initial list of attachments
                updateRemainingAttachmentsField();

                // Finally, show the edit modal.
                $('#editCollectionVisitModal').modal('show');

            });

            // Helper function to format time (if needed).
            function formatTime(timeStr) {
            if (!timeStr) return '';
            var parts = timeStr.split(':');
            return parts[0] + ':' + parts[1];
            }

            // This function runs when the user clicks the delete button on an existing attachment.
            function deleteExistingAttachment(button) {
                var previewItem = $(button).closest('.preview-item');
                var attachmentId = previewItem.data('attachment-id');
                
                // Remove the attachmentId from the existingAttachments array.
                existingAttachments = existingAttachments.filter(function(item) {
                    return item !== attachmentId;
                });
                // Update the hidden field with the new list.
                updateRemainingAttachmentsField();
                // Remove the preview from the UI.
                previewItem.remove();
            }



            $('#editVisitForm').on('submit', function(e) {
                e.preventDefault();

                // Show loading alert
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            
                // Create FormData from the form
                var formData = new FormData(this);

                // (Optional) You can log the hidden field value:
                console.log('Existing attachments:', $('#editRemainingAttachments').val());

                // Continue with your AJAX submission...
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        Swal.close();   // Close loading alert
                        if(response.success) {
                            Swal.fire('Success', response.message, 'success');
                            $('#editCollectionVisitModal').modal('hide');
                            fetchVisits(); // Refresh the visits list
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();   // Close loading alert
                        console.error("Error updating visit:", error);
                        Swal.fire('Error', 'An error occurred while updating the visit.', 'error');
                    }
                });
                });

                function calculateEditTotalPrice() {
                    // Get the Part-A, Part-B, and Part-C invoice prices
                    var partA = parseFloat($('#editPartAPrice').val()) || 0;
                    var partB = parseFloat($('#editPartBPrice').val()) || 0;
                    var partC = parseFloat($('#editPartCPrice').val()) || 0;
                    var total = partA + partB + partC;
                    
                    // If GST is included, add GST to the total
                    if ($('#editIncludeGST').is(':checked')) {
                        var gstRate = parseFloat($('#editGSTRateSelect').val()) || 0;
                        total += total * (gstRate / 100);
                    }
                    
                    // Update the total price field with the calculated value (2 decimal places)
                    $('#editTotalPrice').val(total.toFixed(2));
                }

                // Listen for changes on Part-A, Part-B, and Part-C inputs
                $('#editPartAPrice, #editPartBPrice, #editPartCPrice').on('input', calculateEditTotalPrice);

                // Listen for changes on the "Include GST" checkbox
                $('#editIncludeGST').on('change', function() {
                if ($(this).is(':checked')) {
                    // Show GST dropdown if checked
                    $('#editGSTRateSection').show();
                } else {
                    // Hide GST dropdown if unchecked and clear its value
                    $('#editGSTRateSection').hide();
                    $('#editGSTRateSelect').val('');
                }
                calculateEditTotalPrice();
                });

                // Listen for changes on the GST rate dropdown
                $('#editGSTRateSelect').on('change', calculateEditTotalPrice);

                $(document).on('click', '.preview-item .delete-btn', function() {
                    var previewItem = $(this).closest('.preview-item');
                    var attachmentId = previewItem.data('attachment-id');
                    existingAttachments = existingAttachments.filter(function(item) {
                        return item !== attachmentId;
                    });
                    updateRemainingAttachmentsField();
                    previewItem.remove();
                });


                $('#editQuantityCollected').on('input', function () {
                    // Get the planned quantity (which is in a hidden input)
                    const planned = parseFloat($('#editPlannedQuantity').val()) || 0;
                    // Get the current quantity collected from the edit field
                    const collected = parseFloat($(this).val()) || 0;
                    // Calculate the remaining quantity
                    const remaining = planned - collected;
                    // Set the value of the editRemainingQuantity field, ensuring it does not go negative.
                    $('#editRemainingQuantity').val(remaining >= 0 ? remaining : 0);
                });

            // End Edit Collection Update Visit section *******************


            // ************************** Edit Sourcing Update Visit section ***************************************************

            $(document).on('click', '.edit-sourcing-visit-btn', function(){
                // Retrieve the sourcing visit data from the button’s data attribute
                var svData = $(this).data('sv');

                console.log('svData', svData);

                // Finally, show the edit modal.
                $('#viewSourcingDCRVisitModal').modal('hide');

                // Fetch GST Rates Details
                var modal = $('#editSourcingVisitModal');
                fetchGSTRates(modal);
                
                // Basic Fields
                $('#editSourcingVisitId').val(svData.id || '');
                $('#editBloodBankName').val(svData.blood_bank_name || '');
                $('#editContactPerson').val(svData.sourcing_contact_person || '');
                $('#editMobileNo').val(svData.sourcing_mobile_number || '');
                $('#editEmail').val(svData.sourcing_email || '');
                $('#editAddress').val(svData.sourcing_address || '');
                $('#editFFPCompany').val(svData.sourcing_ffp_company || '');
                
                // Additional auto-filled fields
                $('#editPlasmaPrice').val(svData.sourcing_plasma_price || '');
                $('#editPotentialPerMonth').val(svData.sourcing_potential_per_month || '');
                $('#editPaymentTerms').val(svData.sourcing_payment_terms || '');
                $('#editRemarks').val(svData.sourcing_remarks || '');
                $('#editSourcingPartAPrice').val(svData.sourcing_part_a_price || 0);
                $('#editSourcingPartBPrice').val(svData.sourcing_part_b_price || 0);
                $('#editSourcingPartCPrice').val(svData.sourcing_part_c_price || 0);
                
            
                // Populate GST section (example):
                if (svData.include_gst == 1) {
                    $('#editSourcingIncludeGST').prop('checked', true);
                    $('#editSourcingGSTRateSection').show();
                    $('#editSourcingGSTRateSelect').val(svData.gst_rate || '');
                } else {
                    $('#editSourcingIncludeGST').prop('checked', false);
                    $('#editSourcingGSTRateSection').hide();
                    $('#editSourcingGSTRateSelect').val('');
                }
                $('#editSourcingGSTRateSelect').val(svData.gst_rate || '');
                
                // Set the Total Plasma Price (should be readonly)
                $('#editTotalPlasmaPrice').val(svData.sourcing_total_plasma_price || '');
                
                // Finally, show the modal.
                $('#editSourcingVisitModal').modal('show');
            });

            // Function to calculate total plasma price in the edit modal
            function calculateEditTotalPlasmaPrice() {
            // Parse Part prices or default to 0
            var partA = parseFloat($('#editSourcingPartAPrice').val()) || 0;
            var partB = parseFloat($('#editSourcingPartBPrice').val()) || 0;
            var partC = parseFloat($('#editSourcingPartCPrice').val()) || 0;
            var total = partA + partB + partC;

            // If GST is included, add GST percentage to total
            if ($('#editSourcingIncludeGST').is(':checked')) {
                var gstRate = parseFloat($('#editSourcingGSTRateSelect').val()) || 0;
                total += total * (gstRate / 100);
            }

            // Set the Total Plasma Price field to the calculated value (fixed to 2 decimals)
            $('#editTotalPlasmaPrice').val(total.toFixed(2));
            }

            // Event listeners for recalculation
            $('#editSourcingPartAPrice, #editSourcingPartBPrice, #editSourcingPartCPrice').on('input', calculateEditTotalPlasmaPrice);

            // Listen for changes on the "Include GST" checkbox
            $('#editSourcingIncludeGST').on('change', function() {
                if ($(this).is(':checked')) {
                    // Show GST dropdown if checked
                    $('#editSourcingGSTRateSection').show();
                } else {
                    // Hide GST dropdown if unchecked and clear its value
                    $('#editSourcingGSTRateSection').hide();
                    $('#editSourcingGSTRateSelect').val('');
                }
                calculateEditTotalPlasmaPrice();
            });

            // Listen for changes on the GST rate dropdown
            $('#editSourcingGSTRateSelect').on('change', calculateEditTotalPlasmaPrice);

            $('#editSourcingVisitForm').on('submit', function(e){
                e.preventDefault();

                // Show loading alert
                Swal.fire({
                    title: 'Updating...',
                    text: 'Please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData(this);
                
                $.ajax({
                    url: "{{ route('visits.sourcing_edit_submit') }}", // change to your route
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(response) {
                        if(response.success){
                            Swal.close();   // Close loading alert
                            Swal.fire('Success', response.message, 'success');
                            $('#editSourcingVisitModal').modal('hide');
                            fetchVisits(); // Refresh the visits list
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.close();   // Close loading alert
                        Swal.fire('Error', 'An error occurred while updating the sourcing visit.', 'error');
                    }
                });
            });

            // ************************** End Edit Sourcing Update Visit section ***************************************************


            // When the checkbox changes, show or hide the extra transportation fields.
            $('#differentTransportPartnerCheckbox').on('change', function () {
                if ($(this).is(':checked')) {
                    $('#differentTransportDetailsRow').slideDown();
                } else {
                    $('#differentTransportDetailsRow').slideUp();
                    // Clear the text boxes when unchecked
                    $('#transportationName').val('');
                    $('#transportationContactPerson').val('');
                    $('#transportationContactNumber').val('');
                }
            });


            // Function to fetch GST Rates when modal opens
            function fetchGSTRates(modal) {
                return $.ajax({
                    url: "{{ route('visits.getSourcingGSTRates') }}",
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if(response.success) {
                            GSTRates = response.data;
                            console.log('GST Rates:', GSTRates);

                        
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching GST Rates:", error);
                        Swal.fire('Error', 'An error occurred while fetching GST Rates.', 'error');
                    }
                });
            }

              // Function to calculate the total plasma price
        function calculateTotalPlasmaPrice(){
            var partA = parseFloat($('#partAPrice').val()) || 0;
            var partB = parseFloat($('#partBPrice').val()) || 0;
            var partC = parseFloat($('#partCPrice').val()) || 0;
            var sum = partA + partB + partC;
            if($('#includeGST').is(':checked')){
                var gstRate = parseFloat($('#gstRateSelect').val()) || 0;
                // Calculate GST amount as percentage of the sum
                var gstAmount = sum * (gstRate / 100);
                sum += gstAmount;
            }
            $('#totalPlasmaPrice').val(sum.toFixed(2));
        }

        // When any of the part price inputs change, recalc total
        $('#partAPrice, #partBPrice, #partCPrice').on('input', calculateTotalPlasmaPrice);

        // When the Include GST checkbox is toggled
        $('#includeGST').change(function(){
            if($(this).is(':checked')){
                $('#gstRateSection').show();
            } else {
                $('#gstRateSection').hide();
            }
            calculateTotalPlasmaPrice();
        });

        // When the GST rate selection changes
        $('#gstRateSelect').on('change', calculateTotalPlasmaPrice);

        // Populate the GST dropdown after fetching GST Rates
        function populateGSTRates(gstRates) {
            // Clear existing options (except the first placeholder)
            $('#gstRateSelect').find('option:not(:first)').remove();
            $.each(gstRates, function(index, rate) {
                $('#gstRateSelect').append(
                    $('<option>', {
                        value: rate.gst, // using the gst percentage value
                        text: rate.gst + '%'
                    })
                );
            });
        }

        // In your fetchGSTRates callback, after you confirm success:
        fetchGSTRates().done(function(response) {
            if(response.success){
                populateGSTRates(response.data);
                populateCollectionGSTRates(response.data); 
                populateEditCollectionGSTRates(response.data);
                populateEditSourcingGSTRates(response.data);
            }
        });

        // Function to populate the collection GST dropdown dynamically
        function populateEditSourcingGSTRates(gstRates) {
            // Clear any existing options except the placeholder
            $('#editSourcingGSTRateSelect').find('option:not(:first)').remove();
            $.each(gstRates, function(index, rate) {
                $('#editSourcingGSTRateSelect').append(
                    $('<option>', {
                        value: rate.gst, // assuming 'gst' holds the percentage value (e.g. 5, 12, etc.)
                        text: rate.gst + '%'
                    })
                );
            });
        }

        // Function to populate the collection GST dropdown dynamically
        function populateEditCollectionGSTRates(gstRates) {
            // Clear any existing options except the placeholder
            $('#editGSTRateSelect').find('option:not(:first)').remove();
            $.each(gstRates, function(index, rate) {
                $('#editGSTRateSelect').append(
                    $('<option>', {
                        value: rate.gst, // assuming 'gst' holds the percentage value (e.g. 5, 12, etc.)
                        text: rate.gst + '%'
                    })
                );
            });
        }


        // Function to populate the collection GST dropdown dynamically
        function populateCollectionGSTRates(gstRates) {
            // Clear any existing options except the placeholder
            $('#collectionGSTRate').find('option:not(:first)').remove();
            $.each(gstRates, function(index, rate) {
                $('#collectionGSTRate').append(
                    $('<option>', {
                        value: rate.gst, // assuming 'gst' holds the percentage value (e.g. 5, 12, etc.)
                        text: rate.gst + '%'
                    })
                );
            });
        }

        function calculateTotalCollectionPrice(){
            // Get Part Prices or default to 0
            var partA = parseFloat($('#collectionUpdatePartAPrice').val()) || 0;
            var partB = parseFloat($('#collectionUpdatePartBPrice').val()) || 0;
            var partC = parseFloat($('#collectionUpdatePartCPrice').val()) || 0;
            var sum = partA + partB + partC;
            
            // If GST is included, calculate GST amount
            if($('#includeCollectionGST').is(':checked')){
                var gstRate = parseFloat($('#collectionGSTRate').val()) || 0;
                var gstAmount = sum * (gstRate / 100);
                sum += gstAmount;
            }
            
            // Set the readonly total field (fixed to 2 decimals)
            $('#totalCollectionPrice').val(sum.toFixed(2));
        }



        });
    </script>
@endpush
