@extends('include.dashboardLayout')

@section('title', 'DCR Approvals')

@section('content')

<div class="pagetitle">
    <h1>DCR Approvals</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('tourplanner.manage') }}">Manage Tour Plan</a></li>
        <li class="breadcrumb-item active">DCR Approvals</li>
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
                        <i class="bi bi-filter me-1"></i> Filter
                   </button>
                </div>

                <!-- Reset Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button id="resetButton" class="btn btn-secondary w-100">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                    </button>
                </div>
            </div>
            <!-- End Filters Row -->

            <!-- Table Row -->
            <div class="row">
                <div class="col-12">
                    <table id="dcrApprovalsTable" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Tour Plan Type</th>
                                <th>Visit Date</th>
                                <th>Time</th>
                                <th>TP Status</th>
                                <th>Mgr Status</th>
                                <th>CA Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Data will be populated via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Table Row -->

          </div>
        </div>

      </div>
    </div>
</section>

<!-- View Details Modal -->
<div class="modal fade" id="dcrDetailsModal" tabindex="-1" aria-labelledby="dcrDetailsModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><span id="modalTitle">DCR Details</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Details will be populated via JavaScript -->
        <div id="dcrDetailsContent">
            <!-- Dynamic Content -->
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

@endsection

@push('styles')
    <style>
        /* Existing styles... */

        /* Optional: Style for the table */
        #dcrApprovalsTable th, #dcrApprovalsTable td {
            vertical-align: middle;
            text-align: center;
        }
    </style>
@endpush

@push('scripts')

    <!-- Your Custom Scripts -->
    <script>
        $(document).ready(function() {

            // Define route URLs with placeholders
            var dcrDetailsRoute = "{{ route('tourplanner.dcr-details', ['id' => ':id']) }}";
            var bloodBankRegisterRoute = "{{ route('bloodbank.register') }}"; // Ensure this route exists
           
           
            // Initialize DataTable
            var table = $('#dcrApprovalsTable').DataTable({
                "columns": [
                    { "data": "title" },
                    { "data": "tour_plan_type" },
                    { "data": "visit_date" },
                    { "data": "time" },
                    { "data": "tp_status" },
                    { "data": "mgr_status" },
                    { "data": "ca_status" },
                    { "data": "actions", "orderable": false, "searchable": false }
                ],
                "order": [[2, "desc"]], // Order by Visit Date descending
                "responsive": true
            });

            // Function to populate Collecting Agents Dropdown
            function loadCollectingAgents(callback) {
                $.ajax({
                    url: "{{ route('tourplanner.getCollectingAgents') }}",
                    type: 'GET',
                    success: function(response) {
                        if(response.success) {

                            var agents = response.data;
                            var dropdown = $('#collectingAgentDropdown');
                            dropdown.empty().append('<option value="">Choose Collecting Agent</option>');
                            $.each(agents, function(index, agent) {
                                var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                dropdown.append(option);
                            });
                            // Trigger Select2 to reinitialize with new options
                            dropdown.trigger('change');
                            if (callback) callback();
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
            loadCollectingAgents(initializeFilters);

            // Function to load DCR Approvals based on filters
            function loadDCRApprovals(agentId, selectedMonth) {
                $.ajax({
                    url: "{{ route('tourplanner.getDCRApprovals') }}",
                    type: 'GET',
                    data: {
                        agent_id: agentId,
                        month: selectedMonth
                    },
                    beforeSend: function() {
                        // Optionally, show a loading indicator
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading()
                            }
                        });
                    },
                    success: function(response) {
                        console.log("getDCRApprovals API Response: ",response);
                        if(response.success) {
                            var events = response.events;

                            // Clear existing table data
                            table.clear();

                            // Iterate over each event and add to the table
                            $.each(events, function(index, event) {
                                // Map tour_plan_type
                                var tourPlanType = event.extendedProps.tour_plan_type === 1 ? 'Collections' :
                                                   event.extendedProps.tour_plan_type === 2 ? 'Sourcing' : '-';

                                // Format visit_date
                                var visitDate = event.visit_date ? event.visit_date : '-';

                                // Format time
                                var time = event.time ? formatTime(event.time) : '-';

                                // Capitalize status
                                var tp_status = event.extendedProps.status ? capitalizeFirstLetter(event.extendedProps.status.replace('_', ' ')) : '-';

                                var mgr_status = event.extendedProps.manager_status ? capitalizeFirstLetter(event.extendedProps.manager_status.replace('_', ' ')) : '-';

                                var ca_status = event.extendedProps.ca_status ? capitalizeFirstLetter(event.extendedProps.ca_status.replace('_', ' ')) : '-';

                                // Actions button
                                // var actions = `<button class="btn btn-sm btn-primary view-details-btn" data-event='${JSON.stringify(event)}'>
                                //                     View Details
                                //                 </button>`;


                                // Generate URLs by replacing the placeholder with actual event.id
                                var viewInfoUrl = dcrDetailsRoute.replace(':id', event.id);
                                

                                // Actions button as a link with URL parameter
                                var actions = `<a href="${viewInfoUrl}" class="btn btn-sm btn-primary mb-1">
                                                View Info
                                            </a>`;

                                            // Check if event_type is 2 (Sourcing) and mgr_status is "accepted"
                                            if (event.extendedProps.tour_plan_type === 2 && 
                                                event.extendedProps.manager_status.toLowerCase() === 'accepted') {
                                               
                                               var registerUrl = bloodBankRegisterRoute + '?id=' + event.id;
                                                // Append "Register" button
                                                actions += `<a href="${registerUrl}" class="btn btn-sm btn-success">
                                                                Register
                                                            </a>`;
                                            }

                                // Add row to DataTable
                                table.row.add({
                                    "title": event.title,
                                    "tour_plan_type": tourPlanType,
                                    "visit_date": visitDate,
                                    "time": time,
                                    "tp_status": tp_status,
                                    "mgr_status": mgr_status,
                                    "ca_status": ca_status,
                                    "actions": actions
                                });
                            });

                            // Draw the table with new data
                            table.draw();

                            Swal.close(); // Close the loading indicator
                        } else {
                            Swal.fire('Error', response.message, 'error');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching DCR Approvals:", error);
                        Swal.fire('Error', 'An error occurred while fetching DCR Approvals.', 'error');
                    }
                });
            }


             // Function to initialize the page with saved filters
            function initializeFilters() {
                var savedAgentId = sessionStorage.getItem('dcrFilterAgentId');
                var savedMonth = sessionStorage.getItem('dcrFilterMonth');

                console.log("initializeFilters");
                console.log("savedAgentId", savedAgentId);
                console.log("savedMonth", savedMonth);


                if (savedMonth) {
                    $('#monthPicker').val(savedMonth);
                }

                if (savedAgentId) {
                    $('#collectingAgentDropdown').val(savedAgentId).trigger('change');
                }

                if (savedMonth) {
                    loadDCRApprovals(savedAgentId, savedMonth);
                }
            }


            // Function to format time (HH:MM:SS to HH:MM AM/PM)
            function formatTime(timeStr) {
                if (!timeStr) return 'N/A';
                var date = new Date('1970-01-01T' + timeStr + 'Z');
                return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
            }

            // Function to capitalize first letter
            function capitalizeFirstLetter(string) {
                return string.charAt(0).toUpperCase() + string.slice(1);
            }

            // Handle Filter Button Click
            $('#filterButton').on('click', function() {
                var agentId = $('#collectingAgentDropdown').val();
                var selectedMonth = $('#monthPicker').val();

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                // Save filters to sessionStorage
                sessionStorage.setItem('dcrFilterAgentId', agentId);
                sessionStorage.setItem('dcrFilterMonth', selectedMonth);

                // Load DCR Approvals with selected filters
                loadDCRApprovals(agentId, selectedMonth);
            });

            // Initial Load: Fetch data for current month and selected agent (if any)
            var initialAgentId = $('#collectingAgentDropdown').val();
            var initialMonth = $('#monthPicker').val();

            // Handle View Details Button Click
            // $('#dcrApprovalsTable tbody').on('click', '.view-details-btn', function() {
            //     var eventData = $(this).data('event');

            //     // Populate the modal with event details
            //     var modalContent = `
            //         <h5>Title: ${eventData.title}</h5>
            //         <p><strong>Tour Plan Type:</strong> ${eventData.extendedProps.tour_plan_type === 1 ? 'Collections' : (eventData.extendedProps.tour_plan_type === 2 ? 'Sourcing' : 'N/A')}</p>
            //         <p><strong>Visit Date:</strong> ${eventData.visit_date}</p>
            //         <p><strong>Time:</strong> ${eventData.time ? formatTime(eventData.time) : 'N/A'}</p>
            //         <p><strong>Status:</strong> ${eventData.extendedProps.status ? capitalizeFirstLetter(eventData.extendedProps.status.replace('_', ' ')) : 'N/A'}</p>
            //         <hr/>
            //         <h6>Details:</h6>
            //         <ul>
            //             <li><strong>Collecting Agent:</strong> ${eventData.extendedProps.collecting_agent_name || 'N/A'}</li>
            //             <li><strong>Blood Bank:</strong> ${eventData.extendedProps.blood_bank_name || 'N/A'}</li>
            //             <li><strong>Quantity:</strong> ${eventData.extendedProps.quantity || '0'}</li>
            //             <li><strong>Available Quantity:</strong> ${eventData.extendedProps.available_quantity || '0'}</li>
            //             <li><strong>Remaining Quantity:</strong> ${eventData.extendedProps.remaining_quantity || '0'}</li>
            //             <li><strong>Price:</strong> ${eventData.extendedProps.price || 'N/A'}</li>
            //             <li><strong>Latitude:</strong> ${eventData.extendedProps.latitude || 'N/A'}</li>
            //             <li><strong>Longitude:</strong> ${eventData.extendedProps.longitude || 'N/A'}</li>
            //             <li><strong>Remarks:</strong> ${eventData.extendedProps.remarks || 'N/A'}</li>
            //             <li><strong>Created By:</strong> ${eventData.extendedProps.created_by_name || 'N/A'}</li>
            //             <li><strong>Created At:</strong> ${eventData.extendedProps.created_at || 'N/A'}</li>
            //         </ul>
            //         <hr/>
            //         <h6>Transport Details:</h6>
            //         ${eventData.extendedProps.transport_details ? `
            //             <ul>
            //                 <li><strong>Vehicle Number:</strong> ${eventData.extendedProps.transport_details.vehicle_number || 'N/A'}</li>
            //                 <li><strong>Driver Name:</strong> ${eventData.extendedProps.transport_details.driver_name || 'N/A'}</li>
            //                 <li><strong>Contact Number:</strong> ${eventData.extendedProps.transport_details.contact_number || 'N/A'}</li>
            //                 <li><strong>Alternative Contact Number:</strong> ${eventData.extendedProps.transport_details.alternative_contact_number || 'N/A'}</li>
            //                 <li><strong>Email ID:</strong> ${eventData.extendedProps.transport_details.email || 'N/A'}</li>
            //                 <li><strong>Remarks:</strong> ${eventData.extendedProps.transport_details.remarks || 'N/A'}</li>
            //             </ul>
            //         ` : '<p>N/A</p>'}
            //         <hr/>
            //         <h6>Attachments:</h6>
            //         ${generateAttachmentsHtml(eventData.extendedProps.dcr_attachments)}
            //     `;

            //     $('#dcrDetailsContent').html(modalContent);
            //     $('#dcrDetailsModal').modal('show');
            // });


             // Listen to pageshow event to handle back navigation
             $(window).on('pageshow', function(event) {
                if (event.originalEvent.persisted) {
                    // Page was restored from the cache, reinitialize filters
                    initializeFilters();
                }
            });

            // Function to generate attachments HTML
            function generateAttachmentsHtml(attachments) {
                if (!attachments || attachments.length === 0) {
                    return '<p>No attachments available.</p>';
                }

                var html = '<div class="attachment-container">';
                $.each(attachments, function(index, attachment) {
                    var fileUrl = `{{ asset('/') }}${attachment.attachment}`;
                    var fileType = getFileType(attachment.attachment_type);
                    var fileIcon = getFileIcon(fileType);
                    html += `
                        <a href="${fileUrl}" target="_blank">
                            <img src="${fileUrl}" alt="Attachment ${index + 1}">
                        </a>
                    `;
                });
                html += '</div>';
                return html;
            }

            // Function to determine file type based on attachment_type
            function getFileType(attachmentType) {
                switch(attachmentType) {
                    case 1: return 'Certificate of Quality';
                    case 2: return 'Donor Reports';
                    case 3: return 'Invoice Copy';
                    case 4: return 'Pending Documents';
                    default: return 'Unknown';
                }
            }

            // Function to get file icon based on file type
            function getFileIcon(fileType) {
                // You can customize icons based on file types if needed
                return '/path/to/default/icon.png'; // Replace with actual icon path
            }

            // Handle Reset Button Click
            $('#resetButton').on('click', function() {
                // Clear sessionStorage
                sessionStorage.removeItem('dcrFilterAgentId');
                sessionStorage.removeItem('dcrFilterMonth');

                // Reset filters
                $('#collectingAgentDropdown').val('').trigger('change');
                $('#monthPicker').val('{{ date('Y-m') }}');

                // 3. Clear existing table data
                table.clear().draw();
               
            });
        });
    </script>
@endpush
