@extends('include.dashboardLayout')

@section('title', 'Reports')

@section('content')

<div class="pagetitle">
    <h1>Blood Bank Summary</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('reports.blood_banks_summary') }}">Blood Banks</a></li>
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
                <h5 class="card-title">View Blood Banks</h5>
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
                <!-- States Dropdown -->
                <div class="col-md-12">
                    <label for="statesDropdown" class="form-label">States</label>
                    <select id="statesDropdown" class="form-select select2" name="collecting_agent_id[]" multiple>
                        <option value="">Choose States</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

                {{-- <!-- Date Range Picker -->
                <div class="col-md-6 mt-2">
                    <label for="dateRangePicker" class="form-label">Select Date Range</label>
                    <input type="text" id="dateRangePicker" class="form-control" placeholder="Select date range"/>
                </div> --}}

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
                <table id="bloodBankSummaryTable" class="table table-striped table-bordered col-lg-12">
                    <thead>
                    <tr>
                        <th class="text-center">SI. No.</th>
                        <th class="text-center">Name</th>
                        <th class="text-center">Mobile</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">City</th>
                        <th class="text-center">State</th>
                        <th class="text-center">FFP Procurement Company</th>
                        <th class="text-center">Final Accepted Offer</th>
                        <th class="text-center">Payment Terms</th>
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

        var urlGetStatesByIdTemplate = "{{ route('api.states', ['countryId' => '__COUNTRY_ID__']) }}";

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

 
        function loadStatesLists() {
            const countryId = '1';
            console.log("Fetching states for countryId:", countryId);
            var urlGetStates = urlGetStatesByIdTemplate.replace('__COUNTRY_ID__', countryId);
            var stateDropdown = $('#statesDropdown');

            if (countryId) {
                $.ajax({
                    url: urlGetStates,
                    type: 'GET',
                    success: function(data) {
                        console.log("States fetched:", data);
                    
                        // Clear the dropdown and add "Select All" as the first option
                        stateDropdown.empty();
                        stateDropdown.append('<option value="all">Select All</option>');
                        
                        if (data.success) {
                            data.data.forEach(state => {
                                stateDropdown.append(`<option value="${state.id}">${state.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No states available for the selected country.');
                        }
                        // Refresh the Select2 plugin (if using it)
                        stateDropdown.trigger('change');
                    },
                    error: function(error) {
                        console.error("Error fetching states:", error);
                        alert('Failed to fetch states. Please check the server logs for details.');
                    }
                });
            } else {
                stateDropdown.empty().append('<option value="">Choose State</option>');
            }
        }

        // Attach a change event handler to automatically select all options when "Select All" is chosen
        $('#statesDropdown').on('change', function() {
            var selectedValues = $(this).val();
            if(selectedValues && selectedValues.includes("all")){
                // Build an array of all state option values (exclude "all" and any empty value)
                var allStates = [];
                $(this).find('option').each(function() {
                    var val = $(this).val();
                    if(val !== "all" && val !== "") {
                        allStates.push(val);
                    }
                });
                // Set the dropdown's value to all available states and trigger change so Select2 updates
                $(this).val(allStates).trigger('change');
            }
        });


        // Load Collecting Agents on page load
        loadStatesLists();

        // Initialize DataTable with empty data initially
        var table = $('#bloodBankSummaryTable').DataTable({
            responsive: true,
            processing: true,
            serverSide: false, // Set to true if implementing server-side processing
            data: [],
            columns: [
                { 
                        "data": null, 
                        className: "text-center",
                        "orderable": true, 
                        "searchable": false,
                        "render": function (data, type, row, meta) {
                            return meta.row + 1;
                        }
                },
                { data: 'blood_bank_name', className: "text-left" },
                { data: 'mobile_no', className: "text-center" },
                { data: 'email', className: "text-center" },
                { data: 'account_status', className: "text-center" },
                { data: 'city_name', className: "text-center" },
                { data: 'state_name', className: "text-center" },
                { data: 'FFP_rocurement_company', className: "text-center" },
                { data: 'final_accepted_offer', className: "text-center" },
                { data: 'payment_terms', className: "text-center" },
                // Hidden columns (with titles so they export)
                { data: 'contact_person', title: 'Contact Person', visible: false },
                { data: 'state_name', title: 'State', visible: false },
                { data: 'city_name', title: 'City', visible: false },
                { data: 'country_name', title: 'Country', visible: false },
                { data: 'logo', title: 'Logo', visible: false },
                { data: 'gstin', title: 'GSTIN', visible: false },
                { data: 'license_validity', title: 'License Validity', visible: false },
                { data: 'latitude', title: 'Latitude', visible: false },
                { data: 'longitude', title: 'Longitude', visible: false },
                { data: 'entity_customer_care_no', title: 'Customer Care', visible: false },
                { data: 'created_at', title: 'Created At', visible: false }
            ],
            order: [[0, 'asc']],
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No records"
            },
          //  dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'Blood Bank Summary',
                    
                }
            ]
        });


        // On Filter button click, perform the AJAX request to fetch summary data
        $('#filterButton').click(function() {

             // Get the input values
            var dateRange = $('#dateRangePicker').val();
            var stateIds = $('#statesDropdown').val();
            console.log('stateIds', stateIds);


            // Validate that both fields are filled
            if (stateIds === '') {
                Swal.fire('Warning', 'States are mandatory.', 'warning');
                return; // Stop further execution if validation fails
            }

            var postData = {
                _token: "{{ csrf_token() }}",
                dateRange: dateRange,
                stateIds: stateIds
            };

            $.ajax({
                url: "{{ route('reports.getBloodBankSummary') }}",
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
