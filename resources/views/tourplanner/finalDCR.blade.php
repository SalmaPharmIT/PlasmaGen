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
                                <th>SI. No.</th>
                                <th>Employee</th>
                                <th>Visit Date</th>
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
            var dcrVisitDetailsRoute = "{{ route('tourplanner.dcrVisit-details', ['id' => ':id']) }}";

            // Initialize DataTable
            var table = $('#dcrApprovalsTable').DataTable({
                "columns": [
                    { 
                        "data": null, 
                        "orderable": true, 
                        "searchable": false,
                        "render": function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                    },
                    { "data": "employee_name" },
                    { "data": "visit_date" },
                    { "data": "visit_status" },
                    { "data": "manager_status" },
                    { "data": "ca_status" },
                    { "data": "actions", "orderable": false, "searchable": false }
                ],
                order: [[0, 'asc']],
                "responsive": true,
                "drawCallback": function(settings) {
                    // Update the SI. No. column after each draw
                    var api = this.api();
                    api.column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
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
                            dropdown.empty().append('<option value="">Choose Collecting Executives</option>');
                            $.each(agents, function(index, agent) {
                                //var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                                var option = '<option value="' + agent.id + '">' + agent.name + ' (' + agent.role.role_name + ')</option>';
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

            // Function to load DCR Approvals based on filters
            function loadDCRApprovals(agentId, selectedMonth) {
                $.ajax({
                    url: "{{ route('tourplanner.getFinalDCRApprovals') }}",
                    type: 'GET',
                    data: {
                        agent_id: agentId,
                        month: selectedMonth
                    },
                    beforeSend: function() {
                        Swal.fire({
                            title: 'Loading...',
                            allowOutsideClick: false,
                            didOpen: () => { Swal.showLoading(); }
                        });
                    },
                    success: function(response) {
                        console.log("final getDCRApprovals API Response: ", response);
                        if(response.success) {
                            var events = response.events;
                            table.clear();
                            $.each(events, function(index, event) {
                               
                                var visitDate = event.visit_date ? event.visit_date : '-';
                                var tp_status = event.visit_status ? capitalizeFirstLetter(event.visit_status.replace('_', ' ')) : '-';
                                var mgr_status = event.manager_status ? capitalizeFirstLetter(event.manager_status.replace('_', ' ')) : '-';
                                var ca_status = event.ca_status ? capitalizeFirstLetter(event.ca_status.replace('_', ' ')) : '-';

                                var viewInfoUrl = dcrVisitDetailsRoute.replace(':id', event.id);
                                // Append visit_date and emp_id as query parameters (using encodeURIComponent to handle special characters)
                                viewInfoUrl += '?visit_date=' + encodeURIComponent(event.visit_date) + '&emp_id=' + encodeURIComponent(event.emp_id);

                                var actions = `<a href="${viewInfoUrl}" class="btn btn-sm btn-primary mb-1">View Info</a>`;

                                // if (event.extendedProps.tour_plan_type === 2 && 
                                //     event.extendedProps.manager_status.toLowerCase() === 'accepted') {
                                //     var registerUrl = bloodBankRegisterRoute + '?id=' + event.id;
                                //     actions += `<a href="${registerUrl}" class="btn btn-sm btn-success">Register</a>`;
                                // }

                                table.row.add({
                                    "employee_name": event.employee_name,
                                    "visit_date": visitDate,
                                    "visit_status": tp_status,
                                    "manager_status": mgr_status,
                                    "ca_status": ca_status,
                                    "actions": actions
                                });
                            });
                            table.draw();
                            Swal.close();
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

            // Function to initialize the page with saved filters or defaults
            function initializeFilters() {
                var savedAgentId = sessionStorage.getItem('dcrFilterAgentId');
                var savedMonth = sessionStorage.getItem('dcrFilterMonth');

                // If no saved month, default to current month (from the monthPicker input)
                if (!savedMonth) {
                    savedMonth = $('#monthPicker').val();
                } else {
                    $('#monthPicker').val(savedMonth);
                }

                if (savedAgentId) {
                    $('#collectingAgentDropdown').val(savedAgentId).trigger('change');
                }

                // Load DCR Approvals with the filters (or defaults)
                loadDCRApprovals(savedAgentId, savedMonth);
            }

            // Load Collecting Agents on page load and initialize filters in the callback
            loadCollectingAgents(initializeFilters);

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

                loadDCRApprovals(agentId, selectedMonth);
            });

            // Handle Reset Button Click
            $('#resetButton').on('click', function() {
                sessionStorage.removeItem('dcrFilterAgentId');
                sessionStorage.removeItem('dcrFilterMonth');
                $('#collectingAgentDropdown').val('').trigger('change');
                $('#monthPicker').val('{{ date('Y-m') }}');
                table.clear().draw();
            });

            // Listen to pageshow event to handle back navigation
            $(window).on('pageshow', function(event) {
                if (event.originalEvent.persisted) {
                    initializeFilters();
                }
            });
            });

    </script>
@endpush
