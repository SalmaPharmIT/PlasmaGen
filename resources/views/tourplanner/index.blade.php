@extends('include.dashboardLayout')

@section('title', 'Create Tour Planner')

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
            <div class="row mb-4 mt-2 align-items-end">
                <!-- Collecting Agent Dropdown -->
                <div class="col-md-4">
                    <label for="collectingAgentDropdown" class="form-label">Collecting Agent</label>
                    <select id="collectingAgentDropdown" class="form-select select2">
                        <option value="">Choose Collecting Agent</option>
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

<!-- Add Tour Plan Modal -->
<div class="modal fade" id="addTourPlanModal" tabindex="-1" aria-labelledby="addTourPlanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="addTourPlanForm">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Add Tour Plan</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Blood Bank Dropdown -->
          <div class="mb-3">
                <label for="tourPlanBloodBank" class="form-label">Blood Bank</label>
                <select id="tourPlanBloodBank" class="form-select select2" name="blood_bank_id" required>
                    <option value="">Choose Blood Bank</option>
                    <!-- Options will be populated via AJAX -->
                </select>
          </div>
         <!-- Collecting Agents Dropdown -->
         <div class="mb-3">
            <label for="tourPlanCollectingAgent" class="form-label">Collecting Agent</label>
            <select id="tourPlanCollectingAgent" class="form-select select2" name="collecting_agent_id" required>
                <option value="">Choose Collecting Agent</option>
                <!-- Options will be populated via AJAX -->
            </select>
          </div>
          <!-- Tour Plan Date (readonly) -->
          <div class="mb-3">
            <label for="tourPlanDate" class="form-label">Date</label>
            <input type="date" class="form-control" id="tourPlanDate" name="date" readonly>
          </div>
        
          <!-- Quantity -->
          <div class="mb-3">
            <label for="tourPlanQuantity" class="form-label">Quantity</label>
            <input type="number" class="form-control" id="tourPlanQuantity" name="quantity" min="1" required>
          </div>

            <!-- Remarks -->
            <div class="col-md-12">
                <label for="tourPlanRemarks" class="form-label">Remarks</label>
                <textarea class="form-control" id="tourPlanRemarks" name="remarks" rows="2"></textarea>
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
            <div class="col-md-6 mb-3">
              <strong>Blood Bank:</strong>
              <p id="detailBloodBank"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Collecting Agent:</strong>
              <p id="detailCollectingAgent"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Date:</strong>
              <p id="detailDate"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Quantity:</strong>
              <p id="detailQuantity"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Status:</strong>
              <p id="detailStatus"></p>
            </div>
            <div class="col-md-6 mb-3">
                <strong>Remarks:</strong>
                <p id="detailRemarks"></p>
              </div>
            <div class="col-md-6 mb-3">
              <strong>Available Quantity:</strong>
              <p id="detailAvailableQuantity"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Remaining Quantity:</strong>
              <p id="detailRemainingQuantity"></p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Latitude:</strong>
              <p id="detailLatitude">N/A</p>
            </div>
            <div class="col-md-6 mb-3">
              <strong>Longitude:</strong>
              <p id="detailLongitude">N/A</p>
            </div>
            <div class="col-md-12 mb-3">
              <strong>Created By:</strong>
              <p id="detailCreatedBy"></p>
            </div>
            <div class="col-md-12 mb-3">
              <strong>Created At:</strong>
              <p id="detailCreatedAt"></p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
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

        /* Disable "Prev" Button Initially (Will be Removed) */
        /* .fc-prev-button {
            pointer-events: none;
            opacity: 0.5;
        }

        /* Optional: Change cursor to not-allowed for disabled buttons */
        .fc-prev-button[disabled],
        .fc-prev-button.disabled {
            cursor: not-allowed;
        } */

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

        /* === New CSS to Limit Calendar to 5 Rows === */
        /* Note: Use with caution as some months naturally require 6 rows */
        /* Uncomment the following CSS if you still want to limit to 5 rows */

        /*
        #tourCalendar .fc-daygrid-body table tbody tr:nth-child(6) {
            display: none !important;
        }

        #tourCalendar .fc-daygrid-body {
            height: auto !important;
        }

        #tourCalendar .fc-daygrid-body table tbody tr {
            height: 100px;  
        }

        #tourCalendar .fc-daygrid-body table tbody {
            overflow: hidden;
        }
        */
    </style>
@endpush

@push('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
   
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
                            var modalDropdown = $('#tourPlanCollectingAgent');
                            dropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            modalDropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            $.each(agents, function(index, agent) {
                                var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                dropdown.append(option);
                                modalDropdown.append(option);
                            });
                            // Trigger Select2 to reinitialize with new options
                            dropdown.trigger('change');
                            modalDropdown.trigger('change');
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
            function loadBloodBanks() {
                $.ajax({
                    url: "{{ route('tourplanner.getBloodBanks') }}",
                    type: 'GET',
                    success: function(response) {
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
            loadBloodBanks();

            // Initialize FullCalendar
            var calendarEl = document.getElementById('tourCalendar');
            var today = new Date();
            var firstDayOfCurrentMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            var todayDateString = today.toISOString().split('T')[0];

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev',
                    center: 'title',
                    right: 'next',
                },
                height: 'auto', // Adjust height to fit content
                aspectRatio: 1.35, // Adjust aspect ratio for better mobile view
                contentHeight: 'auto', // Allow FullCalendar to resize properly
                dayMaxEventRows: true, // Allow event stacking
                // Removed validRange to allow navigation to any month
                // validRange: {
                //     start: firstDayOfCurrentMonth.toISOString().split('T')[0]
                // },
                selectable: true, // Allow date selection
                // Removed selectConstraint since validRange is removed
                // selectConstraint: {
                //     start: firstDayOfCurrentMonth.toISOString().split('T')[0]
                // },
                editable: false, // Disable event dragging and resizing
                eventStartEditable: false,
                eventDurationEditable: false,
                events: [], // Events will be loaded based on filters

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

                    // Open the modal and set the selected date
                    $('#tourPlanDate').val(clickedDateString);
                    $('#tourPlanCollectingAgent').val(currentFilteredAgentId);
                    $('#addTourPlanModal').modal('show');
                },

                 // Handle event clicks to view tour plan details
                 eventClick: function(info) {
                    var eventObj = info.event;
                    var tourPlanId = eventObj.id; // Ensure 'id' corresponds to 'tour_plan_id'

                    // Extract event details from extendedProps
                    var bloodBank = eventObj.extendedProps.blood_bank_name || 'N/A';
                    var collectingAgent = eventObj.extendedProps.collecting_agent_name || 'N/A';
                    var date = eventObj.start ? eventObj.start.toISOString().split('T')[0] : 'N/A';
                    var quantity = eventObj.extendedProps.quantity || 'N/A';
                    var availableQuantity = eventObj.extendedProps.available_quantity || 'N/A';
                    var remainingQuantity = eventObj.extendedProps.remaining_quantity || 'N/A';
                    var status = eventObj.extendedProps.status || 'N/A';
                    var remarks = eventObj.extendedProps.remarks || 'N/A';
                    var latitude = eventObj.extendedProps.latitude || 'N/A';
                    var longitude = eventObj.extendedProps.longitude || 'N/A';
                    var createdBy = eventObj.extendedProps.created_by_name || 'N/A';
                    var createdAt = eventObj.extendedProps.created_at || 'N/A';

                    // Populate the modal with event details
                    $('#detailBloodBank').text(bloodBank);
                    $('#detailCollectingAgent').text(collectingAgent);
                    $('#detailDate').text(date);
                    $('#detailQuantity').text(quantity);
                    $('#detailAvailableQuantity').text(availableQuantity);
                    $('#detailRemainingQuantity').text(remainingQuantity);
                    $('#detailStatus').text(status.charAt(0).toUpperCase() + status.slice(1)); // Capitalize first letter
                    $('#detailRemarks').text(remarks);
                    $('#detailLatitude').text(latitude);
                    $('#detailLongitude').text(longitude);
                    $('#detailCreatedBy').text(createdBy);
                    $('#detailCreatedAt').text(createdAt);

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

                    // Load events for the current visible month and selected agent
                    if (currentFilteredAgentId) {
                        loadCalendarEvents(currentFilteredAgentId, selectedMonth);
                    } else {
                        // If no agent is selected, remove all events
                        calendar.removeAllEvents();
                    }
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
                            calendar.removeAllEvents();
                            calendar.addEventSource(response.events);
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

            // Handle form submission for adding a tour plan
            $('#addTourPlanForm').on('submit', function(e) {
                e.preventDefault();

                var form = $(this);
                if (form[0].checkValidity() === false) {
                    e.stopPropagation();
                    form.addClass('was-validated');
                    return;
                }

                var formData = {
                    blood_bank_id: $('#tourPlanBloodBank').val(),
                    date: $('#tourPlanDate').val(),
                    collecting_agent_id: $('#tourPlanCollectingAgent').val(),
                    quantity: $('#tourPlanQuantity').val(),
                    remarks: $('#tourPlanRemarks').val(), // Include remarks
                    latitude: $('#tourPlanLatitude').val(), // Ensure these fields exist or remove if not needed
                    longitude: $('#tourPlanLongitude').val(),
                    _token: '{{ csrf_token() }}' // CSRF token
                };

                // Validate that Blood Bank is selected
                if (!formData.blood_bank_id) {
                    Swal.fire('Warning', 'Please select a blood bank.', 'warning');
                    return;
                }

                // Validate that Collecting Agent is selected
                if (!formData.collecting_agent_id) {
                    Swal.fire('Warning', 'Please select a collecting agent.', 'warning');
                    return;
                }

                // Validate that quantity is a positive number
                if (formData.quantity < 1) {
                    Swal.fire('Warning', 'Quantity must be at least 1.', 'warning');
                    return;
                }

                // Send AJAX request to save the tour plan
                $.ajax({
                    url: "{{ route('tourplanner.saveTourPlan') }}", // Ensure this route is defined
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
                            $('.select2').val(null).trigger('change');
                            form.removeClass('was-validated');
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

            // Initialize Select2 for elements within the modal with dropdownParent set to the modal
            $('#addTourPlanModal').on('shown.bs.modal', function () {
                $('#tourPlanBloodBank').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose Blood Bank',
                    allowClear: true,
                    dropdownParent: $('#addTourPlanModal')
                });

                $('#tourPlanCollectingAgent').select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    placeholder: 'Choose Collecting Agent',
                    allowClear: true,
                    dropdownParent: $('#addTourPlanModal')
                });
            });

            // Optional: Destroy Select2 when modal is hidden to prevent duplicate initialization
            // Destroy Select2 and reset the form when the modal is hidden
            $('#addTourPlanModal').on('hidden.bs.modal', function () {
                // Destroy Select2 to prevent duplication
                $('#tourPlanBloodBank').select2('destroy');
                $('#tourPlanCollectingAgent').select2('destroy');

                // Reset the form fields
                $(this).find('form')[0].reset();

                // Remove validation classes
                $(this).find('form').removeClass('was-validated');

                // Optionally, remove any additional validation feedback
                $(this).find('.invalid-feedback').html('');
            });


             // **New: Handle DELETE Tour Plan Button Click**
             $('#deleteTourPlanButton').on('click', function() {
                var tourPlanId = $('#viewTourPlanModal').data('tourPlanId');

                if (!tourPlanId) {
                    Swal.fire('Error', 'Tour Plan ID is missing.', 'error');
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

        });
    </script>
    
@endpush
