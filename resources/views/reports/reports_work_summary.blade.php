@extends('include.dashboardLayout')

@section('title', 'Reports')

@section('content')

<div class="pagetitle">
    <h1>Periodic Work Summary</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('reports.reports_work_summary') }}">Reports</a></li>
        <li class="breadcrumb-item active">View</li>
      </ol>
    </nav>
</div><!-- End Page Title -->

<section class="section">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-body">
           
            <!-- Header with Button -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title">View Periodic Work Summary</h5>
            </div>

            <!-- Display Success Message -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-1"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Display Error Messages -->
            @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-octagon me-1"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif

            <!-- Filters Row -->
            <div class="row mb-4 align-items-end">
                <!-- Collecting Agent Dropdown -->
                <div class="col-md-4">
                    <label for="collectingAgentDropdown" class="form-label">Collecting Agent</label>
                    <select id="collectingAgentDropdown" class="form-select select2">
                        <option value="">Choose Collecting Agent</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

                <!-- Date Range Picker -->
                <div class="col-md-4">
                    <label for="dateRangePicker" class="form-label">Select Date Range</label>
                    <input type="text" id="dateRangePicker" class="form-control" placeholder="Select date range"/>
                </div>

                <!-- Submit Button -->
                <div class="col-md-2 d-flex align-items-end">
                    <button id="filterButton" class="btn btn-success w-100">
                        <i class="bi bi-filter me-1"></i> Submit
                   </button>
                </div>
            </div>
            <!-- End Filters Row -->

            <!-- Summary Data Table -->
            <table id="periodicWorkSummaryTable" class="table table-striped table-bordered col-lg-12">
              <thead>
                <tr>
                  <th class="text-center">S.No.</th>
                  <th class="text-center">Agent</th>
                  <th class="text-center">Total Collections</th>
                  <th class="text-center">Total Sourcing</th>
                  <th class="text-center">Total Both</th>
                  <th class="text-center">Avg. Collections</th>
                  <th class="text-center">Avg. Sourcing</th>
                  <th class="text-center">Avg. Both</th>
                  <th class="text-center">Total Days</th>
                </tr>
              </thead>
              <tbody>
                <!-- Initially empty: "No records" will be shown -->
              </tbody>
            </table>
            <!-- End Summary Table -->

          </div>
        </div>
      </div>
    </div>
</section>

@endsection

@push('scripts')
<!-- Include moment.js and daterangepicker.js -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<script>
    $(document).ready(function() {

        // Initialize the date range picker
        $('#dateRangePicker').daterangepicker({
            locale: {
                format: 'YYYY-MM-DD'
            },
            autoUpdateInput: false,
            opens: 'right'
        });

        // Update the input when a date range is selected
        $('#dateRangePicker').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('YYYY-MM-DD') + ' - ' + picker.endDate.format('YYYY-MM-DD'));
        });

        // Clear the input if cancel is clicked
        $('#dateRangePicker').on('cancel.daterangepicker', function(ev, picker) {
            $(this).val('');
        });

        // Function to populate Collecting Agents Dropdown
        function loadCollectingAgents() {
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
                        dropdown.trigger('change');
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

        // Initialize DataTable with empty data initially
        var table = $('#periodicWorkSummaryTable').DataTable({
            responsive: true,
            processing: true,
            data: [],
            columns: [
                { 
                    data: null,
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        return meta.row + 1;
                    }
                },
                { data: 'agent_name', className: "text-left" },
                { data: 'total_collections', className: "text-center" },
                { data: 'total_sourcing', className: "text-center" },
                { data: 'total_both', className: "text-center" },
                { data: 'average_collections', className: "text-center" },
                { data: 'average_sourcing', className: "text-center" },
                { data: 'average_both', className: "text-center" },
                { data: 'days_in_range', className: "text-center" }
            ],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No records"
            }
        });

        // On Filter button click, perform the AJAX request to fetch summary data
        $('#filterButton').click(function() {

             // Get the input values
            var dateRange = $('#dateRangePicker').val();
            var collectingAgent = $('#collectingAgentDropdown').val();


            // Validate that both fields are filled
            if (dateRange === '' || collectingAgent === '') {
                Swal.fire('Warning', 'Both date range and collecting agent are required.', 'warning');
                return; // Stop further execution if validation fails
            }

            var postData = {
                _token: "{{ csrf_token() }}",
                dateRange: dateRange,
                collectingAgent: collectingAgent
            };

            $.ajax({
                url: "{{ route('reports.getPeriodicWorkSummary') }}",
                type: 'POST',
                data: postData,
                success: function(json) {
                    if(json.success) {
                        // Update the table with the summary record wrapped in an array
                        table.clear().rows.add([json.data]).draw();
                    } else {
                        Swal.fire('Error', json.message, 'error');
                        table.clear().draw();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching summary report:", error);
                    Swal.fire('Error', 'An error occurred while fetching the data.', 'error');
                }
            });
        });

        // Bootstrap's custom validation (if needed)
        (function () {
          'use strict'
          var forms = document.querySelectorAll('.needs-validation')
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
        })();
    });
</script>
@endpush
