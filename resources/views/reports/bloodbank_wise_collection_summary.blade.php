@extends('include.dashboardLayout')

@section('title', 'Reports')

@section('content')

<div class="pagetitle">
    <h1>Blood Bank Wise Collection Summary</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('reports.bloodbank_wise_collection_summary') }}">Reports</a></li>
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
                <h5 class="card-title">Blood Bank Wise Collection Summary</h5>
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

               <!-- Blood Bank Dropdown -->
               <div class="col-md-6">
                    <label for="bloodBankDropdown" class="form-label">Blood Banks</label>
                    <select id="bloodBankDropdown" class="form-select select2" name="blood_banks_id[]" multiple>
                        <option value="">Choose Blood Banks</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

               <!-- States Dropdown -->
               <div class="col-md-6">
                    <label for="statesDropdown" class="form-label">States</label>
                    <select id="statesDropdown" class="form-select select2" name="states_id[]" multiple>
                        <option value="">Choose States</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>

                <!-- States Dropdown -->
               <div class="col-md-6">
                    <label for="cityDropdown" class="form-label">Cities</label>
                    <select id="cityDropdown" class="form-select select2" name="cities_id[]" multiple>
                        <option value="">Choose Cities</option>
                        <!-- Options will be populated via AJAX -->
                    </select>
                </div>



                <!-- Date Range Picker -->
                <div class="col-md-4 mt-2">
                    <label for="dateRangePicker" class="form-label">Select Date Range</label>
                    <input type="text" id="dateRangePicker" class="form-control" placeholder="Select date range"/>
                </div>

                <!-- Submit Button -->
                <div class="col-md-1 mt-2 d-flex align-items-end">
                    <button id="filterButton" class="btn btn-success w-80">
                        Submit
                   </button>
                </div>

                 <!-- Export Button -->
                <div class="col-md-1 mt-2 d-flex align-items-end">
                    <button id="exportButton" class="btn btn-info w-80">
                       Export
                    </button>
                </div>
                <div class="col-md-12 mt-2">
                    <h6>Note: Choose either Blood banks or States & Cities to filter the data !!!</h6>
                <div>

               
            </div>
            <!-- End Filters Row -->

            <!-- Summary Data Table -->
            <div class="table-responsive">
                <table id="collectionSummaryTable" class="table table-striped table-bordered col-lg-12">
                <thead>
                    <tr>
                    <th class="text-center">SI.No.</th>
                    <th class="text-center">Blood Bank</th>
                    <th class="text-center">Planned Qty.</th>
                    <th class="text-center">Total Collected</th>
                    <th class="text-center">Total Remaining</th>
                    <th class="text-center">Total Price</th>
                    <th class="text-center">Total Part-A Price</th>
                    <th class="text-center">Total Part-B Price</th>
                    <th class="text-center">Total Part-C Price</th>
                    <th class="text-center">Total GST Amt.</th>
                    <th class="text-center">Total Invoice Price</th>
                    {{-- <th class="text-center">TP Status</th>
                    <th class="text-center">MGR Status</th>
                    <th class="text-center">CA Status</th> --}}
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
        var urlcityByStateIdTemplate = "{{ route('api.citiesByStateIds', ['stateId' => '__STATE_ID__']) }}";
        var urlBloodbanksByCityTemplate = "{{ url('users/workLocationMapping/bloodbanks') }}/__CITY_ID__";

        // Define dropdown variables for Country, State and City
        var stateDropdown = $('#statesDropdown');
        var cityDropdown = $('#cityDropdown');

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
            
            if (countryId) {
                $.ajax({
                    url: urlGetStates,
                    type: 'GET',
                    success: function(data) {
                        console.log("States fetched:", data);
                    
                        // Clear the dropdown and add "Select All" as the first option
                        stateDropdown.empty();
                        stateDropdown.append('<option value="all">SELECT ALL</option>');
                        
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
            var selectedStateIds = $(this).val();
            console.log("Selected state IDs:", selectedStateIds);
            
            // Clear the cities dropdown first
            cityDropdown.empty().append('<option value="">Choose City</option>');
            
            if (selectedStateIds && selectedStateIds.length > 0) {
                // If "all" is selected, replace with all available state IDs (excluding "all")
                if (selectedStateIds.includes("all")) {
                    var allStates = [];
                    $(this).find('option').each(function() {
                        var val = $(this).val();
                        if (val !== "all" && val !== "") {
                            allStates.push(val);
                        }
                    });
                    selectedStateIds = allStates;
                    $(this).val(allStates).trigger('change');
                }
                
                // Create a comma-separated list of state IDs
                var ids = selectedStateIds.join(',');
                
                // Replace the placeholder in your API URL with the comma-separated state IDs
                var cityUrl = urlcityByStateIdTemplate.replace('__STATE_ID__', ids);
                console.log("City URL:", cityUrl);
                
                // Make one API call with all state IDs
                $.ajax({
                    url: cityUrl,
                    type: 'GET',
                    success: function(data) {
                         // Clear the dropdown and add "Select All" as the first option
                         cityDropdown.empty().append('<option value="all">SELECT ALL</option>');

                        if (data.success) {
                            data.data.forEach(function(city) {
                                cityDropdown.append(`<option value="${city.id}">${city.name}</option>`);
                            });
                        } else {
                            console.error("No cities available for states: " + ids);
                        }
                    },
                    error: function(error) {
                        console.error("Error fetching cities for states: " + ids, error);
                    }
                });
            }
        });


        // Attach a change event handler for the city dropdown to auto-select all options when "Select All" is chosen.
        $('#cityDropdown').on('change', function() {
            var selectedCityIds = $(this).val();
            if (selectedCityIds && selectedCityIds.includes("all")) {
                var allCities = [];
                // Gather all city option values except "all" and empty ones
                $(this).find('option').each(function() {
                    var val = $(this).val();
                    if (val !== "all" && val !== "") {
                        allCities.push(val);
                    }
                });
                // Set the dropdown's value to all available city IDs and trigger change so that any plugins (like Select2) update accordingly.
                $(this).val(allCities).trigger('change');
            }
        });


        function loadBloodBanks() {
            console.log("Fetching bloodbanks");
         
            var bloodBankDropdown = $('#bloodBankDropdown');

            $.ajax({
                    url: "{{ route('api.bloodbanks') }}",
                    type: 'GET',
                    success: function(data) {
                        console.log("bloodbanks fetched:", data);
                    
                        // Clear the dropdown and add "Select All" as the first option
                        bloodBankDropdown.empty();
                        bloodBankDropdown.append('<option value="">Choose Blood Banks</option>')
                        bloodBankDropdown.append('<option value="all">SELECT ALL</option>');

                        
                        if (data.success) {
                            data.data.forEach(bloodBank => {
                                bloodBankDropdown.append(`<option value="${bloodBank.id}">${bloodBank.name}, ${bloodBank.city.name}, ${bloodBank.state.name}</option>`);
                            });
                        } else {
                            alert(data.message || 'No bloodBanks available.');
                        }
                        // Refresh the Select2 plugin (if using it)
                        bloodBankDropdown.trigger('change');
                    },
                    error: function(error) {
                        console.error("Error fetching bloodBank:", error);
                        alert('Failed to fetch bloodBank. Please check the server logs for details.');
                    }
                });
        }

        // Blood-Banks “Select All” behavior
        $('#bloodBankDropdown').on('change', function() {
        const sel = $(this).val() || [];

        if (sel.includes('all')) {
            // grab every real ID (skip empty + “all”)
            const allIds = $('#bloodBankDropdown option')
            .map(function() { return this.value; })
            .get()
            .filter(v => v && v !== 'all');

            // replace selection with the full list
            $(this)
            .val(allIds)
            .trigger('change.select2');
        }
        });


        // Load States on page load
        loadStatesLists();

        // Load all Blood Banks on page load
        loadBloodBanks();

        // Initialize DataTable with empty data initially
        var table = $('#collectionSummaryTable').DataTable({
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
                {
                    data: null,
                    className: "text-left",
                    render: function(data, type, row) {
                        // Retrieve the values from the row, handling missing values if necessary
                        var bankName = row.blood_bank_name || '';
                        var city = row.city_name || '';
                        var state = row.state_name || '';
                        // Combine the values. Adjust the format as needed.
                        return (bankName + ' (' + city + ', ' + state + ')').toUpperCase();
                    }
                },
                { data: 'total_quantity', className: "text-center" },
                { data: 'total_available_quantity', className: "text-center" },
                { data: 'total_remaining_quantity', className: "text-center" },
                { data: 'total_price', className: "text-center" },
                { data: 'total_part_a_invoice_price', className: "text-center" },
                { data: 'total_part_b_invoice_price', className: "text-center" },
                { data: 'total_part_c_invoice_price', className: "text-center" },
                { 
                    data: 'total_gst_amount', 
                    className: "text-center",
                    render: function(data, type, row) {
                        var value = parseFloat(data);
                        return !isNaN(value) ? value.toFixed(2) : "0.00";
                    }
                },
                { data: 'total_invoice_price', className: "text-center" },
                // { 
                //     data: 'status', 
                //     className: "text-center", 
                //     render: function(data, type, row) {
                //         return data ? data.toUpperCase() : data;
                //     } 
                // },
                // { 
                //     data: 'manager_status', 
                //     className: "text-center", 
                //     render: function(data, type, row) {
                //         return data ? data.toUpperCase() : data;
                //     } 
                // },
                // { 
                //     data: 'ca_status', 
                //     className: "text-center", 
                //     render: function(data, type, row) {
                //         return data ? data.toUpperCase() : data;
                //     } 
                // },
            ],
            order: [[0, 'asc']], // Sort by the first column (ID) in descending order
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            language: {
                emptyTable: "No records"
            },
           // dom: 'Bfrtip', // Define the control elements to appear on the page
            buttons: [
                {
                    extend: 'excelHtml5',
                    title: 'User Wise Collection Summary'
                }
            ]
        });

      

        // On Filter button click, perform the AJAX request to fetch summary data
        $('#filterButton').click(function() {

            // Get field values
            var dateRange = $('#dateRangePicker').val();
            var bloodBanks = $('#bloodBankDropdown').val();  // Array of selected blood bank IDs
            var states = $('#statesDropdown').val();          // Array of selected state IDs
            var cities = $('#cityDropdown').val();            // Array of selected city IDs

            // Validate that date range is provided
            if (dateRange === '') {
                Swal.fire('Warning', 'Date range is required.', 'warning');
                return;
            }

            // Validate that either blood banks are selected OR both states and cities are selected.
            // if ((!bloodBanks || bloodBanks.length === 0) && 
            //     (!states || states.length === 0 || !cities || cities.length === 0)) {
            //     Swal.fire('Warning', 'Please select one or more Blood Banks or select both States and Cities.', 'warning');
            //     return;
            // }

            // Option A: Blood Banks provided and States/Cities are empty.
            // Option B: Blood Banks are empty and both States and Cities are provided.
            var isBloodBankFilter = (bloodBanks && bloodBanks.length > 0);
            var isStateCityFilter = (states && states.length > 0 && cities && cities.length > 0);
            
            // If user selects both or none then show an error.
            if ((isBloodBankFilter && (states && states.length > 0 || cities && cities.length > 0)) ||
                (!isBloodBankFilter && !isStateCityFilter)) {
                Swal.fire('Warning', 'Please select either Blood Banks OR both States & Cities along with the date range.', 'warning');
                return;
            }


            // Prepare the data to submit
            var postData = {
                _token: "{{ csrf_token() }}",
                dateRange: dateRange,
                bloodBanks: bloodBanks,
                states: states,
                cities: cities
            };


            $.ajax({
                url: "{{ route('reports.getBloodBankWiseColllectionSummary') }}",
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
