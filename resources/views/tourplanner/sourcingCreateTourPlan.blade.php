@extends('include.dashboardLayout')

@section('title', 'Sourcing Agent Create Tour Planner')

@section('content')

<div class="pagetitle">
    <h1>Create Tour Planner</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.index') }}">Tour Planner</a></li>
        <li class="breadcrumb-item active">Create</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
      <div class="col-lg-12">

        <div class="card">
          <div class="card-body">

          <!-- Filters Row -->
            <div class="row mb-4 mt-3 align-items-end">
                <input type="hidden" id="monthPicker" class="form-control" value="{{ date('Y-m') }}"/>
                
                <!-- TP Status Display: Full width on mobile, half width centered on md+ -->
                <div class="col-12 col-md-6 text-center mb-2 mb-md-0">
                <h5>TP Status: <span id="tpStatusValue">--</span></h5>
                </div>
            
                <!-- Submit Button: Full width on mobile, half width right aligned on md+ -->
                <div class="col-12 col-md-6 text-end">
                <button id="submitTourPlanButton" class="btn btn-success btn-bg">
                    <i class="bi bi-send-check me-1"></i> Tour Plan Submit
                </button>
                </div>
            </div>
            <!-- End Filters Row -->
  


            <!-- Calendar Section -->
            <div class="row">
                <div class="col-12">
                    <div id="tourCalendar" class="w-100"></div>
                </div>
            </div>
            <!-- End Calendar Section -->

          </div>
        </div>

      </div>
    </div>
</section>

<!-- Optional: Row to display the edit request reason (if needed) -->
<div class="row mb-3" id="editRequestRow" style="display: none;">
    <div class="col-12 text-center">
      <small id="editRequestReasonDisplay" class="text-muted"></small>
    </div>
  </div>

<!-- Add Tour Plan Modal -->
<div class="modal fade" id="addTourPlanModal" tabindex="-1" aria-labelledby="addTourPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <form id="addTourPlanForm" novalidate>
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addTourPlanModalLabel">Add Tour Plan <small class="text-muted" id="selectedDate"></small></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
  
            <!-- Tour Plan Type Radio Buttons -->
            <div class="mb-3">
              <label class="form-label">Tour Plan Type <span style="color:red">*</span></label>
              <div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input tour-plan-type" type="radio" name="tour_plan_type" id="typeCollections" value="collections" checked>
                  <label class="form-check-label" for="typeCollections">Collections</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input tour-plan-type" type="radio" name="tour_plan_type" id="typeSourcing" value="sourcing" checked>
                  <label class="form-check-label" for="typeSourcing">Sourcing</label>
                </div>
                {{-- <div class="form-check form-check-inline">
                    <input class="form-check-input tour-plan-type" type="radio" name="tour_plan_type" id="typeBoth" value="both">
                    <label class="form-check-label" for="typeBoth">Both</label>
                  </div> --}}
              </div>
            </div>

             <!-- **Common** Collecting Agents Dropdown -->
             <div class="mb-3">
                {{-- @if(in_array(Auth::user()->role_id, [2,6]))
                    <label for="tourPlanCollectingAgent" class="form-label">
                        Collecting Agent <span style="color:red">*</span>
                    </label>
                    <select id="tourPlanCollectingAgent" class="form-select select2" name="collecting_agent_id" required>
                        <option value="">Choose Collecting Agent</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                    <div class="invalid-feedback">
                        Please select a Collecting Agent.
                    </div>
                @elseif(Auth::user()->role_id == 8)
                    <input type="hidden" id="tourPlanCollectingAgent" name="collecting_agent_id" value="{{ Auth::user()->id }}"> --}}
                {{-- @endif --}}

                <input type="hidden" id="tourPlanCollectingAgent" name="collecting_agent_id" value="{{ Auth::user()->id }}">
            </div>
  
            <!-- Collections Form Section -->
            <div id="collectionsFields">
              <!-- Blood Bank Dropdown -->
              <div class="mb-3">
                <label for="tourPlanBloodBank" class="form-label">Blood Bank <span style="color:red">*</span></label>
                <select id="tourPlanBloodBank" class="form-select select2" name="blood_bank_id" required>
                  <option value="">Choose Blood Bank</option>
                  <!-- Options will be populated via AJAX -->
                </select>
                <div class="invalid-feedback">
                  Please select a Blood Bank.
                </div>
              </div>
  
  
              <!-- Time Input -->
              <div class="mb-3">
                <label for="tourPlanTime" class="form-label">Time</label>
                <input type="time" class="form-control" id="tourPlanTime" name="time" required>
                <div class="invalid-feedback">
                  Please provide a valid time.
                </div>
              </div>
  
              <!-- Hidden Tour Plan Date -->
              <input type="hidden" id="tourPlanDate" name="date">
  
              <!-- Quantity -->
              <div class="mb-3">
                <label for="tourPlanQuantity" class="form-label">Quantity <span style="color:red">*</span></label>
                <input type="number" class="form-control" id="tourPlanQuantity" name="quantity" min="1" required>
                <div class="invalid-feedback">
                  Please enter a valid quantity (minimum 1).
                </div>
              </div>

              <!--Pending Documents Dropdown -->
              <div class="mb-3">
                <label for="tourPlanPendingDocuments" class="form-label">Any Pending Documents</label>
                <select id="tourPlanPendingDocuments" class="form-select select2" name="pending_documents_id[]" multiple>
                  <option value="">Choose Pending Documents</option>
                  <!-- Options will be populated via AJAX -->
                </select>
                <div class="invalid-feedback">
                  Please select a Pending Document.
                </div>
              </div>
  
             {{-- <!-- Collections Remarks -->
              <div class="mb-3">
                  <label for="tourPlanRemarks" class="form-label">Remarks</label>
                  <textarea class="form-control" id="tourPlanRemarks" name="collections_remarks" rows="2"></textarea>
                  <div class="invalid-feedback">
                  Please enter remarks.
                  </div>
              </div> --}}
            </div>
            <!-- End Collections Form Section -->
  
            <!-- Sourcing Form Section -->
            <div id="sourcingFields" style="display: none;">
              <!-- Blood Bank Name -->
              {{-- <div class="mb-3">
                <label for="sourcingBloodBankName" class="form-label">Blood Bank Name</label>
                <input type="text" class="form-control" id="sourcingBloodBankName" name="sourcing_blood_bank_name" placeholder="Enter Blood Bank Name">
                <div class="invalid-feedback">
                  Please enter the Blood Bank Name.
                </div>
              </div> --}}
  
              <!-- City Dropdown -->
              <div class="mb-3">
                <label for="sourcingCityDropdown" class="form-label">City <span style="color:red">*</span></label>
                <select id="sourcingCityDropdown" class="form-select select2" name="sourcing_city_id" required>
                  <option value="">Choose City</option>
                  <!-- Options will be populated via AJAX -->
                </select>
                <div class="invalid-feedback">
                  Please select a City.
                </div>
              </div>
  
              {{-- <!-- Sourcing Remarks -->
              <div class="mb-3">
                  <label for="sourcingRemarks" class="form-label">Remarks</label>
                  <textarea class="form-control" id="sourcingRemarks" name="sourcing_remarks" rows="2"></textarea>
                  <div class="invalid-feedback">
                  Please enter remarks.
                  </div>
              </div> --}}
            </div>
            <!-- End Sourcing Form Section -->

            <!-- Common Remarks -->
            <div class="mb-3">
            <label for="tourPlanRemarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="tourPlanRemarks" name="remarks" rows="2"></textarea>
                <div class="invalid-feedback">
                Please enter remarks.
                </div>
            </div>
  
          </div>
  
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Add Tour Plan</button>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- End Add Tour Plan Modal -->

<!-- View Tour Plan Details Modal -->
<div class="modal fade" id="viewTourPlanModal" tabindex="-1" aria-labelledby="viewTourPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title text-primary" id="viewTourPlanModalLabel">Tour Plan Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Tour Plan Details -->
          <div class="row">
            {{-- <div class="col-md-6 mb-3">
              <strong>Blood Bank:</strong>
              <p id="detailBloodBank"></p>
            </div> --}}
            <div class="col-md-6 mb-3">
              <strong>Collecting Agent:</strong>
              <p id="detailCollectingAgent"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Date:</strong>
              <p id="detailDate"></p>
            </div>
            {{-- <div class="col-md-6 mb-3">
              <strong>Quantity:</strong>
              <p id="detailQuantity"></p>
            </div> --}}
            <div class="col-md-6 mb-3">
              <strong>Status:</strong>
              <p id="detailStatus"></p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Remarks:</strong>
                <p id="detailRemarks"></p>
              </div>
            {{-- <div class="col-md-6 mb-3">
              <strong>Available Quantity:</strong>
              <p id="detailAvailableQuantity"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Remaining Quantity:</strong>
              <p id="detailRemainingQuantity"></p>
            </div> --}}
            {{-- <div class="col-md-6 mb-3">
              <strong>Latitude:</strong>
              <p id="detailLatitude">N/A</p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Longitude:</strong>
              <p id="detailLongitude">N/A</p>
            </div> --}}
            <div class="col-md-12 mb-3">
              <strong>Created By:</strong>
              <p id="detailCreatedBy"></p>
            </div>
            <div class="col-md-12 mb-3">
              <strong>Created At:</strong>
              <p id="detailCreatedAt"></p>
            </div>
            <div class="col-md-12 mb-3">
                <strong>Pending Documents:</strong>
                <ul id="detailPendingDocuments" class="list-unstyled">
                  <!-- Pending document names will be dynamically populated here -->
                </ul>
            </div>
          </div>

        <!-- New Divider and Request Collection Section -->
        <hr>
        <div class="row mt-3">
        <div class="col-12">
            <div class="form-check">
            <input class="form-check-input" type="checkbox" id="collectionSameCityCheckbox">
            <label class="form-check-label" for="collectionSameCityCheckbox">
                I would like to request collection for the same city.
            </label>
            </div>
        </div>
        <div class="col-12 mt-2">
            <label for="collectionRequestMessage" class="form-label">Message</label>
            <textarea class="form-control" id="collectionRequestMessage" rows="2" placeholder="Enter your message..."></textarea>
        </div>
        <div class="col-12 text-end mt-2">
            <button id="requestCollectionButton" class="btn btn-primary">Request for Collection</button>
        </div>
        </div>

        </div>
        <div class="modal-footer">

          <!-- Cancel Button -->
          <button type="button" class="btn btn-warning" id="cancelTourPlanButton">Cancel</button>

          <!-- DELETE Button -->
          <button type="button" class="btn btn-danger" id="deleteTourPlanButton">Delete</button>
         
          <!-- Optional: Add buttons for additional actions like Edit or Delete -->
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- End View Tour Plan Details Modal -->

@endsection

@push('styles')
    <style>
        /* Base Calendar Styles */
        #tourCalendar {
            max-width: 100%;
            margin: 0 auto;
        }
        .fc {
            max-width: 100%;
        }

        /* Override FullCalendar Text Colors to Black */
        #tourCalendar .fc {
            color: black;
        }

        #tourCalendar .fc-daygrid-day-number {
            color: black;
        }

        #tourCalendar .fc-event-title {
            color: black;
        }

        #tourCalendar .fc a {
            color: black;
        }

        #tourCalendar .fc-toolbar-title {
            color: black;
        }

        #tourCalendar .fc-col-header-cell-cushion {
            color: #012970 !important;
        }

        /* Optional: Event Background and Hover Colors */
        #tourCalendar .fc-event {
            background-color: #28a745; /* Bootstrap Success Color */
            border: none;
        }

        #tourCalendar .fc-event:hover {
            background-color: #218838; /* Darker Green on Hover */
        }

        #tourCalendar .fc-daygrid-day-frame {
            color: black;
        }

        /* Enhance Select2 Dropdowns to Match Bootstrap */
         /*   .select2-container--default .select2-selection--single {
            height: 38px; 
            padding: 6px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 10px;
        } */

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

        /* Target all disabled day grid cells */
        .fc-day-disabled.fc-daygrid-day {
            background-color: white !important; /* Set background to white */
        }

        /* Optional: Ensure the day number remains visible */
        .fc-day-disabled.fc-daygrid-day .fc-daygrid-day-number {
            color: black !important; /* Set text color to black */
        }

        /* Optional: Disable pointer events to prevent interaction */
        .fc-day-disabled.fc-daygrid-day {
            pointer-events: none;
        }

        /* Optional: Add a subtle hover effect */
         .fc-day-disabled.fc-daygrid-day:hover {
            background-color: #f8f9fa; /* Light gray on hover */
        } 

        .modal-dialog {
            max-height: 95vh;
            overflow-y: auto;
            scrollbar-width: thin; /* Options: auto, thin, none */
        }

        .select2-container--open {
            z-index: 9999 !important; /* Ensure it stays above other elements */
        }

        @media (max-width: 576px) {
            #tourCalendar {
                width: 100% !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

    <script>
        $(document).ready(function() {
            // Variable to store the currently selected Collecting Agent ID from filters
            var currentFilteredAgentId = null;
            var authRoleId = {{ Auth::user()->role_id }};
            var authUserId = {{ Auth::user()->id }};
            var selectedMonth = $('#monthPicker').val();
            var currentTPStatus = null;
            var currentEditRequest = null;


            // Function to populate Collecting Agents Dropdown
            function loadCollectingAgents() {
                $.ajax({
                    url: "{{ route('tourplanner.getCollectingAgents') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                            var agents = response.data;
                            var dropdown = $('#collectingAgentDropdown');
                            var modalDropdown = $('#tourPlanCollectingAgent');
                            dropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            modalDropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            $.each(agents, function(index, agent) {
                                var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                dropdown.append(option);
                                modalDropdown.append(option);
                            });
                            // Trigger Select2 to reinitialize with new options
                            // dropdown.trigger('change');
                            // modalDropdown.trigger('change');

                            // If the authenticated user's role is 6, auto-select his own collecting agent value
                            if(authRoleId === 6) {
                                dropdown.val(authUserId).trigger('change');
                                modalDropdown.val(authUserId).trigger('change');

                                // Also call loadCalendarEvents with this agent id and current month
                                var selectedMonth = $('#monthPicker').val();
                                loadCalendarEvents(authUserId, selectedMonth);
                            } else {
                                dropdown.trigger('change');
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


            // Function to populate Blood Banks Dropdown
            function loadBloodBanks(authUserId) {
                console.log("loadBloodBanks authUserId: ", authUserId);
                if (!authUserId) {
                    // Don't attempt to call the API if the employeeId is empty.
                    return;
                }
                console.log("loadBloodBanks employeeId: ", authUserId);
                
                $.ajax({
                    url: "{{ route('tourplanner.getEmployeesBloodBanks') }}",
                    type: 'GET',
                    data: { auth_user_id: authUserId }, // Pass authUserId here
                    success: function(response) {
                        console.log("loadBloodBanks response: ", response);
                        if(response.success) {
                            var bloodBanks = response.data;
                            var dropdown = $('#tourPlanBloodBank');
                            
                            // Clear existing options and add the default option
                            dropdown.empty().append('<option value="">Choose Blood Bank</option>');
                            
                            // Append each Blood Bank as an option
                            $.each(bloodBanks, function(index, bloodBank) {
                                var option = '<option value="' + bloodBank.id + '">' + bloodBank.name + '</option>';
                                dropdown.append(option);
                            });
                            
                            // Refresh Select2 to recognize new options
                            dropdown.trigger('change');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching blood banks:", error);
                        Swal.fire('Error', 'An error occurred while fetching blood banks.', 'error');
                    }
                });
            }

            // Load Blood Banks on page load
            loadBloodBanks(authUserId);


             // Function to populate Pending Documents Dropdown
             function loadPendingDocuments() {
                console.log("loadPendingDocuments");
                $.ajax({
                    url: "{{ route('tourplanner.getPendingDocuments') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                            console.log("loadPendingDocuments success");
                            console.log(response);
                            var pendingDocuments = response.data;
                            var dropdown = $('#tourPlanPendingDocuments');
                            dropdown.empty().append('<option value="">Choose Pending Documents</option>');
                            
                            $.each(response.data, function (index, pendingDocument) {
                                dropdown.append('<option value="' + pendingDocument.id + '">' + pendingDocument.name + '</option>');
                            });

                            // Reinitialize Select2
                            dropdown.select2({
                                theme: 'bootstrap-5',
                                width: '100%',
                                placeholder: 'Choose Pending Documents',
                                allowClear: true,
                                multiple: true, // Enable multiple selection
                                dropdownParent: $('#addTourPlanModal')
                            });
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching pending document:", error);
                        Swal.fire('Error', 'An error occurred while fetching pending document.', 'error');
                    }
                });
            }

            // Load Pending Documents on page load
            loadPendingDocuments();

            // Function to populate City Dropdown (Assuming you have an API endpoint for cities)
            function loadCities(agentId) {
                $.ajax({
                    url: "{{ route('tourplanner.getEmployeesCities') }}", // Ensure this route exists
                    data: { agent_id: agentId }, // Pass the agentId if your API uses it for filtering
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {
                            var cities = response.data;
                            var dropdown = $('#sourcingCityDropdown');
                            dropdown.empty().append('<option value="">Choose City</option>');
                            $.each(cities, function(index, city) {
                                var option = '<option value="' + city.id + '">' + city.name + '</option>';
                                dropdown.append(option);
                            });
                            // Trigger Select2 to reinitialize with new options
                            dropdown.trigger('change');
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching cities:", error);
                        Swal.fire('Error', 'An error occurred while fetching cities.', 'error');
                    }
                });
            }

              // Initialize FullCalendar
                var calendarEl = document.getElementById('tourCalendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    headerToolbar: {
                        left: 'prev',
                        center: 'title',
                        right: 'next',
                    },
                    height: 'auto',
                    aspectRatio: 1.35,
                    contentHeight: 'auto',
                    dayMaxEventRows: true,
                    selectable: true,
                    editable: false,
                    eventStartEditable: false,
                    eventDurationEditable: false,
                    events: [],

                // Handle date clicks
                dateClick: function(info) {
                    var clickedDate = info.date;
                    var clickedDateString = info.dateStr;
                    var currentDate = new Date();
                    currentDate.setHours(0,0,0,0); // Set to midnight to compare dates only
                    clickedDate.setHours(0,0,0,0);

                    if (clickedDate < currentDate) {
                        Swal.fire('Warning', 'You cannot add tour plan for previous dates.', 'warning');
                        return;
                    }

                    // Format the date as dd/mm/yyyy
                    var parts = clickedDateString.split('-'); // ['yyyy', 'mm', 'dd']
                    var formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];

                    console.log('currentTPStatus ************', currentTPStatus);
                    console.log('currentEditRequest ************', currentEditRequest);

                    // Check currentTPStatus and currentEditRequest before showing modal.
                    // If status is pending and edit_request is 1, show a message.
                    if ((currentTPStatus === 'pending' || currentTPStatus === 'submitted') && currentEditRequest === 1) {
                        Swal.fire('Info', "You have already submitted an edit request. Please wait till manager 'Enable' it.", 'info');
                        return;
                    }

                    if (currentTPStatus === 'submitted' && currentEditRequest === 0) {
                        Swal.fire('Info', "You have already submitted the Tour Plan.", 'info');
                        return;
                    }

                     // If tp_status is accepted, show a message and return.
                    // if (currentTPStatus === 'accepted') {
                    //     Swal.fire('Info', "TP already accepted.", "info");
                    //     return;
                    // }

                    // Open the modal and set the selected date
                    $('#tourPlanDate').val(clickedDateString);
                    $('#selectedDate').text('[Date: ' + formattedDate + ']'); // Set the formatted date
                    $('#tourPlanCollectingAgent').val(currentFilteredAgentId);
                    $('#addTourPlanModal').modal('show');
                },

                 // Handle event clicks to view tour plan details
                 eventClick: function(info) {
                    var eventObj = info.event;
                    var tourPlanId = eventObj.id; // Ensure 'id' corresponds to 'tour_plan_id'

                    var planType = eventObj.extendedProps.tour_plan_type;
                    var planTypeLabel = '';

                    if (planType === 1) {
                        planTypeLabel = 'Collections';
                    } else if (planType === 2) {
                        planTypeLabel = 'Sourcing';
                    } else if (planType === 3) {
                        planTypeLabel = 'Both: Collections & Sorcing';
                    }

                    // Update the modal title to include the plan type
                    $('#viewTourPlanModalLabel').text('Tour Plan Details (' + planTypeLabel + ')');


                    // Extract event details from extendedProps
                    var bloodBank = eventObj.extendedProps.blood_bank_name || 'N/A';
                    var collectingAgent = eventObj.extendedProps.collecting_agent_name || 'N/A';
                 //   var date = eventObj.start ? eventObj.start.toISOString().split('T')[0] : 'N/A';
                    var date = eventObj.start ? [
                                            eventObj.start.getFullYear(),
                                            String(eventObj.start.getMonth() + 1).padStart(2, '0'),
                                            String(eventObj.start.getDate()).padStart(2, '0')
                                            ].join('-')
                                        : 'N/A';
                    var quantity = eventObj.extendedProps.quantity || 'N/A';
                    var availableQuantity = eventObj.extendedProps.available_quantity || 'N/A';
                    var remainingQuantity = eventObj.extendedProps.remaining_quantity || 'N/A';
                    var status = eventObj.extendedProps.status || 'N/A';
                    var remarks = eventObj.extendedProps.remarks || 'N/A';
                    var latitude = eventObj.extendedProps.latitude || 'N/A';
                    var longitude = eventObj.extendedProps.longitude || 'N/A';
                    var createdBy = eventObj.extendedProps.created_by_name || 'N/A';
                    var createdAt = eventObj.extendedProps.created_at || 'N/A';

                     // Extract pending_document_names
                    var pendingDocuments = eventObj.extendedProps.pending_document_names || [];
                    console.log("Pending Documents:", pendingDocuments); // Debugging

                    // Populate the modal
                    var pendingDocumentsHtml = pendingDocuments.length 
                        ? pendingDocuments.map(doc => `<li>${doc}</li>`).join('')
                        : '<li>No pending documents.</li>';
                    $('#detailPendingDocuments').html(pendingDocumentsHtml);

                    // Populate the modal with event details
                    // $('#detailBloodBank').text(bloodBank);
                    $('#detailCollectingAgent').text(collectingAgent);
                    $('#detailDate').text(date);
                    $('#detailQuantity').text(quantity);
                    // $('#detailAvailableQuantity').text(availableQuantity);
                    // $('#detailRemainingQuantity').text(remainingQuantity);
                    $('#detailStatus').text(status.charAt(0).toUpperCase() + status.slice(1)); // Capitalize first letter
                    $('#detailRemarks').text(remarks);
                    // $('#detailLatitude').text(latitude);
                    // $('#detailLongitude').text(longitude);
                    $('#detailCreatedBy').text(createdBy);
                    $('#detailCreatedAt').text(createdAt.split(' ')[0]);

                    // Store the tourPlanId in the modal's data attribute
                    $('#viewTourPlanModal').data('tourPlanId', tourPlanId);

                    // Show the modal
                    $('#viewTourPlanModal').modal('show');
                },

                // After the calendar has rendered or dates have been set
                datesSet: function(info) {
                    // Get the first day of the current view
                    var currentStart = info.view.currentStart;
                    var year = currentStart.getFullYear();
                    var month = ('0' + (currentStart.getMonth() + 1)).slice(-2); // Months are zero-based
                    var selectedMonth = year + '-' + month;

                    // Update the monthPicker to reflect the current view
                    $('#monthPicker').val(selectedMonth);

                     // Use currentFilteredAgentId if available, otherwise fallback to authUserId
                    var agentId = currentFilteredAgentId ? currentFilteredAgentId : authUserId;
                     // Call both API functions to refresh calendar events and TP status
                    loadCalendarEvents(agentId, selectedMonth);
                    loadTPStatus(agentId, selectedMonth);

                   
                }
            });
            calendar.render();

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

                // Navigate the calendar to the selected month
                calendar.gotoDate(selectedMonth);
                // This will trigger datesSet, which in turn loads the events
            });

            // Function to load calendar events based on filters
            function loadCalendarEvents(agentId, selectedMonth) {
                // Define the API endpoint to fetch events based on filters
                var eventsApiUrl = "{{ route('tourplanner.getCalendarEvents') }}";

                // Only fetch events if agentId is present
                if (!agentId) {
                    calendar.removeAllEvents();
                    return;
                }

                $.ajax({
                    url: eventsApiUrl,
                    type: 'GET',
                    data: {
                        agent_id: agentId,
                        month: selectedMonth
                    },
                    beforeSend: function() {
                        // Optionally, show a loading indicator or disable certain UI elements
                        // For simplicity, not implementing here
                    },
                    success: function(response) {
                        if(response.success) {
                            console.log("API Response:", response); // Add this line
                            calendar.removeAllEvents();
                          //  calendar.addEventSource(response.events);

                          // Ensure response.events is an array before mapping
                            if (Array.isArray(response.events)) {
                                // Map each event to include color based on tour_plan_type
                                var events = response.events.map(function(event) {
                                    // Define default colors
                                    var eventColor = '#6c757d'; // Gray for undefined types

                                    if(event.extendedProps.tour_plan_type === 1) { // Collections
                                        eventColor = '#28a745'; // Green
                                    } else if(event.extendedProps.tour_plan_type === 2 && (event.extendedProps.status == 'cancel_requested' || event.extendedProps.status == 'cancel_approved')) { // Sourcing
                                        eventColor = '#FF0000'; // Blue
                                    }  else if(event.extendedProps.tour_plan_type === 2) { // Sourcing
                                        eventColor = '#007bff'; // Blue
                                    } else if(event.extendedProps.tour_plan_type === 3) { // Both
                                        eventColor = '#a569bd'; // Purple 
                                    }

                                    // Assign color properties
                                    return {
                                        id: event.id,
                                        title: event.title,
                                        start: event.start,
                                        allDay: event.allDay,
                                        backgroundColor: eventColor,
                                        borderColor: eventColor,
                                        extendedProps: {
                                            ...event.extendedProps,
                                            pending_document_names: event.pending_document_names || [] // Map pending_document_names
                                        }
                                    };
                                });

                                calendar.addEventSource(events);
                            } else {
                                console.error("Expected 'events' to be an array.");
                                Swal.fire('Error', 'Unexpected response format: events should be an array.', 'error');
                            }
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching calendar events:", error);
                        Swal.fire('Error', 'An error occurred while fetching calendar events.', 'error');
                    },
                    complete: function() {
                        // Optionally, hide the loading indicator or re-enable UI elements
                    }
                });
            }

            $('#addTourPlanModal').on('shown.bs.modal', function () {

                // Ensure the sourcing radio button is checked
                $('input[name="tour_plan_type"][value="sourcing"]').prop('checked', true);
                // Hide collections fields and show sourcing fields
                $('#collectionsFields').hide();
                $('#sourcingFields').show().find('input, select, textarea').prop('disabled', false);

                $('#sourcingCityDropdown').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose City',
                    allowClear: true,
                    dropdownParent: $('#addTourPlanModal')
                });

                  // Optionally trigger a change event to update any dependent fields
                $('.tour-plan-type:checked').trigger('change');

                console.log('addTourPlanModal open: ',authUserId);

                // Now call loadCities with the new agent id to load cities based on that agent
                loadCities(authUserId);
            });

            // Handle form submission for adding a tour plan
            $('#addTourPlanForm').on('submit', function(e) {
                e.preventDefault();
                console.log("addTourPlanForm submit");

                var form = $(this);
                if (form[0].checkValidity() === false) {
                    e.stopPropagation();
                    form.addClass('was-validated');
                    console.log("addTourPlanForm validate - validation failed");
                    return;
                }
                console.log("addTourPlanForm validate - validation passed");

                var tourPlanType = $('input[name="tour_plan_type"]:checked').val();

                var formData = {
                    tour_plan_type: tourPlanType,
                    blood_bank_id: $('#tourPlanBloodBank').val(),
                    date: $('#tourPlanDate').val(),
                    time: $('#tourPlanTime').val(),
                  //  collecting_agent_id: $('#tourPlanCollectingAgent').val(),
                    collecting_agent_id: authUserId,
                    quantity: $('#tourPlanQuantity').val(),
                 //   remarks: $('#tourPlanRemarks').val(),
                    _token: '{{ csrf_token() }}' // CSRF token
                };

               // Capture remarks based on tour plan type
                if (tourPlanType === 'collections') {
                    formData.remarks = $('#tourPlanRemarks').val();
                    var pendingDocuments = $('#tourPlanPendingDocuments').val(); // Array of selected IDs
                    formData.pending_documents_id = pendingDocuments; // Add to formData
                } else if (tourPlanType === 'sourcing') {
                    formData.remarks = $('#tourPlanRemarks').val();
                    formData.sourcing_blood_bank_name = $('#sourcingBloodBankName').val();
                    formData.sourcing_city_id = $('#sourcingCityDropdown').val();
                } else if (tourPlanType === 'both')  {
                    formData.remarks = $('#tourPlanRemarks').val();
                    var pendingDocuments = $('#tourPlanPendingDocuments').val(); // Array of selected IDs
                    formData.pending_documents_id = pendingDocuments; // Add to formData
                    formData.sourcing_blood_bank_name = $('#sourcingBloodBankName').val();
                    formData.sourcing_city_id = $('#sourcingCityDropdown').val();
                }

                // If Sourcing, validate additional fields
                if (tourPlanType === 'sourcing') {
                    // if (!formData.sourcing_blood_bank_name) {
                    //     Swal.fire('Warning', 'Please enter the Blood Bank Name for Sourcing.', 'warning');
                    //     return;
                    // }
                    if (!formData.sourcing_city_id) {
                        Swal.fire('Warning', 'Please select a city for Sourcing.', 'warning');
                        return;
                    }
                }

               console.log("formData: "+formData);
               console.log(formData);


                // // Send AJAX request to save the tour plan
                $.ajax({
                    url: "{{ route('tourplanner.saveTourPlan') }}",
                    type: 'POST',
                    data: formData,
                    beforeSend: function() {
                        // Show loading state on the submit button
                        $('#addTourPlanForm button[type="submit"]').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Submitting...');
                    },
                    success: function(response) {
                        if(response.success) {
                            // Close the modal
                            $('#addTourPlanModal').modal('hide');
                            // Refresh the calendar events
                            var selectedMonth = $('#monthPicker').val(); // Now synchronized with current view
                            loadCalendarEvents(formData.collecting_agent_id, selectedMonth);
                            // Show success message
                            Swal.fire('Success', 'Tour Plan added successfully.', 'success');
                            // Reset the form
                            $('#addTourPlanForm')[0].reset();
                           // $('.select2').val(null).trigger('change');
                            // $('#addTourPlanForm .select2').val(null).trigger('change');
                            form.removeClass('was-validated');
                            // Hide sourcing fields in case they were visible
                            $('#sourcingFields').hide();
                        } else {
                            if (response.errors) {
                                // Iterate through errors and add invalid class to respective fields
                                $.each(response.errors, function(field, messages) {
                                    var input = $('[name="' + field + '"]');
                                    input.addClass('is-invalid');
                                    // Update the invalid-feedback message
                                    input.next('.invalid-feedback').html(messages.join('<br>'));
                                });
                                Swal.fire('Validation Error', 'Please correct the highlighted fields.', 'error');
                            } else {
                                Swal.fire('Error', response.message, 'error');
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error saving tour plan:", error);
                        Swal.fire('Error', 'An error occurred while saving the tour plan.', 'error');
                    },
                    complete: function() {
                        // Re-enable the submit button and reset its text
                        $('#addTourPlanForm button[type="submit"]').prop('disabled', false).html('Add Tour Plan');
                    }
                });
            });


             // Bootstrap's custom validation
            (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
                })
            })()

    
              // 1. Handle the filter's Collecting Agent dropdown change
              $('#collectingAgentDropdown').on('change', function(){
                var selectedAgentId = $(this).val();
                currentFilteredAgentId = selectedAgentId ? selectedAgentId : null;
                
                // Update the modal's Collecting Agent dropdown
                $('#tourPlanCollectingAgent').val(selectedAgentId).trigger('change.select2');

                // Optional: If the modal is currently open, ensure it reflects the change
                if ($('#addTourPlanModal').hasClass('show')) {
                    $('#tourPlanCollectingAgent').trigger('change.select2');
                }

                var agentId = $('#collectingAgentDropdown').val();
                var selectedMonth = $('#monthPicker').val();

                // Store the selected agent ID
                currentFilteredAgentId = agentId ? agentId : null;

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                // Navigate the calendar to the selected month
                calendar.gotoDate(selectedMonth);
                // This will trigger datesSet, which in turn loads the events

                // Call loadBloodBanks with the new agent id so the blood bank dropdown updates
                 loadBloodBanks(agentId);
            });


            // Initialize Select2 for elements within the modal with dropdownParent set to the modal
            $('#addTourPlanModal').on('shown.bs.modal', function () {
                $('#tourPlanBloodBank').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose Blood Bank',
                    allowClear: true,
                    dropdownParent: $('#addTourPlanModal')
                });

                if ($('#tourPlanCollectingAgent').is('select')) {
                    $('#tourPlanCollectingAgent').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Choose Collecting Agent',
                        allowClear: true,
                        dropdownParent: $('#addTourPlanModal')
                    });
                }
                $('#sourcingCityDropdown').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose City',
                    allowClear: true,
                    dropdownParent: $('#addTourPlanModal')
                });

                $('#tourPlanPendingDocuments').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose Pending Documents',
                    allowClear: true,
                    multiple: true, // Enable multiple selection
                    dropdownParent: $('#addTourPlanModal')
                });

                // **IMPORTANT**: Trigger change to set the correct enabled/disabled fields based on default selection
                $('.tour-plan-type:checked').trigger('change');
            });

            // Optional: Destroy Select2 when modal is hidden to prevent duplicate initialization
            // Destroy Select2 and reset the form when the modal is hidden
            $('#addTourPlanModal').on('hidden.bs.modal', function () {
                // Destroy Select2 to prevent duplication
                $('#tourPlanBloodBank').select2('destroy');
                $('#tourPlanCollectingAgent').select2('destroy');
                $('#sourcingCityDropdown').select2('destroy');
                $('#tourPlanPendingDocuments').select2('destroy');

                // Reset the form fields
                $(this).find('form')[0].reset();

                // Remove validation classes
                $(this).find('form').removeClass('was-validated');

                // Optionally, remove any additional validation feedback
                $(this).find('.invalid-feedback').html('');

                // Hide sourcing fields if visible
                $('#sourcingFields').hide();
                // Show collections fields as default
                $('#collectionsFields').show();

                // **IMPORTANT**: Disable sourcing fields as default
                $('#sourcingFields').find('input, select, textarea').prop('disabled', true);
            });


            // **Handle DELETE Tour Plan Button Click**
            $('#deleteTourPlanButton').on('click', function() {
                var tourPlanId = $('#viewTourPlanModal').data('tourPlanId');

                if (!tourPlanId) {
                    Swal.fire('Error', 'Tour Plan ID is missing.', 'error');
                    return;
                }

                 // 1. Read the date text (YYYY-MM-DD) from the modal and turn it into a Date object
                var clickedDateString = $('#detailDate').text().trim();
                 console.log('clickedDateString:', clickedDateString);
                const dateStr = new Date(clickedDateString);
                var clickedDate = dateStr;
                var currentDate = new Date();
                currentDate.setHours(0,0,0,0); // Set to midnight to compare dates only
                clickedDate.setHours(0,0,0,0);

                console.log('currentDate:', currentDate);
                console.log('clickedDate:', clickedDate);

                // only block if planDate is strictly BEFORE today
                if (clickedDate < currentDate) {
                     Swal.fire('Warning', 'You cannot delete the Tour Plan for previous dates !!!', 'warning');
                    return;
                }

                console.log('delete currentTPStatus ************', currentTPStatus);
                console.log('delete currentEditRequest ************', currentEditRequest);


                //  If tp_status is accepted, show a message and return.
                if (currentTPStatus === 'accepted') {
                    Swal.fire('Info', "Cannot delete accepted Tour Plan.", "info");
                    return;
                }
                    
                        

                // Show confirmation dialog
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to delete this Tour Plan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX DELETE request
                        $.ajax({
                            url: "{{ route('tourplanner.deleteTourPlan') }}",
                            type: 'DELETE',
                            data: {
                                tour_plan_id: tourPlanId,
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Deleting...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading()
                                    }
                                });
                            },
                            success: function(response) {
                                if(response.success) {
                                    // Remove the event from the calendar
                                    var event = calendar.getEventById(tourPlanId);
                                    if (event) {
                                        event.remove();
                                    }
                                    // Close the modal
                                    $('#viewTourPlanModal').modal('hide');
                                    // Show success message
                                    Swal.fire('Deleted!', response.message, 'success');
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error deleting tour plan:", error);
                                Swal.fire('Error', 'An error occurred while deleting the tour plan.', 'error');
                            }
                        });
                    }
                });
            });


             // Handle Tour Plan Type Radio Buttons Change
             $('.tour-plan-type').on('change', function() {
                var selectedType = $('input[name="tour_plan_type"]:checked').val();
                console.log("Tour Plan Type Changed to:", selectedType); // Debugging

                if (selectedType === 'both') {
                    // Show both sourcing and collections sections without reinitializing
                    $('#sourcingFields').slideDown().find('input, select, textarea').prop('disabled', false);
                    $('#collectionsFields').slideDown().find('input, select, textarea').prop('disabled', false);

                    // Retrieve the agent ID from the collecting agent dropdown
                    var agentId = $('#tourPlanCollectingAgent').val();  
                    
                    // Clear sourcing fields as needed without reinitializing select2
                    $('#sourcingBloodBankName').val('');
                    $('#sourcingCityDropdown').val(null).trigger('change');

                    // Load cities if not already loaded
                    if ($('#sourcingCityDropdown').children().length <= 1) {
                        loadCities(authUserId);
                    }
                } else if (selectedType === 'sourcing') {
                    // Hide collections fields and show sourcing fields
                    $('#collectionsFields').slideUp().find('input, select, textarea').prop('disabled', true);
                    $('#sourcingFields').slideDown().find('input, select, textarea').prop('disabled', false);

                     // Retrieve the agent ID from the collecting agent dropdown
                     var agentId = $('#tourPlanCollectingAgent').val();  

                    if ($('#sourcingCityDropdown').children().length <= 1) {
                        loadCities(authUserId);
                    }
                } else {
                    // Only collections
                    $('#collectionsFields').slideDown().find('input, select, textarea').prop('disabled', false);
                    $('#sourcingFields').slideUp().find('input, select, textarea').prop('disabled', true);
                    // Optionally clear sourcing fields if needed
                    $('#sourcingBloodBankName').val('');
                    $('#sourcingCityDropdown').val(null).trigger('change');
                }

                // if (selectedType === 'sourcing') {
                //      // Hide Collections Fields
                //      $('#collectionsFields').slideUp();

                //     // Disable Collections Fields
                //     $('#collectionsFields').find('input, select, textarea').prop('disabled', true);

                //     // Show Sourcing Fields with temporary background color
                //     $('#sourcingFields').slideDown();

                //     // Enable Sourcing Fields
                //     $('#sourcingFields').find('input, select, textarea').prop('disabled', false);

                //     // Load cities if not already loaded
                //     if ($('#sourcingCityDropdown').children().length <= 1) { // Only default option exists
                //         loadCities();
                //     }
                // } else if (selectedType === 'collections') {
                //     // Show Collections Fields
                //     $('#collectionsFields').slideDown();

                //     // Enable Collections Fields
                //     $('#collectionsFields').find('input, select, textarea').prop('disabled', false);

                //     // Hide Sourcing Fields
                //     $('#sourcingFields').slideUp();

                //     // Disable Sourcing Fields
                //     $('#sourcingFields').find('input, select, textarea').prop('disabled', true);

                //     // Clear Sourcing Fields
                //     $('#sourcingBloodBankName').val('');
                //     $('#sourcingCityDropdown').val(null).trigger('change');
                // }
                // else if (selectedType === 'both') {
                //     // Show Sourcing Fields with temporary background color
                //     $('#sourcingFields').slideDown();

                //     // Enable Sourcing Fields
                //     $('#sourcingFields').find('input, select, textarea').prop('disabled', false);
                //      // Show Collections Fields
                //      $('#collectionsFields').slideDown();

                //     // Enable Collections Fields
                //     $('#collectionsFields').find('input, select, textarea').prop('disabled', false);

                //     // Clear Sourcing Fields
                //     $('#sourcingBloodBankName').val('');
                //     $('#sourcingCityDropdown').val(null).trigger('change');

                //     if (!$('#sourcingCityDropdown').hasClass("select2-hidden-accessible")) {
                //         $('#sourcingCityDropdown').select2({
                //             theme: 'bootstrap-5',
                //             width: '100%',
                //             placeholder: 'Choose City',
                //             allowClear: true,
                //             dropdownParent: $('#addTourPlanModal')
                //         });
                //     }
                //     // Load cities if not already loaded
                //     if ($('#sourcingCityDropdown').children().length <= 1) { // Only default option exists
                //         loadCities();
                //     }
                // }
            });


            // loadCalendarEvents(authUserId, selectedMonth);
            // loadTPStatus(authUserId, selectedMonth);


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
                            if(response.success) {
                                var status = response.data.tp_status;

                                var editReq = response.data.edit_request; // expected as 0 or 1
                                currentTPStatus = status.toLowerCase(); // normalize to lowercase
                                currentEditRequest = parseInt(editReq, 10);

                                var statusColor;
                                // Determine the color based on the status (case-insensitive)
                                switch (status.toLowerCase()) {
                                    case 'pending':
                                        statusColor = '#FFBF00';
                                        break;
                                    case 'submitted':
                                        statusColor = '#e74c3c';
                                        break;
                                    case 'accepted':
                                        statusColor = 'green';
                                        break;
                                    case 'rejected':
                                        statusColor = 'red';
                                        break;
                                    default:
                                        statusColor = 'gray';
                                }

                                // Update the TP Status display element
                                $("#tpStatusValue").text(status).css({
                                    'background-color': statusColor,
                                    'border': '2px solid ' + statusColor,
                                    'color': 'white',
                                    'padding': '4px 8px',
                                    'border-radius': '4px'
                                });

                                 // If the tour plan has been submitted, ask if the user wants to edit.
                                // if (status.toLowerCase() === 'submitted' || status.toLowerCase() === 'accepted') {
                                //     Swal.fire({
                                //         title: 'Already Submitted/Accepted',
                                //         text: "You have already submitted this month's tour plan. Do you want to edit?",
                                //         icon: 'info',
                                //         showCancelButton: true,
                                //         confirmButtonText: 'Yes, Edit',
                                //         cancelButtonText: 'No'
                                //     }).then((result) => {
                                //         if(result.isConfirmed) {
                                //             // Prompt user for the edit request reason
                                //             Swal.fire({
                                //                 title: 'Edit Request',
                                //                 input: 'textarea',
                                //                 inputPlaceholder: 'Enter edit request reason...',
                                //                 showCancelButton: true,
                                //                 inputValidator: (value) => {
                                //                     if (!value) {
                                //                         return 'Please enter a reason for editing.';
                                //                     }
                                //                 }
                                //             }).then((result) => {
                                //                 if (result.isConfirmed) {
                                //                     var reason = result.value;
                                //                     // Call the API function to submit the edit request
                                //                     submitEditRequest(agentId, selectedMonth, reason);
                                //                 }
                                //             });
                                //         }
                                //     });
                                // }
                            } else {
                               // Swal.fire('Error', response.message, 'error');
                               // Update the TP Status display element
                                $("#tpStatusValue").text('Pending').css({
                                    'background-color': 'gray',
                                    'border': '2px solid gray',
                                    'color': 'white',
                                    'padding': '4px 8px',
                                    'border-radius': '4px'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error("Error fetching TPStatus:", error);
                            Swal.fire('Error', 'An error occurred while fetching TPStatus.', 'error');
                        }
                    });
            }


            $('#submitTourPlanButton').on('click', function() {
                // Use currentFilteredAgentId if available, otherwise fallback to authUserId
                var agentId = currentFilteredAgentId ? currentFilteredAgentId : authUserId;
                var selectedMonth = $('#monthPicker').val();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "Are you sure you want to submit this month's Tour Plan?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, submit it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('tourplanner.submitMonthlyTourPlan') }}", // Update with your actual route name
                            type: 'POST',
                            data: {
                                agent_id: agentId,
                                month: selectedMonth,
                                status: 'submitted',
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Submitting...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                console.log("response: ", response);
                                if(response.success) {
                                    let msg = response.message;
                                    if(response.data.updated_rows == 0) {
                                        msg += " (No records were updated.)";
                                    }
                                    Swal.fire('Submitted!', msg, 'success');
                                    loadTPStatus(agentId, selectedMonth);
                                    loadCalendarEvents(agentId, selectedMonth);
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }   
                            },
                            error: function(xhr, status, error) {
                                console.error("Error submitting monthly tour plan:", error);
                                Swal.fire('Error', 'An error occurred while submitting the monthly tour plan.', 'error');
                            }
                        });
                    }
                });
            });


            function submitEditRequest(agentId, selectedMonth, reason) {
                $.ajax({
                    url: "{{ route('tourplanner.submitEditRequest') }}", // New route, see below
                    type: 'POST',
                    data: {
                        agent_id: agentId,
                        month: selectedMonth,
                        edit_request: 1,
                        edit_request_reason: reason,
                        _token: '{{ csrf_token() }}'
                    },
                    beforeSend: function(){
                        Swal.fire({
                            title: 'Submitting edit request...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        console.log("Edit Request Response: ", response);
                        if(response.success) {
                            Swal.fire('Success', 'Edit request submitted successfully.', 'success');
                            // Refresh the TP status after editing
                            loadTPStatus(agentId, selectedMonth);
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error submitting edit request:", error);
                        Swal.fire('Error', 'An error occurred while submitting the edit request.', 'error');
                    }
                });
            }


            $('#requestCollectionButton').on('click', function() {
                // Get the tour_plan_id stored in the modal's data attribute
                var tourPlanId = $('#viewTourPlanModal').data('tourPlanId');
                // Get the checkbox value: 1 if checked, otherwise 0
                var requestForSameCity = $('#collectionSameCityCheckbox').is(':checked') ? 1 : 0;
                // Retrieve the message from the textarea
                var message = $('#collectionRequestMessage').val();

                console.log('requestCollectionButton clcik');
                console.log('requestCollectionButton tourPlanId: ', tourPlanId);
                console.log('requestCollectionButton requestForSameCity: ', requestForSameCity);
                console.log('requestCollectionButton message: ', message);

                // Ask for confirmation via SweetAlert
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to request collection?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, request it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If confirmed, send AJAX POST request to the controller function
                        $.ajax({
                            url: "{{ route('tourplanner.requestCollection') }}", // Ensure this route exists
                            type: 'POST',
                            data: {
                                tour_plan_id: tourPlanId,
                                request_same_city: requestForSameCity,
                                message: message,
                                _token: '{{ csrf_token() }}'
                            },
                            beforeSend: function() {
                                Swal.fire({
                                    title: 'Requesting...',
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
                            },
                            success: function(response) {
                                if(response.success) {
                                    Swal.fire('Success', response.message, 'success');
                                } else {
                                    Swal.fire('Error', response.message, 'error');
                                }
                            },
                            error: function(xhr, status, error) {
                                console.error("Error requesting collection:", error);
                                Swal.fire('Error', 'An error occurred while processing the request.', 'error');
                            }
                        });
                    }
                });
            });


             // **Handle CANCEL Tour Plan Button Click**
            $('#cancelTourPlanButton').on('click', function() {
                var tourPlanId = $('#viewTourPlanModal').data('tourPlanId');

                if (!tourPlanId) {
                    Swal.fire('Error', 'Tour Plan ID is missing.', 'error');
                    return;
                }

                //  If tp_status is accepted, show a message and return.
                if (currentTPStatus === 'accepted' || currentTPStatus === 'rejected') {
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Are you sure you want to cancel this Tour Plan?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, cancel it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send AJAX DELETE request
                            $.ajax({
                                url: "{{ route('tourplanner.cancelTourPlan') }}",
                                type: 'DELETE',
                                data: {
                                    tour_plan_id: tourPlanId,
                                    _token: '{{ csrf_token() }}'
                                },
                                beforeSend: function() {
                                    Swal.fire({
                                        title: 'Request Cancel...',
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading()
                                        }
                                    });
                                },
                                success: function(response) {
                                    if(response.success) {
                                        
                                        // Close the modal
                                        $('#viewTourPlanModal').modal('hide');
                                        // Show success message
                                        Swal.fire('Cancel Requested!', response.message, 'success');
                                    } else {
                                        Swal.fire('Error', response.message, 'error');
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error cancelling the tour plan:", error);
                                    Swal.fire('Error', 'An error occurred while cancelling the tour plan.', 'error');
                                }
                            });
                        }
                    });
                }
                else {
                    Swal.fire('Info', "Cannot cancel the Pending/Submitted Tour Plan.", "info");
                    return;
                }
               
            });


        });
    </script>
    
@endpush
