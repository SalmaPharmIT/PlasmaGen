@extends('include.dashboardLayout')

@section('title', 'Reports')

@section('content')

<div class="pagetitle">
    <h1>User Expenses Summary</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('reports.user_expenses_summary') }}">Reports</a></li>
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
                <h5 class="card-title">View Expenses</h5>
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
                    <label for="collectingAgentDropdown" class="form-label">Executives</label>
                    <select id="collectingAgentDropdown" class="form-select select2" name="collecting_agent_id[]" multiple>
                        <option value="">Choose Executives</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

                <!-- Date Range Picker -->
                <div class="col-md-4 mt-2">
                    <label for="dateRangePicker" class="form-label">Select Date Range</label>
                    <input type="text" id="dateRangePicker" class="form-control" placeholder="Select date range"/>
                </div>

                <!-- Submit Button -->
                <div class="col-md-2 mt-2 d-flex align-items-end">
                    <button id="filterButton" class="btn btn-success w-100">
                        <i class="bi bi-filter me-1"></i> Submit
                   </button>
                </div>

                 <!-- Export Button -->
                <div class="col-md-2 mt-2 d-flex align-items-end">
                    <button id="exportButton" class="btn btn-info w-100">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                </div>
            </div>
            <!-- End Filters Row -->

            <!-- Summary Data Table -->
            <div class="table-responsive">
                <table id="userWiseCollectionSummaryTable" class="table table-striped table-bordered col-lg-12">
                <thead>
                    <tr>
                    <th class="text-center">SI.No.</th>
                    <th class="text-center">Executive</th>
                    <th class="text-center">Date</th>
                    <th class="text-center">Description</th>
                    <th class="text-center">Food</th>
                    <th class="text-center">Conveyance</th>
                    <th class="text-center">Tel/Fax</th>
                    <th class="text-center">Lodging</th>
                    <th class="text-center">Sundry</th>
                    <th class="text-center">Total Price</th>
                    <th class="text-center">Attachments</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Initially empty: "No records" will be shown -->
                </tbody>
                </table>
            </div>
            <!-- End Summary Table -->

          </div>
        </div>
      </div>
    </div>
</section>

@endsection


@push('styles')
<style>
   .table-responsive {
        overflow-x: auto;
        width: 100%;
        border-collapse: collapse;
    }

    .table td {
        max-width: 200px; /* Adjust according to your needs */
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .table td, .table th {
        word-wrap: break-word;
        white-space: normal;
    }
  
    /* Truncate text in Name, Mobile, and Email columns */
    .table td, .table th {
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<!-- Include moment.js and daterangepicker.js -->
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

<!-- DataTables Buttons JS, JSZip, and HTML5 export -->
<script src="https://cdn.datatables.net/buttons/2.3.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.5/js/buttons.html5.min.js"></script>

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
                          //  var option = '<option value="' + agent.id + '">' + agent.name + '</option>';
                            var option = '<option value="' + agent.id + '" data-role-id="' + agent.role_id + '">' + agent.name + ' (' + agent.role.role_name + ')</option>';
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
        var table = $('#userWiseCollectionSummaryTable').DataTable({
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
                { data: 'collecting_agent_name', className: "text-center" },
                { data: 'date', className: "text-center" },
                { data: 'description', className: "text-center" },
                { data: 'food', className: "text-center" },
                { data: 'convention', className: "text-center" },
                { data: 'tel_fax', className: "text-center" },
                { data: 'lodging', className: "text-center" },
                { data: 'sundry', className: "text-center" },
                { data: 'total_price', className: "text-center" },
                // Updated Attachments column with image preview rendering
                {
                    data: 'documents',
                    className: "text-center",
                    render: function(data, type, row, meta) {
                        let html = '';
                        if (data && data.length > 0) {
                            data.forEach(function(doc) {
                                const docUrl = "{{ config('auth_api.base_image_url') }}" + doc.attachments;
                                if (doc.attachments.match(/\.(jpg|jpeg|png|gif|svg)$/i)) {
                                    html += `
                                        <div class="preview-item d-inline-block me-1">
                                            <a href="${docUrl}" target="_blank">
                                                <img src="${docUrl}" alt="Document Preview" class="img-thumbnail" style="width: 50px; height: 50px;">
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    html += `
                                        <div class="preview-item d-inline-block me-1">
                                            <a href="${docUrl}" target="_blank">
                                                <i class="bi bi-file-earmark-text-fill" style="font-size: 2rem;"></i>
                                            </a>
                                        </div>
                                    `;
                                }
                            });
                        } else {
                            html = 'No attachments';
                        }
                        return html;
                    }
                },
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No records"
            },
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'User Expenses Summary'
                }
            ]
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
                url: "{{ route('reports.getUserExpensesSummary') }}",
                type: 'POST',
                data: postData,
                success: function(json) {
                    if(json.success) {
                        // Update the table with the summary record wrapped in an array
                        table.clear().rows.add(json.data).draw();
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

        $('#exportButton').on('click', function() {
            table.button(0).trigger(); // Triggers the first button (Excel export)
        });
    });
</script>
@endpush
