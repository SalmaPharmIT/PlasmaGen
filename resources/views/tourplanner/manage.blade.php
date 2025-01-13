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
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Available Quantity:</strong> <span id="detailAvailableQuantity">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Remaining Quantity:</strong> <span id="detailRemainingQuantity">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Latitude:</strong> <span id="detailLatitude">N/A</span>
            </div>
            <div class="mb-3 collection-fields" style="display: none;">
              <strong>Longitude:</strong> <span id="detailLongitude">N/A</span>
            </div>
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
            <div class="mb-3">
              <strong>Status:</strong> <span id="detailStatus">N/A</span>
            </div>
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
    </style>
@endpush

@push('scripts')
    <!-- jQuery (Required for Select2 and your custom scripts) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+xOHOol+R4m4kT+PgCDjK6F/zuHgI3V4UGRj5ho=" crossorigin="anonymous"></script>
    
    <!-- Bootstrap Bundle JS (Includes Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 JS (Ensure you have included Select2 CSS in your layout) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
                            dropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            if (modalDropdown.length) {
                                modalDropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            }
                            $.each(agents, function(index, agent) {
                                var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
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
                    var tourPlanType = event.extendedProps.tour_plan_type === 1 ? 'Collections' : (event.extendedProps.tour_plan_type === 2 ? 'Sourcing' : 'N/A');

                    // Conditionally include 'Time' only for Collections
                    var timeHtml = (tourPlanType === 'Collections') ? `<small>Time: ${eventTime}</small>` : '';


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
                $('#detailAvailableQuantity').text(eventAvailableQuantity || 'N/A');
                $('#detailRemainingQuantity').text(eventRemainingQuantity || 'N/A');
                $('#detailStatus').text(eventStatus);
                $('#detailRemarks').text(eventRemarks);
                $('#detailLatitude').text(eventLatitude || 'N/A');
                $('#detailLongitude').text(eventLongitude || 'N/A');
                $('#detailCity').text(eventCity);
                $('#detailCreatedBy').text(eventCreatedBy);
                $('#detailCreatedAt').text(eventCreatedAt);
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
                } else {
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

        });
    </script>
@endpush
