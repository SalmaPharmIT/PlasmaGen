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

<!-- resources/views/tourplanner/modals/viewTourPlanModal.blade.php -->
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
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Transport Information</strong></h5>
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

<!-- View Sourcing DCR Details Modal -->
<div class="modal fade" id="viewSourcingDCRVisitModal" tabindex="-1" aria-labelledby="viewSourcingDCRVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Adjust size as needed -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">DCR Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Sourcing Information Card -->
                <div class="card mb-4">
                    <div class="card-header text-black">
                        <h5 class="mb-0"><strong>Sourcing Information</strong></h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3 mt-3">
                            <!-- Sourcing City -->
                            <div class="col-md-4">
                                <strong>Sourcing City:</strong>
                                <p id="sourcing_city_name_display">N/A</p>
                            </div>
                            <!-- Sourcing Blood Bank Name -->
                            <div class="col-md-4">
                                <strong>Sourcing Blood Bank Name:</strong>
                                <p id="sourcing_blood_bank_name_display">N/A</p>
                            </div>
                            <!-- Contact Person Name -->
                            <div class="col-md-4">
                                <strong>Contact Person Name:</strong>
                                <p id="sourcing_contact_person_display">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Mobile No -->
                            <div class="col-md-4">
                                <strong>Mobile No:</strong>
                                <p id="sourcing_mobile_number_display">N/A</p>
                            </div>
                            <!-- Email -->
                            <div class="col-md-4">
                                <strong>Email:</strong>
                                <p id="sourcing_email_display">N/A</p>
                            </div>
                            <!-- Address -->
                            <div class="col-md-4">
                                <strong>Address:</strong>
                                <p id="sourcing_address_display">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- FFP Procurement Company -->
                            <div class="col-md-4">
                                <strong>FFP Procurement Company:</strong>
                                <p id="sourcing_ffp_company_display">N/A</p>
                            </div>
                            <!-- Current Plasma Price/Ltr -->
                            <div class="col-md-4">
                                <strong>Current Plasma Price/Ltr:</strong>
                                <p id="sourcing_plasma_price_display">N/A</p>
                            </div>
                            <!-- Potential Per Month -->
                            <div class="col-md-4">
                                <strong>Potential Per Month:</strong>
                                <p id="sourcing_potential_per_month_display">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Payment Terms -->
                            <div class="col-md-4">
                                <strong>Payment Terms:</strong>
                                <p id="sourcing_payment_terms_display">N/A</p>
                            </div>
                            <!-- Remarks -->
                            <div class="col-md-4">
                                <strong>Remarks:</strong>
                                <p id="sourcing_remarks_display">N/A</p>
                            </div>
                        </div>
                        <div class="row mb-3">
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
    </style>
@endpush

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
                        tourPlanType = 'Both';
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
                } else if(tourPlanType === 'Both') {
                    $('.collection-fields').show();
                    $('.sourcing-fields').show();

                    $('#detailQuantity').text(eventQuantity || 'N/A');
                    $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                    $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
                    $('#detailLatitude').text(eventLatitude || 'N/A');
                    $('#detailLongitude').text(eventLongitude || 'N/A');
                    $('#detailPendingDocuments').html(pendingDocumentsHtml);
                    $('#detailCity').text(eventCity || 'N/A');
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
                // Safely access 'extendedProps'
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
                $('#sourcing_added_by_display').text(extendedProps.created_by_name || 'N/A');

            }

            // Function to populate the View DCR Details Modal
            function populateViewDCRModal(event) {
                console.log("populateViewDCRModal");
                console.log(event);
                // Safely access 'extendedProps'
                const extendedProps = event.extendedProps || {};

                // Populate Visit Information
                $('#view_bloodBankDisplay').text(extendedProps.blood_bank_name || 'N/A');
                $('#view_plannedQuantityDisplay').text(extendedProps.quantity !== null ? extendedProps.quantity : '0');
                $('#view_availableQuantityDisplay').text(extendedProps.available_quantity !== null ? extendedProps.available_quantity : '0');
                $('#view_remainingQuantityDisplay').text(extendedProps.remaining_quantity !== null ? extendedProps.remaining_quantity : '0');
                $('#view_priceDisplay').text(extendedProps.price !== null ? extendedProps.price : 'N/A');
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
                    $('#view_driverNameDisplay').text(extendedProps.transport_details.driver_name || 'N/A');
                    $('#view_driverContactDisplay').text(extendedProps.transport_details.contact_number || 'N/A');
                    $('#view_vehicleNumberDisplay').text(extendedProps.transport_details.vehicle_number || 'N/A');
                    $('#view_driverRemarksDisplay').text(extendedProps.transport_details.remarks || 'N/A');
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
                }
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

        });
    </script>
@endpush
