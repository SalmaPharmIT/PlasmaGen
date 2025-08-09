@extends('include.dashboardLayout')

@section('title', 'Expenses')

@section('content')

<div class="pagetitle">
    <h1>Expenses</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
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
            var expenseVisitDetailsRoute = "{{ route('expenses.view') }}"; // Use base route without placeholders


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
            function loadUpdatedVisitsLists(selectedMonth) {
                $.ajax({
                    url: "{{ route('expenses.getUpdatedVisits') }}",
                    type: 'GET',
                    data: {
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
                                // Format visit_date or use '-' if not available
                                var visitDate = event.visit_date ? event.visit_date : '-';
                                var dcrStatus = event.visit_status ? event.visit_status : '-';
                                var mgrStatus = event.manager_status ? event.manager_status : '-';
                                var caStatus = event.ca_status ? event.ca_status : '-';
                                var typeLabel = event.tour_plan_type ? event.tour_plan_type : '-';
                                var bankNameDisplay = event.visit_to ? event.visit_to : '-';

                                // // Determine the TP Type label based on tp_tour_plan_type
                                // var typeLabel = '';
                                // if (event.tp_tour_plan_type == 1) {
                                //     typeLabel = 'Collection';
                                // } else if (event.tp_tour_plan_type == 2) {
                                //     typeLabel = 'Sourcing';
                                // } else if (event.tp_tour_plan_type == 3) {
                                //     typeLabel = 'Assigned Collections';
                                // } else {
                                //     typeLabel = 'Other';
                                // }

                                // Determine the blood bank name to display.
                                // For Collection (type 1), use the top-level field provided by the API.
                                // For Sourcing (type 2), loop through visits and take the visit_sourcing_blood_bank_name.
                                // var bankNameDisplay = '-';
                                // if (event.tp_tour_plan_type == 1 || event.tp_tour_plan_type == 3) {
                                //     bankNameDisplay = event.visit_collection_blood_bank_name ? event.visit_collection_blood_bank_name : '-';
                                // } else {
                                //     var bankNames = [];
                                //     if (event.visits && event.visits.length > 0) {
                                //         $.each(event.visits, function(i, visit) {
                                //             if (visit.visit_sourcing_blood_bank_name) {
                                //                 bankNames.push(visit.visit_sourcing_blood_bank_name);
                                //             }
                                //         });
                                //     }
                                //     bankNameDisplay = bankNames.length > 0 ? bankNames.join(', ') : '-';
                                // }
                                // console.log('bankNameDisplay', bankNameDisplay);

                               // Dynamically build the URL with query parameters
                                var viewInfoUrl = expenseVisitDetailsRoute + '?date=' + encodeURIComponent(event.visit_date) + '&dcr_id=' + encodeURIComponent(event.dcr_id);

                                // Create the "View Info" button
                                var actions = `<a href="${viewInfoUrl}" class="btn btn-sm btn-primary mb-1">Add Expense</a>`;

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
                var savedMonth = sessionStorage.getItem('dcrFilterMonth');

                // If no saved month, default to current month (from the monthPicker input)
                if (!savedMonth) {
                    savedMonth = $('#monthPicker').val();
                } else {
                    $('#monthPicker').val(savedMonth);
                }

                // Load DCR Approvals with the filters (or defaults)
                loadUpdatedVisitsLists(savedMonth);
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
                var selectedMonth = $('#monthPicker').val();

                if (!selectedMonth) {
                    Swal.fire('Warning', 'Please select a month.', 'warning');
                    return;
                }

                loadUpdatedVisitsLists(selectedMonth);
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

              // Helper function to capitalize the first letter
        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
    </script>
@endpush
