@extends('include.dashboardLayout')

@section('title', 'Expense Clearance')

@section('content')

<div class="pagetitle">
    <h1>Expense Clearance</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('expenses.clearance') }}">Expenses Clearance</a></li>
        <li class="breadcrumb-item active">Views</li>
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
                    <select id="collectingAgentDropdown" class="form-select select2" name="collecting_agent_id[]" multiple>
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
                                <th>TP Type</th>
                                <th>Visit Date</th>
                                <th>Blood Bank</th>
                                <th>DCR Status</th>
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
            var expenseVisitDetailsRoute = "{{ route('expenses.details') }}"; // Use base route without placeholders


            initializeFilters();
            // Initialize DataTable with updated column definitions:
            var table = $('#dcrApprovalsTable').DataTable({
                "columns": [
                    { "data": null, "orderable": false, "searchable": false },
                    { "data": "tp_type" },
                    { "data": "visit_date" },
                    { "data": "bank" },
                    { "data": "dcr_status" },
                    { "data": "mgr_status" },
                    { "data": "ca_status" },
                    { "data": "actions", "orderable": false, "searchable": false }
                ],
                "order": [[2, "desc"]], // Order by Visit Date descending
                "responsive": true,
                "drawCallback": function(settings) {
                    // Update the SI. No. column after each draw
                    var api = this.api();
                    api.column(0, {search:'applied', order:'applied'}).nodes().each(function(cell, i) {
                        cell.innerHTML = i + 1;
                    });
                }
            });

        

            // Function to load DCR Approvals based on filters
            function loadUpdatedVisitsLists(selectedMonth, selectedAgentId) {
                $.ajax({
                    url: "{{ route('expenses.getAllExpenses') }}",
                    type: 'GET',
                    data: {
                        month: selectedMonth,
                        agent_id: selectedAgentId
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
                                // Format visit_date or use '-' if not available
                                var visitDate = event.visit_date ? event.visit_date : '-';
                                var dcrStatus = event.visit_status ? event.visit_status : '-';
                                var mgrStatus = event.manager_status ? event.manager_status : '-';
                                var caStatus = event.ca_status ? event.ca_status : '-';
                                var typeLabel = event.tour_plan_type ? event.tour_plan_type : '-';
                                var bankNameDisplay = event.visit_to ? event.visit_to : '-';

                               // Dynamically build the URL with query parameters
                                var viewInfoUrl = expenseVisitDetailsRoute + '?date=' + encodeURIComponent(event.visit_date) + '&dcr_id=' + encodeURIComponent(event.dcr_id);

                                // Create the "View Info" button
                                var actions = `<a href="${viewInfoUrl}" class="btn btn-sm btn-primary mb-1">View</a>`;

                                // Add the row to the DataTable.
                                table.row.add({
                                    "tp_type": typeLabel,  // TP Type column
                                    "visit_date": visitDate,
                                    "bank": bankNameDisplay, // Display the blood bank name
                                    "dcr_status": capitalizeFirstLetter(dcrStatus).toUpperCase(),
                                    "mgr_status": capitalizeFirstLetter(mgrStatus).toUpperCase(),
                                    "ca_status": capitalizeFirstLetter(caStatus).toUpperCase(),
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
                loadUpdatedVisitsLists(savedMonth, savedAgentId);
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
                var agentIds = $('#collectingAgentDropdown').val() || [];  // always an array
                // (no need to check for 'all': our select-all code already expanded it)
                var agentParam = Array.isArray(agentIds) ? agentIds.join(',') : agentIds;

                var selectedMonth = $('#monthPicker').val();

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                 // Save filters to sessionStorage
                sessionStorage.setItem('dcrFilterAgentId', agentParam);
                sessionStorage.setItem('dcrFilterMonth', selectedMonth);

                loadUpdatedVisitsLists(selectedMonth, agentParam);
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


             // 2. Load executives dropdown
            function loadCollectingAgents(){
                $.get("{{ route('tourplanner.getCollectingAgents') }}", res => {
                // if(res.success){
                //   const dd = $('#collectingAgentDropdown').empty().append('<option value="">Choose Executives</option>');
                //   res.data.forEach(a => dd.append(`<option value="${a.id}">${a.name} (${a.role.role_name})</option>`));
                // } else {
                //   Swal.fire('Error', res.message, 'error');
                // }
                if(!res.success) return Swal.fire('Error', res.message,'error');

                const dd = $('#collectingAgentDropdown').empty();
                // 2.1 add the "Select All" first
                dd.append(new Option('Select All','all'));
                // 2.2 then all the real agents
                res.data.forEach(a => {
                    dd.append(new Option(`${a.name} (${a.role.role_name})`, a.id));
                });
                // notify select2 of the change
                dd.trigger('change');
                });
            }

            
            // 3. When the user picks "Select All", grab every real option and select it
            $('#collectingAgentDropdown').on('select2:select', function(e) {
                if (e.params.data.id === 'all') {
                const allIds = $(this).find('option')
                    .map((_,opt) => opt.value)
                    .get()
                    .filter(v => v && v !== 'all');
                // overwrite the selection with all real IDs
                $(this).val(allIds).trigger('change');
                }
            });


        });

              // Helper function to capitalize the first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endpush
