@extends('include.dashboardLayout')

@section('title', 'Generate Destruction No')
@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- SweetAlert2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .card {
        margin: 0.5rem;
    }
    .form-control-sm, .form-select-sm {
        height: 22px;
        min-height: 22px;
        padding: 0.1rem 0.2rem;
        font-size: 0.8rem;
        width: 100%;
    }
    .table.excel-like {
        margin-bottom: 0;
        width: 100%;
        table-layout: fixed;
    }
    .excel-like td {
        padding: 4px 8px;
        vertical-align: middle;
        position: relative;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .excel-like td.text-center {
        padding: 8px;
        line-height: 1.2;
    }
    .table-bordered > :not(caption) > * > * {
        border-width: 1px;
    }
    /* Group of 3 styling */
    .excel-like tr:nth-child(3n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(3n+1) {
        border-top: 2px solid #000;
    }

    /* Logo Styles */
    .logo-cell {
        width: 200px;
        padding: 8px;
    }
    .logo-cell img {
        width: 180px;
        height: auto;
        display: block;
    }

    /* Column Width Styles */
    .excel-like .sr-no-col { width: 30px !important; min-width: 30px !important; max-width: 30px !important; }
    .excel-like .donor-id-col { width: 200px !important; min-width: 200px !important; }
    .excel-like .donation-date-col { width: 120px !important; min-width: 120px !important; }
    .excel-like .blood-group-col { width: 100px !important; min-width: 100px !important; }
    .excel-like .bag-volume-col { width: 100px !important; min-width: 100px !important; }
    .excel-like .remarks-col { width: 150px !important; min-width: 150px !important; }

    /* Updated Select2 Custom Styles */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 4px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-dropdown {
        border: 1px solid #ced4da !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da !important;
        padding: 6px !important;
    }
    .select2-results__option {
        padding: 6px 12px !important;
    }

    /* Force table layout */
    .table.excel-like {
        table-layout: fixed !important;
        width: 100% !important;
    }

    /* Table Layout */
    .table.excel-like {
        margin-bottom: 0;
        width: 100%;
        table-layout: fixed;
    }

    /* Header Table */
    .header-table {
        width: 100%;
        margin-bottom: 0;
    }
    .header-table td {
        padding: 4px 8px;
        vertical-align: middle;
    }

    /* Data Table */
    .data-table {
        width: 100%;
        margin-top: 0;
    }
    .data-table td {
        padding: 4px 8px;
        vertical-align: middle;
        font-size: 0.8rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Column Width Styles */
    .data-table .sr-no-col { width: 30px !important; min-width: 30px !important; max-width: 30px !important; }
    .data-table .donor-id-col { width: 200px !important; min-width: 200px !important; }
    .data-table .donation-date-col { width: 120px !important; min-width: 120px !important; }
    .data-table .blood-group-col { width: 100px !important; min-width: 100px !important; }
    .data-table .bag-volume-col { width: 100px !important; min-width: 100px !important; }
    .data-table .remarks-col { width: 150px !important; min-width: 150px !important; }

    /* Contenteditable cell styles */
    .remarks-cell {
        padding: 4px 8px;
        border: 1px solid #ced4da;
        border-radius: 4px;
        background-color: #fff;
        cursor: text;
    }
    .remarks-cell:focus {
        outline: none;
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .remarks-cell:hover {
        background-color: #f8f9fa;
    }

    /* Dropdown in table cell styles */
    .remarks-select {
        width: 100%;
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        height: 100%;
    }
    .remarks-select:focus {
        outline: none;
        box-shadow: none;
    }
    .table td {
        padding: 0 !important;
    }
    .table td .form-control-sm {
        border-radius: 0;
        height: 100%;
        min-height: 100%;
    }
</style>
@endpush

@section('content')

<div class="pagetitle">
    <h1>Generate Destruction No</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('newBag.index') }}">Bag Entry</a></li>
            <li class="breadcrumb-item active">Generate Destruction No</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('plasma.rejection.store') }}" id="plasmaRejectionForm">
                        @csrf
                        <input type="hidden" name="debug_token" value="{{ time() }}">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label for="ar_number" class="form-label">
                                    <i class="bi bi-hash me-1"></i>A.R. Number
                                </label>
                                <select class="form-select"
                                       id="ar_number"
                                       name="ar_number"
                                       required>
                                    <option value="">Select A.R. Number</option>
                                </select>
                                <div class="invalid-feedback">Please select A.R. Number.</div>
                            </div>
                            <div class="col-md-2">

                            </div>
                            <div class="col-md-4">
                                <div class="mt-4 d-flex align-items-center">
                                    <strong>Quality Rejected List</strong>
                                    <input class="form-check-input ms-3" type="checkbox" id="quality_rejected_list" name="quality_rejected_list" value="1">
                                </div>
                            </div>
                        </div>

                        <!-- Report Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark header-table">
                                <tr>
                                    <td class="logo-cell">
                                        <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo">
                                    </td>
                                    <td colspan="10" class="text-center">
                                        <h5 class="mb-0">PLASMA REJECTION RECORD</h5>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="4">
                                        <strong>Blood Centre Name & City:</strong> <span id="display_blood_centre"></span>
                                    </td>
                                    <td colspan="2">
                                        <strong>Pickup Date:</strong> <span id="display_pickup_date"></span>
                                    </td>
                                    <td colspan="2">
                                        <strong>Receipt Date:</strong> <span id="display_date"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark data-table">
                                <thead>
                                    <tr>
                                        <td class="text-center sr-no-col">Sr.No.</td>
                                        <td class="text-center donor-id-col">DonorID / Mini Pool ID / Mega Pool ID</td>
                                        <td class="text-center donation-date-col">Donation Date</td>
                                        <td class="text-center blood-group-col">Blood Group</td>
                                        <td class="text-center bag-volume-col">Bag Volumen in ML</td>
                                        <td class="text-center remarks-col">Remarks</td>
                                    </tr>
                                </thead>
                                <tbody id="reportBody">
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm donor-id" name="donor_id[]">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm donation-date" name="donation_date[]">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm blood-group" name="blood_group[]">
                                                <option value="">Select</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm bag-volume" name="bag_volume[]">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm remarks-select" name="remarks[]">
                                                <option value="">Select</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Hemolyzed (Red)">Hemolyzed (Red)</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Quality Rejected">Quality Rejected</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">2</td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm donor-id" name="donor_id[]">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm donation-date" name="donation_date[]">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm blood-group" name="blood_group[]">
                                                <option value="">Select</option>
                                                <option value="A+">A+</option>
                                                <option value="A-">A-</option>
                                                <option value="B+">B+</option>
                                                <option value="B-">B-</option>
                                                <option value="AB+">AB+</option>
                                                <option value="AB-">AB-</option>
                                                <option value="O+">O+</option>
                                                <option value="O-">O-</option>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" step="0.01" class="form-control form-control-sm bag-volume" name="bag_volume[]">
                                        </td>
                                        <td>
                                            <select class="form-control form-control-sm remarks-select" name="remarks[]">
                                                <option value="">Select</option>
                                                <option value="Damaged">Damaged</option>
                                                <option value="Hemolyzed (Red)">Hemolyzed (Red)</option>
                                                <option value="Expired">Expired</option>
                                                <option value="Quality Rejected">Quality Rejected</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr id="buttonRow" class="table-secondary" style="height: 28px;">
                                        <td colspan="4"></td>
                                        <td class="text-right">
                                            <strong>Total:</strong> <span id="totalVolume">0.00</span> ml
                                        </td>
                                        <td class="text-right">
                                            <button type="button" id="addRowBtn" class="btn btn-success btn-sm py-0 px-2" style="font-size: 0.7rem;">
                                                <i class="bi bi-plus-circle"></i> Add Row
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@if(session('success'))
    <div class="alert alert-success mt-3">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger mt-3">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger mt-3">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Add Row button visibility based on checkbox state
    if ($('#quality_rejected_list').is(':checked')) {
        $('#addRowBtn').hide();
    } else {
        $('#addRowBtn').show();
    }

    // Add form submission handler
    $('#plasmaRejectionForm').on('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        // Log form data
        console.log('Form submission - Form data:', $(this).serialize());

        // Check if AR number is selected
        if (!$('#ar_number').val()) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select an A.R. Number'
            });
            return false;
        }

        // Check if at least one row has data
        let hasData = false;
        $('.donor-id').each(function() {
            if ($(this).val()) {
                hasData = true;
                return false; // Break the loop
            }
        });

        if (!hasData) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter at least one donor record'
            });
            return false;
        }

        // Get AR Number for the confirmation message
        const arNumber = $('#ar_number').val();

        // Show confirmation dialog
        Swal.fire({
            title: 'Confirm Destruction Number Generation',
            html: `<span style="font-size: 1.0em">Do you want to generate a destruction number for A.R. Number <b>${arNumber}</b>?</span>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, generate it!',
            cancelButtonText: 'No, cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    html: 'Please wait while we generate the destruction number.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit the form via AJAX
                $.ajax({
                    url: "{{ route('plasma.rejection.store') }}",
                    method: "POST",
                    data: $('#plasmaRejectionForm').serialize(),
                    success: function(response) {
                        console.log('Response:', response);

                        if (response.success) {
                            // Show success message with destruction number
                            Swal.fire({
                                icon: 'success',
                                title: `<span style="font-size: 1.0em">Rejection Number <b>${response.destruction_no}</b> has been generated successfully</span>`,
                                showConfirmButton: true,
                                allowOutsideClick: false
                            }).then((result) => {
                                // Optionally reload the page or redirect
                                // window.location.reload();
                            });
                        } else {
                            // Show error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'An error occurred while processing your request.'
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', xhr.responseText);

                        // Try to parse the error response
                        let errorMessage = 'An error occurred while processing your request.';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            errorMessage = response.message || errorMessage;
                        } catch (e) {
                            // Use default error message
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: errorMessage
                        });
                    }
                });
            }
        });
    });

    // Initialize Select2 for blood bank
    $('.select2-bloodbank').select2({
        placeholder: "Select Blood Centre",
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for AR Number dropdown
    $('#ar_number').select2({
        placeholder: "Select A.R. Number",
        allowClear: true,
        width: '100%',
        dropdownCssClass: 'select2-dropdown-large',
        theme: 'default'
    });

    // Fetch A.R. Numbers and populate dropdown
    $.ajax({
        url: '{{ route("barcode.ar-numbers") }}',
        method: 'GET',
        success: function(response) {
            if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                // Clear existing options except the first one
                $('#ar_number option:not(:first)').remove();

                // Add new options
                response.data.forEach(function(arNumber) {
                    $('#ar_number').append(new Option(arNumber, arNumber));
                });
            } else {
                console.warn('No A.R. Numbers found or invalid response format');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching A.R. Numbers:', error);
        }
    });

    // Event handler for A.R. Number selection
    $('#ar_number').on('change', function() {
        var arNumber = $(this).val();
        console.log('AR Number changed to:', arNumber);

        // Reset the Quality Rejected List checkbox
        $('#quality_rejected_list').prop('checked', false);

        if (arNumber) {
            // Fetch details based on selected A.R. Number
            $.ajax({
                url: '{{ route("plasma.ar-details") }}',
                method: 'GET',
                data: { ar_number: arNumber },
                success: function(response) {
                    console.log('AR details response:', response);

                    if (response.success) {
                        // Update the display fields with fetched data
                        $('#display_blood_centre').text(response.data.blood_centre || '');
                        $('#display_date').text(response.data.date || '');
                        $('#display_pickup_date').text(response.data.pickup_date || '');

                        // Always clear and reset table when changing AR Number
                        clearTableData();
                        $('#addRowBtn').show();

                    } else {
                        console.warn('Failed to fetch A.R. details');
                        // Clear all fields if API returns an error
                        $('#display_blood_centre').text('');
                        $('#display_date').text('');
                        $('#display_pickup_date').text('');
                        clearTableData();
                        $('#addRowBtn').show();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching A.R. details:', error);
                    console.error('Status:', status);
                    console.error('Response:', xhr.responseText);
                    // Clear all fields on error
                    $('#display_blood_centre').text('');
                    $('#display_date').text('');
                    $('#display_pickup_date').text('');
                    clearTableData();
                    $('#addRowBtn').show();
                }
            });
        } else {
            // Clear all fields if no A.R. Number is selected
            $('#display_blood_centre').text('');
            $('#display_date').text('');
            $('#display_pickup_date').text('');
            clearTableData();
            $('#addRowBtn').show();
        }
    });

    // Event handler for Quality Rejected List checkbox
    $('#quality_rejected_list').on('change', function() {
        var arNumber = $('#ar_number').val();
        var isChecked = $(this).is(':checked');
        console.log('Quality rejected checkbox changed. Checked:', isChecked, 'AR Number:', arNumber);

        if (isChecked && arNumber) {
            console.log('Loading quality rejected data for AR:', arNumber);
            loadQualityRejectedData(arNumber);
            // Hide the Add Row button
            $('#addRowBtn').hide();
        } else {
            console.log('Clearing table data');
            // Clear the table if checkbox is unchecked
            clearTableData();
            // Show the Add Row button
            $('#addRowBtn').show();
        }
    });

    // Function to load quality rejected data
    function loadQualityRejectedData(arNumber) {
        // Option to create a test entry for development
        var createTest = false; // Set to true to create a test entry

        $.ajax({
            url: '{{ route("plasma.quality-rejected") }}',
            method: 'GET',
            data: {
                ar_number: arNumber,
                create_test: createTest ? 1 : undefined
            },
            success: function(response) {
                console.log('Quality rejected response:', response);

                if (response.success && Array.isArray(response.data) && response.data.length > 0) {
                    console.log('Found quality rejected entries:', response.data.length);
                    console.log('First entry:', response.data[0]);

                    // Clear existing rows except the button row
                    $('#reportBody tr:not(#buttonRow)').remove();

                    // Add rows for each quality rejected entry
                    response.data.forEach(function(entry, index) {
                        console.log(`Processing entry ${index+1}:`, entry);
                        addEntryRow(entry, index + 1);
                    });

                    // Recalculate the total
                    calculateTotalVolume();

                    // Hide the Add Row button
                    $('#addRowBtn').hide();
                } else {
                    console.warn('No quality rejected entries found or invalid response format');
                    console.log('Response data:', response);

                    // Clear the table
                    clearTableData();

                    // Show the Add Row button
                    $('#addRowBtn').show();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching quality rejected entries:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                // Clear the table on error
                clearTableData();

                // Show the Add Row button
                $('#addRowBtn').show();
            }
        });
    }

    // Function to add a row with entry data
    function addEntryRow(entry, rowNum) {
        console.log('Adding entry row with data:', entry);

        // Format the donation date if it exists
        var donationDate = '';
        if (entry.donation_date) {
            // Try to format the date if it's a valid date string
            try {
                var date = new Date(entry.donation_date);
                if (!isNaN(date.getTime())) {
                    donationDate = date.toISOString().split('T')[0];  // Format as YYYY-MM-DD
                }
            } catch (e) {
                console.error('Error formatting date:', e);
            }
        }

        // Helper function to check if a value exists and is not null
        function getValue(val) {
            return (val !== undefined && val !== null && val !== '') ? val : '';
        }

        var newRow = `<tr>
            <td class="text-center">${rowNum}</td>
            <td>
                <input type="text" class="form-control form-control-sm donor-id" name="donor_id[]" value="${getValue(entry.mega_pool_id)}">
            </td>
            <td>
                <input type="date" class="form-control form-control-sm donation-date" name="donation_date[]" value="${donationDate}">
            </td>
            <td>
                <select class="form-control form-control-sm blood-group" name="blood_group[]">
                    <option value="">Select</option>
                    <option value="A+" ${entry.blood_group === 'A+' ? 'selected' : ''}>A+</option>
                    <option value="A-" ${entry.blood_group === 'A-' ? 'selected' : ''}>A-</option>
                    <option value="B+" ${entry.blood_group === 'B+' ? 'selected' : ''}>B+</option>
                    <option value="B-" ${entry.blood_group === 'B-' ? 'selected' : ''}>B-</option>
                    <option value="AB+" ${entry.blood_group === 'AB+' ? 'selected' : ''}>AB+</option>
                    <option value="AB-" ${entry.blood_group === 'AB-' ? 'selected' : ''}>AB-</option>
                    <option value="O+" ${entry.blood_group === 'O+' ? 'selected' : ''}>O+</option>
                    <option value="O-" ${entry.blood_group === 'O-' ? 'selected' : ''}>O-</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm bag-volume" name="bag_volume[]" value="${getValue(entry.bag_volume)}">
            </td>
            <td>
                <select class="form-control form-control-sm remarks-select" name="remarks[]">
                    <option value="">Select</option>
                    <option value="Damaged" ${entry.reject_reason === 'Damaged' ? 'selected' : ''}>Damaged</option>
                    <option value="Hemolyzed (Red)" ${entry.reject_reason === 'Hemolyzed (Red)' ? 'selected' : ''}>Hemolyzed (Red)</option>
                    <option value="Expired" ${entry.reject_reason === 'Expired' ? 'selected' : ''}>Expired</option>
                    <option value="Quality Rejected" ${entry.reject_reason === 'Quality Rejected' || entry.reject_reason === 'quality-rejected' ? 'selected' : ''}>Quality Rejected</option>
                </select>
            </td>
        </tr>`;

        // Insert the new row before the button row
        $(newRow).insertBefore('#buttonRow');
    }

    // Function to clear table data
    function clearTableData() {
        // Remove all rows except the button row
        $('#reportBody tr:not(#buttonRow)').remove();

        // Add two empty default rows
        for (var i = 0; i < 2; i++) {
            var rowNum = i + 1;
            var emptyRow = `<tr>
                <td class="text-center">${rowNum}</td>
                <td>
                    <input type="text" class="form-control form-control-sm donor-id" name="donor_id[]">
                </td>
                <td>
                    <input type="date" class="form-control form-control-sm donation-date" name="donation_date[]">
                </td>
                <td>
                    <select class="form-control form-control-sm blood-group" name="blood_group[]">
                        <option value="">Select</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control form-control-sm bag-volume" name="bag_volume[]">
                </td>
                <td>
                    <select class="form-control form-control-sm remarks-select" name="remarks[]">
                        <option value="">Select</option>
                        <option value="Damaged">Damaged</option>
                        <option value="Hemolyzed (Red)">Hemolyzed (Red)</option>
                        <option value="Expired">Expired</option>
                        <option value="Quality Rejected">Quality Rejected</option>
                    </select>
                </td>
            </tr>`;

            // Insert the empty row before the button row
            $(emptyRow).insertBefore('#buttonRow');
        }

        // Reset the total volume
        $('#totalVolume').text('0.00');
    }

    // Add Row button functionality
    $('#addRowBtn').on('click', function() {
        // Get the current number of rows
        var rowCount = $('#reportBody tr:not(#buttonRow)').length;

        // Create a new row with the next sequential number
        var newRow = `<tr>
            <td class="text-center">${rowCount + 1}</td>
            <td>
                <input type="text" class="form-control form-control-sm donor-id" name="donor_id[]">
            </td>
            <td>
                <input type="date" class="form-control form-control-sm donation-date" name="donation_date[]">
            </td>
            <td>
                <select class="form-control form-control-sm blood-group" name="blood_group[]">
                    <option value="">Select</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                </select>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm bag-volume" name="bag_volume[]">
            </td>
            <td>
                <select class="form-control form-control-sm remarks-select" name="remarks[]">
                    <option value="">Select</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Hemolyzed (Red)">Hemolyzed (Red)</option>
                    <option value="Expired">Expired</option>
                    <option value="Quality Rejected">Quality Rejected</option>
                </select>
            </td>
        </tr>`;

        // Insert the new row before the button row
        $(newRow).insertBefore('#buttonRow');

        // Recalculate total after adding row
        calculateTotalVolume();
    });

    // Calculate total volume function
    function calculateTotalVolume() {
        var total = 0;

        // Sum up all bag volumes
        $('.bag-volume').each(function() {
            var value = parseFloat($(this).val()) || 0;
            total += value;
        });

        // Update the total display (format to 2 decimal places)
        $('#totalVolume').text(total.toFixed(2));
    }

    // Recalculate total when any bag volume changes
    $(document).on('input', '.bag-volume', function() {
        calculateTotalVolume();
    });

    // Calculate initial total
    calculateTotalVolume();
});
</script>
@endpush
