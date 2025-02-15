@extends('include.dashboardLayout')

@section('title', 'Report Visits')

@section('content')

<div class="pagetitle">
    <h1>Report Visits</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('visits.index') }}">Visits</a></li>
        <li class="breadcrumb-item active">Update Visit</li>
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

<!-- Aggregated Tour Plan Details Modal -->
<div class="modal fade" id="viewAggregatedTourPlanModal" tabindex="-1" aria-labelledby="viewAggregatedTourPlanModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="viewAggregatedTourPlanModalLabel">Tour Plan Details</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Details will be injected here by JavaScript -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="viewButton">View</button>
        {{-- <button type="button" class="btn btn-info d-none" id="updateButton">Update</button> <!-- Added 'd-none' --> --}}
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

        .fc-event {
            border: none; /* Remove border to match custom background colors */
            color: white; /* Ensure text is readable on colored backgrounds */
            cursor: pointer; /* Indicate that the event is clickable */
            white-space: normal; /* Allow text to wrap onto multiple lines */
            text-align: center; /* Center-align text */
            padding: 2px; /* Optional: Add some padding for better aesthetics */
        }
        .fc-event:hover {
            opacity: 0.8; /* Slight hover effect */
        }
    </style>
@endpush

@push('scripts')
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
   
    <script>

       // Date comparison function
       function isSameDay(d1, d2) {
            return d1.getFullYear() === d2.getFullYear() &&
                   d1.getMonth() === d2.getMonth() &&
                   d1.getDate() === d2.getDate();
        }

        $(document).ready(function() {
            // Variable to store the currently selected Collecting Agent ID from filters
            var currentFilteredAgentId = null;
            var today = new Date();
            today.setHours(0,0,0,0); // Normalize today's date

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
                selectable: true, // Allow date selection
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

                
                    // Format the date as dd/mm/yyyy
                    var parts = clickedDateString.split('-'); // ['yyyy', 'mm', 'dd']
                    var formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];

                    // Open the modal and set the selected date
                    $('#tourPlanDate').val(clickedDateString);
                    $('#selectedDate').text('[Date: ' + formattedDate + ']'); // Set the formatted date
                    $('#tourPlanCollectingAgent').val(currentFilteredAgentId);
                    $('#addTourPlanModal').modal('show');
                },

                // Handle aggregated event clicks to view details
                eventClick: function(info) {
                    var eventObj = info.event;
                    
                    // Extract aggregated data from extendedProps
                    var collections = eventObj.extendedProps.collections;
                    var sourcing = eventObj.extendedProps.sourcing;
                    var both = eventObj.extendedProps.both;
                    var dateStr = eventObj.extendedProps.date; // Expected format: 'YYYY-MM-DD'

                    // Prepare the content for the modal
                    var detailsHtml = `
                        <p><strong>Date:</strong> ${dateStr}</p>
                        <p><strong>Collections:</strong> ${collections}</p>
                    `;

                     // If you only want to show “Both:” when it’s > 0, wrap it in an if statement:
                     if (sourcing && sourcing > 0) {
                        detailsHtml += `<p><strong>Sourcing:</strong> ${sourcing}</p>`;
                    }

                    // If you only want to show “Both:” when it’s > 0, wrap it in an if statement:
                    if (both && both > 0) {
                        detailsHtml += `<p><strong>Both:</strong> ${both}</p>`;
                    }

                    // Inject details into the modal body
                    $('#viewAggregatedTourPlanModal .modal-body').html(detailsHtml);

                    // Parse the event date and today's date
                    var modalDate = new Date(dateStr);
                    var today = new Date();
                    today.setHours(0,0,0,0); // Normalize today's date

                    modalDate.setHours(0,0,0,0); // Normalize modal date

                    console.log("modalDate:", modalDate);
                    console.log("today:", today);

                    // Compare the dates
                    // Inside your eventClick handler
                    if(isSameDay(modalDate, today)) {
                    //    $('#updateButton').removeClass('d-none').attr('data-date', dateStr); // Show and set date
                        $('#viewButton').attr('data-date', dateStr); // Set date for view button as well, if needed
                    } else {
                    //    $('#updateButton').addClass('d-none').removeAttr('data-date'); // Hide and remove date
                        $('#viewButton').attr('data-date', dateStr); // Set date for view button
                    }
                    // Show the modal
                    $('#viewAggregatedTourPlanModal').modal('show');
                },


                // Customize event rendering to allow multi-line titles
                eventContent: function(info) {
                    // Create a container for the event content
                    var container = document.createElement('div');
                    container.innerHTML = info.event.title; // Contains <br> for line breaks
                    return { domNodes: [container] };
                },

                // Tooltip for aggregated events
                // eventDidMount: function(info) {
                //     var tooltipContent = `
                //         <strong>Collections:</strong> ${info.event.extendedProps.collections}<br>
                //         <strong>Sourcing:</strong> ${info.event.extendedProps.sourcing}
                //     `;
                //     $(info.el).tooltip({
                //         title: tooltipContent,
                //         html: true,
                //         placement: 'top',
                //         trigger: 'hover'
                //     });
                // },

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


            // By default load the page, load current month TP

              // 1. Embed the authenticated user's ID into JavaScript
              var agentId = @json(Auth::check() ? Auth::user()->id : null);
                var selectedMonth = $('#monthPicker').val();

                console.log('agentId: '+agentId);
                console.log('selectedMonth: '+selectedMonth);

                // Store the selected agent ID
                currentFilteredAgentId = agentId ? agentId : null;

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                // Navigate the calendar to the selected month
                calendar.gotoDate(selectedMonth);

            // Handle Filter Button Click
            $('#filterButton').on('click', function() {
               // var agentId = $('#collectingAgentDropdown').val();
                var agentId = @json(Auth::check() ? Auth::user()->id : null);
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
                            console.log("API Response:", response); // Debugging
                            calendar.removeAllEvents();

                            if (Array.isArray(response.events)) {
                                // Step 1: Aggregate events by date and type
                                var aggregated = {};

                                response.events.forEach(function(event) {
                                    var date = event.start; // e.g., '2025-01-14'
                                    var type = event.extendedProps.tour_plan_type; // 1 for Collections, 2 for Sourcing, 3 for both

                                    if (!aggregated[date]) {
                                        aggregated[date] = { collections: 0, sourcing: 0,  both: 0 };
                                    }

                                    if(type === 1) { // Collections
                                        aggregated[date].collections += 1;
                                    } else if(type === 2) { // Sourcing
                                        aggregated[date].sourcing += 1;
                                    } else if (type === 3) {  //  FOR type === 3 (Both)
                                         aggregated[date].both += 1;
                                    }
                                });

                                // Step 2: Create aggregated event objects
                                var aggregatedEvents = [];

                                for(var date in aggregated) {
                                    var counts = aggregated[date];
                                    var titleParts = [];

                                    if(counts.collections > 0) {
                                        titleParts.push('Collections: ' + counts.collections);
                                    }
                                    if(counts.sourcing > 0) {
                                        titleParts.push('Sourcing: ' + counts.sourcing);
                                    }
                                    if (counts.both > 0) {
                                        // ADDED FOR type === 3 (Both)
                                        titleParts.push('Both: ' + counts.both);
                                    }

                                    var title = titleParts.join('<br>'); // Use <br> for line breaks

                                    // Determine background color based on event types
                                    var backgroundColor = '#6c757d'; // Default Gray
                                    if (counts.both > 0) {
                                        // If there's "Both," highlight in, say, Yellow (#ffc107)
                                        backgroundColor = '#bb8fce'; 
                                    } else if(counts.collections > 0 && counts.sourcing > 0) {
                                        backgroundColor = '#17a2b8'; // Blue for both
                                    } else if(counts.collections > 0) {
                                        backgroundColor = '#28a745'; // Green for Collections
                                    } else if(counts.sourcing > 0) {
                                        backgroundColor = '#007bff'; // Blue for Sourcing
                                    }

                                    aggregatedEvents.push({
                                        title: title,
                                        start: date,
                                        allDay: true,
                                        backgroundColor: backgroundColor,
                                        borderColor: backgroundColor,
                                        extendedProps: {
                                            collections: counts.collections,
                                            sourcing: counts.sourcing,
                                            both: counts.both,
                                            date: date
                                        }
                                    });
                                }

                                // Step 3: Add aggregated events to the calendar
                                calendar.addEventSource(aggregatedEvents);
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


            // Additional JavaScript for "View" and "Update" buttons
            $(document).on('click', '#viewButton', function() {
               // Get the date from data-date attribute
               var date = $(this).data('date');
                if(date) {
                    // Redirect to the update page with the date as a parameter
                    var viewUrl = "{{ route('visits.view', ['date' => 'PLACEHOLDER']) }}".replace('PLACEHOLDER', date);
                    window.location.href = viewUrl;
                } else {
                    Swal.fire('Error', 'No date specified for update.', 'error');
                }
            });

          
        });
    </script>
    
@endpush

