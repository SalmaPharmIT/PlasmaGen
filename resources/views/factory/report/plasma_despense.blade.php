@extends('include.dashboardLayout')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    .excel-like .bag-volume-col { width: 80px !important; min-width: 80px !important; max-width: 80px !important; }
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
    .data-table .bag-volume-col { width: 80px !important; min-width: 80px !important; max-width: 80px !important; }
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

    /* Mega pool item styles */
    .mega-pool-item {
        padding: 3px;
        font-size: 0.8rem;
    }
    .mega-pool-item:hover {
        background-color: #f8f9fa;
    }
    hr.my-1 {
        margin-top: 0.25rem;
        margin-bottom: 0.25rem;
        border: 0;
        border-top: 1px dashed #dee2e6;
    }

    /* Additional Select2 styling */
    .select2-dropdown-large {
        min-width: 300px !important;
    }
    .select2-search__field {
        width: 100% !important;
    }
    .select2-results__option {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .select2-container .select2-selection--single {
        padding: 4px;
    }
</style>
@endpush

@section('content')

<div class="pagetitle">
    <h1>Plasma Despense</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Plasma Despense</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('plasma.despense.store') }}" id="plasmaDespenseForm">
                        @csrf
                        <div class="row g-2 mb-3">
                            <div class="col-md-3">
                                <label for="batch_no" class="form-label">
                                    <i class="bi bi-hash me-1"></i>Batch No
                                </label>
                                <input type="text" class="form-control" id="batch_no" name="batch_no" required>
                                <div class="invalid-feedback">Please enter Batch No.</div>
                            </div>
                            <div class="col-md-3">
                                <label for="date" class="form-label">
                                    Date
                                </label>
                                <input type="date" class="form-control" id="date" name="date" required>
                                <div class="invalid-feedback">Please enter Date.</div>
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
                                        <h5 class="mb-0">PLASMA DESPENSE RECORD</h5>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark data-table">
                                <thead>
                                    <tr>
                                        <td class="text-center sr-no-col" rowspan="2">Sr.No.</td>
                                        <td class="text-center blood-group-col" rowspan="2">AR No</td>
                                        <td class="text-center donor-id-col" rowspan="2">DonorID / Mini Pool ID / Mega Pool ID</td>
                                        <td class="text-center" style="width:160px !important; max-width:160px !important;" colspan="2"><small>Volume in Ltrs</small></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" style="width:80px !important; max-width:80px !important;">Requested</td>
                                        <td class="text-center" style="width:80px !important; max-width:80px !important;">Issued</td>
                                    </tr>
                                </thead>
                                <tbody id="reportBody">
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td class="text-center">
                                            <select class="form-select form-select-sm ar-number-select" name="ar_number">
                                                <option value="">Select A.R. Number</option>
                                            </select>
                                        </td>
                                        <td class="text-center mini-pool-id">-</td>
                                        <td class="text-center requested-volume">-</td>
                                        <td class="text-center">
                                            <div class="d-flex">
                                                <input type="text" class="form-control form-control-sm issued-volume me-1" name="issued_volume[]" value="">
                                                <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-2">
                            <div class="col-12 text-end">
                                <button type="button" id="saveAsDraft" class="btn btn-secondary btn-sm">Save as Draft</button>
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
<script>
// Define logDebug function to prevent errors
function logDebug() {
    // Check if we're in development environment (can customize this check)
    const isDev = {{ config('app.debug') ? 'true' : 'false' }};
    if (isDev && console && console.log) {
        console.log.apply(console, arguments);
    }
}

$(document).ready(function() {
    // Add CSRF token to all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Function to merge cells with same value in the requested volume column
    function mergeRepeatedCells() {
        const table = document.querySelector('.data-table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        if (!tbody) return;

        const rows = tbody.querySelectorAll('tr');
        if (rows.length <= 1) return;

        let currentValue = null;
        let startRow = null;
        let rowspan = 1;

        // Process the requested volume column (4th column, index 3)
        for (let i = 0; i < rows.length; i++) {
            const cell = rows[i].cells[3]; // Requested volume cell
            if (!cell) continue;

            // Skip the total row
            if (rows[i].classList.contains('table-secondary')) continue;

            const cellValue = cell.textContent.trim();

            if (currentValue === null) {
                // First row
                currentValue = cellValue;
                startRow = i;
            } else if (cellValue === currentValue) {
                // Same value as previous row
                rowspan++;
                cell.style.display = 'none'; // Hide this cell
            } else {
                // Different value than previous row
                if (rowspan > 1) {
                    // Apply rowspan to the first cell of the group
                    rows[startRow].cells[3].rowSpan = rowspan;
                }

                // Reset for next group
                currentValue = cellValue;
                startRow = i;
                rowspan = 1;
            }
        }

        // Handle the last group
        if (rowspan > 1) {
            rows[startRow].cells[3].rowSpan = rowspan;
        }
    }

    // Function to calculate and update totals
    function calculateTotals() {
        let totalRequested = 0;
        let totalIssued = 0;

        // Get all regular rows (excluding the total row)
        const rows = $('#reportBody tr:not(.table-secondary)');

        // Use a Set to track unique requested values after merging
        const requestedValues = new Set();

        rows.each(function() {
            // Only count cells that are visible (not merged/hidden)
            const requestedCell = $(this).find('td:eq(3)');
            if (requestedCell.is(':visible')) {
                const requestedText = requestedCell.text().trim();
                const requested = parseFloat(requestedText) || 0;

                // If this cell has rowspan, multiply by the rowspan value
                const rowspan = requestedCell.attr('rowspan') ? parseInt(requestedCell.attr('rowspan')) : 1;

                // We only add each unique value once
                if (!requestedValues.has(requestedText)) {
                    totalRequested += requested;
                    requestedValues.add(requestedText);
                }
            }

            // Get issued volume from input
            const issuedText = $(this).find('.issued-volume').val();
            const issued = parseFloat(issuedText) || 0;

            // Add to issued total
            totalIssued += issued;
        });

        // Update total cells
        $('#total-requested').text(totalRequested.toFixed(2));
        $('#total-issued').text(totalIssued.toFixed(2));
    }

    // Initialize Select2 for blood bank
    $('.select2-bloodbank').select2({
        placeholder: "Select Blood Centre",
        allowClear: true,
        width: '100%'
    });

    // Initialize Select2 for AR Number dropdown in the table
    function initializeArNumberSelect() {
        $('.ar-number-select').select2({
            placeholder: "Select A.R. Number",
            allowClear: true,
            width: '100%',
            dropdownCssClass: 'select2-dropdown-large',
            theme: 'default'
        });
    }

    // Initialize the first row's AR number select
    initializeArNumberSelect();

        // Function to add a new row
    function addNewRow() {
        const rowCount = $('#reportBody tr:not(.total-row)').length;

        // Create the basic row structure (not part of any group yet)
        let newRow = `<tr>
            <td class="text-center">${rowCount + 1}</td>
            <td class="text-center">
                <select class="form-select form-select-sm ar-number-select" name="ar_number">
                    <option value="">Select A.R. Number</option>
                </select>
            </td>
            <td class="text-center mini-pool-id">-</td>
            <td class="text-center requested-volume">-</td>
            <td class="text-center">
                <div class="d-flex">
                    <input type="text" class="form-control form-control-sm issued-volume me-1" name="issued_volume[]" value="">
                    <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                </div>
            </td>
        </tr>`;

        // Add row before the total row if it exists, otherwise append to the table
        if ($('#reportBody tr.total-row').length) {
            $('#reportBody tr.total-row').before(newRow);
        } else {
            $('#reportBody').append(newRow);
        }

        // Get the newly added row's select element
        const newSelect = $('#reportBody tr:not(.total-row)').eq(rowCount).find('.ar-number-select');

        // Add AR number options if available
        if (window.arNumbersData && Array.isArray(window.arNumbersData)) {
            window.arNumbersData.forEach(arNumber => {
                newSelect.append(new Option(arNumber, arNumber));
            });
        }

        // Initialize the new select2
        initializeArNumberSelect();

        // Attach event handlers to the new row
        attachArNumberChangeHandler();

        // Update row numbers for all rows
        updateRowNumbers();
    }

    // Function to update row numbers
    function updateRowNumbers() {
        $('#reportBody tr:not(.total-row)').each(function(index) {
            $(this).find('td:first').text(index + 1);
        });
    }

    // Add a button to add new rows - place it right after the table
    $('.table-responsive').last().after('<div class="row mt-2"><div class="col-12"><button type="button" id="addNewRow" class="btn btn-info btn-sm"><i class="bi bi-plus-circle"></i> Add Row</button></div></div>');

    // Handle add row button click
    $('#addNewRow').on('click', function() {
        addNewRow();
    });

        // Handle remove row button click using event delegation
    $(document).on('click', '.remove-row', function() {
        const currentRow = $(this).closest('tr');
        const arNo = currentRow.data('ar-no') || currentRow.find('select.ar-number-select').val();
        const isGrouped = currentRow.find('td[rowspan]').length > 0;

        // Don't remove if it's the only row
        if ($('#reportBody tr:not(.total-row)').length <= 1) {
            Swal.fire({
                title: 'Cannot Remove',
                text: 'You must have at least one row in the table',
                icon: 'warning',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0c4c90'
            });
            return;
        }

        // If this is part of a group
        if (arNo) {
            const groupRows = $(`#reportBody tr.ar-group[data-ar-no="${arNo}"]`);

            // If this is the first row in a group with rowspan
            if (isGrouped && groupRows.length > 1) {
                // Get the next row in the group
                const nextRow = currentRow.next(`tr.ar-group[data-ar-no="${arNo}"]`);

                if (nextRow.length > 0) {
                    // Move the AR number select to the next row
                    const arSelect = currentRow.find('select.ar-number-select').clone();
                    nextRow.find('td:first').after($('<td class="text-center"></td>').append(arSelect));

                    // Update rowspan on the next row
                    nextRow.find('td:eq(1)').attr('rowspan', groupRows.length - 1);

                    // Initialize select2 on the moved select
                    initializeArNumberSelect();
                }

                // Remove the current row
                currentRow.remove();
            } else if (groupRows.length > 1) {
                // If this is not the first row in a group, just remove it
                currentRow.remove();

                // Update rowspan on the first row
                const firstRow = $(`#reportBody tr.ar-group[data-ar-no="${arNo}"]:first`);
                const newRowspan = $(`#reportBody tr.ar-group[data-ar-no="${arNo}"]`).length;
                firstRow.find('td[rowspan]').attr('rowspan', newRowspan);
            } else {
                // If this is the only row in a group, just remove it
                currentRow.remove();
            }
        } else {
            // Regular row, just remove it
            currentRow.remove();
        }

        // Update row numbers
        updateRowNumbers();

        // Recalculate totals
        calculateTotals();
    });

        // Function to handle AR number selection change
    function handleArNumberChange(element) {
        const arNo = $(element).val();
        const row = $(element).closest('tr');

        if (!arNo) {
            row.find('.mini-pool-id').text('-');
            row.find('.requested-volume').text('-');
            row.find('.issued-volume').val('');
            return;
        }

        // Show loading state
        row.find('.mini-pool-id').text('Loading...');
        row.find('.requested-volume').text('Loading...');

        // Make AJAX call to get data
        $.ajax({
            url: '{{ route("mini.pool.nonreactive.details") }}',
            method: 'GET',
            data: {
                ar_no: arNo,
            },
            success: function(response) {
                console.log('Response from server:', response);
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    // Display blood bank name if available
                    if (response.blood_bank_name) {
                        $('#display_blood_centre').text(response.blood_bank_name);
                    }

                    // If this is a draft, populate the batch_no and date fields
                    if (response.is_draft && response.batch_no) {
                        $('#batch_no').val(response.batch_no);
                    }

                    if (response.is_draft && response.date) {
                        $('#date').val(response.date);
                    }

                    // Get the current row index
                    const currentRowIndex = $('#reportBody tr:not(.total-row)').index(row);

                                        // We'll handle all the data in the code below

                                        // First, remove any existing rows with the same AR number (except the current row)
                    $('#reportBody tr:not(.total-row)').each(function() {
                        const rowArNo = $(this).find('.ar-number-select').val();
                        // Skip the current row and rows with different AR numbers
                        if ($(this).is(row) || rowArNo !== arNo) {
                            return;
                        }
                        // Remove the row
                        $(this).remove();
                    });

                                        // Remove the current row since we'll create a new structure
                    const rowIndex = $('#reportBody tr:not(.total-row)').index(row);
                    row.remove();

                    // Create a new row for each mega pool but with rowspan for AR Number
                    response.data.forEach((item, index) => {
                        const megaPoolId = item.mini_pool_id || item.mega_pool_no || '-';
                        const requestedVolume = item.volume_in_ltrs || '0.00';
                        const issuedValue = response.is_draft && item.issued_volume ? item.issued_volume : '';

                        let newRow = '';

                        if (index === 0) {
                            // First row has the AR number with rowspan
                            newRow = `<tr class="ar-group" data-ar-no="${arNo}">
                                <td class="text-center">${rowIndex + 1}</td>
                                <td class="text-center" rowspan="${response.data.length}">
                                    <select class="form-select form-select-sm ar-number-select" name="ar_number">
                                        <option value="${arNo}" selected>${arNo}</option>
                                    </select>
                                    <input type="hidden" name="ar_number" value="${arNo}">
                                </td>
                                <td class="text-center mini-pool-id" data-mini-pool-id="${megaPoolId}">${megaPoolId}</td>
                                <td class="text-center requested-volume" data-requested="${requestedVolume}">${requestedVolume}</td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <input type="text" class="form-control form-control-sm issued-volume me-1" name="issued_volume[]" value="${issuedValue}">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        } else {
                            // Subsequent rows don't have the AR number cell
                            newRow = `<tr class="ar-group" data-ar-no="${arNo}">
                                <td class="text-center">${rowIndex + index + 1}</td>
                                <td class="text-center mini-pool-id" data-mini-pool-id="${megaPoolId}">${megaPoolId}</td>
                                <td class="text-center requested-volume" data-requested="${requestedVolume}">${requestedVolume}</td>
                                <td class="text-center">
                                    <div class="d-flex">
                                        <input type="text" class="form-control form-control-sm issued-volume me-1" name="issued_volume[]" value="${issuedValue}">
                                        <button type="button" class="btn btn-danger btn-sm remove-row"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>`;
                        }

                        // Insert the row at the appropriate position
                        if (rowIndex === 0) {
                            // If it's the first row, prepend to the table body
                            if (index === 0) {
                                $('#reportBody').prepend(newRow);
                            } else {
                                // Add after the previous row
                                $(`#reportBody tr.ar-group[data-ar-no="${arNo}"]:eq(${index-1})`).after(newRow);
                            }
                        } else {
                            // If not the first row, insert after the previous row or at the correct position
                            if (index === 0) {
                                // Find the row at rowIndex-1 and insert after it
                                $('#reportBody tr:not(.total-row)').eq(rowIndex-1).after(newRow);
                            } else {
                                // Add after the previous row in this group
                                $(`#reportBody tr.ar-group[data-ar-no="${arNo}"]:eq(${index-1})`).after(newRow);
                            }
                        }
                    });

                    // Calculate totals
                    calculateTotals();

                    // Show appropriate message if this is a draft
                    if (response.is_draft) {
                        Swal.fire({
                            title: 'Draft Found',
                            text: 'A draft for this AR number has been loaded. You can continue editing and save as draft again or submit the final version.',
                            icon: 'info',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#0c4c90'
                        });
                    }
                } else {
                    row.find('.mini-pool-id').text('-');
                    row.find('.requested-volume').text('-');
                    row.find('.issued-volume').val('');

                    Swal.fire({
                        title: 'No Data',
                        text: 'No data found for this AR number',
                        icon: 'info',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0c4c90'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                row.find('.mini-pool-id').text('-');
                row.find('.requested-volume').text('-');

                Swal.fire({
                    title: 'Error',
                    text: 'Error loading data: ' + (xhr.responseJSON?.message || error),
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

    // Function to attach AR number change handler to all AR number selects
    function attachArNumberChangeHandler() {
        $('.ar-number-select').off('change').on('change', function() {
            handleArNumberChange(this);
        });
    }

    // Initial attachment of handlers
    attachArNumberChangeHandler();

    // Handle despense type checkboxes
    $('.despense-type').on('change', function() {
        let selectedTypes = [];
        $('.despense-type:checked').each(function() {
            selectedTypes.push($(this).val());
        });
    });

    // Handle form submission
    $('#plasmaDespenseForm').on('submit', function(e) {
        e.preventDefault();
        submitForm(false);
    });

    // Handle save as draft button
    $('#saveAsDraft').on('click', function() {
        submitForm(true);
    });

    // Common function to submit the form as draft or final
    function submitForm(isDraft) {
        const batch_no = $('#batch_no').val();
        const date = $('#date').val();

        // Basic validation
        if (!batch_no) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter Batch No',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        if (!date) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please enter Date',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Check if at least one AR number is selected
        const hasArNumber = $('#reportBody select.ar-number-select').filter(function() {
            return $(this).val() !== '';
        }).length > 0;

        if (!hasArNumber) {
            Swal.fire({
                title: 'Validation Error',
                text: 'Please select at least one AR Number',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // Collect all mini pool data
        const miniPools = [];
                        // First, identify all AR number groups in the table
        const arGroups = {};

        // Find all rows with AR number selects (first row in each group)
        $('#reportBody tr:not(.total-row)').each(function() {
            const arSelect = $(this).find('select.ar-number-select');
            if (arSelect.length > 0) {
                const arNo = arSelect.val();
                if (arNo) {
                    // This is a first row in a group
                    const rowspan = $(this).find('td[rowspan]').attr('rowspan') || 1;
                    arGroups[arNo] = {
                        firstRow: $(this),
                        rowspan: parseInt(rowspan),
                        rows: [$(this)]
                    };
                }
            }
        });

        // Find all rows that belong to each AR group
        $('#reportBody tr.ar-group').each(function() {
            const arNo = $(this).data('ar-no');
            if (arNo && arGroups[arNo] && !$(this).is(arGroups[arNo].firstRow)) {
                arGroups[arNo].rows.push($(this));
            }
        });

        console.log('AR Groups identified:', arGroups);

        // Now process each group
        Object.keys(arGroups).forEach(arNo => {
            const group = arGroups[arNo];

            // Process each row in this AR group
            group.rows.forEach(row => {
                const miniPoolId = row.find('td.mini-pool-id').text().trim();
                const requestedVolume = row.find('td.requested-volume').text().trim();
                const issuedVolume = row.find('.issued-volume').val();
                const remarks = row.find('.remarks-cell').text().trim();

                            // Only add if it has a valid mini pool ID
                if (miniPoolId && miniPoolId !== '-' && miniPoolId !== 'Loading...') {
                    // Debug logging to check what's being collected
                    console.log('Adding mini pool:', {
                        ar_no: arNo,
                        mini_pool_id: miniPoolId,
                        requested_volume: requestedVolume,
                        issued_volume: issuedVolume
                    });

                    miniPools.push({
                        ar_no: arNo,
                        mini_pool_id: miniPoolId,
                        requested_volume: requestedVolume !== '-' ? requestedVolume : '',
                        issued_volume: issuedVolume || '',
                        remarks: remarks
                    });
                }
            });
        });

        if (miniPools.length === 0) {
            Swal.fire({
                title: 'Validation Error',
                text: 'No valid data found to save',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

                // Group mini pools by AR number
        const poolsByAr = {};
        miniPools.forEach(pool => {
            if (!poolsByAr[pool.ar_no]) {
                poolsByAr[pool.ar_no] = [];
            }
            poolsByAr[pool.ar_no].push(pool);
        });

        console.log('Mini pools grouped by AR:', poolsByAr);

        // Process each AR number separately
        const arNumbers = Object.keys(poolsByAr);
        let completedRequests = 0;
        let successCount = 0;
        let errorMessages = [];

        // Show loading state
        const submitBtn = isDraft ? $('#saveAsDraft') : $('button[type="submit"]');
        const originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text(isDraft ? 'Saving Draft...' : 'Submitting...');

        // Function to handle completion of all requests
        function handleAllRequestsComplete() {
            if (successCount === arNumbers.length) {
                // All requests succeeded
                Swal.fire({
                    title: isDraft ? 'Draft Saved' : 'Submission Complete',
                    text: 'All AR numbers processed successfully',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                }).then((result) => {
                    // Reload the page after the user clicks OK
                    if (result.isConfirmed) {
                        window.location.reload();
                    }
                });
            } else {
                // Some requests failed
                Swal.fire({
                    title: 'Error',
                    text: 'Error saving plasma despense records: ' + errorMessages.join('; '),
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            }

            // Re-enable the button
            submitBtn.prop('disabled', false).text(originalText);
        }

        // Process each AR number in sequence
        arNumbers.forEach(arNo => {
            console.log('Processing AR number:', arNo);

            // Get mini pools for this AR number only
            const arPools = poolsByAr[arNo];

            // Submit data for this AR number
            $.ajax({
                url: $('#plasmaDespenseForm').attr('action'),
                method: 'POST',
                data: {
                    ar_no: arNo, // Use this specific AR number
                    batch_no: batch_no,
                    date: date,
                    mini_pools: arPools, // Only the mini pools for this AR number
                    total_requested: $('#total-requested').text(),
                    total_issued: $('#total-issued').text(),
                    is_draft: isDraft ? 1 : 0,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    console.log(`Success for AR ${arNo}:`, response);
                    successCount++;
                    completedRequests++;

                    if (completedRequests === arNumbers.length) {
                        handleAllRequestsComplete();
                    }
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON?.message || 'Unknown error';
                    console.error(`Error for AR ${arNo}:`, errorMsg);
                    errorMessages.push(`AR ${arNo}: ${errorMsg}`);
                    completedRequests++;

                    if (completedRequests === arNumbers.length) {
                        handleAllRequestsComplete();
                    }
                }
            });
        });
    }

    // Function to fetch and populate AR numbers for all selects
    function fetchAndPopulateArNumbers() {
        logDebug('Fetching A.R. Numbers...');
        fetch('{{ route("barcode.ar-numbers") }}')
            .then(response => {
                logDebug('A.R. Numbers response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                logDebug('A.R. Numbers data received:', data);
                if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                    // Store AR numbers for future use when adding new rows
                    window.arNumbersData = data.data;

                    // Update all existing AR number selects
                    $('.ar-number-select').each(function() {
                        const select = $(this);
                        // Clear existing options except the first one
                        select.find('option:not(:first)').remove();

                        // Add new options
                        data.data.forEach(arNumber => {
                            select.append(new Option(arNumber, arNumber));
                        });
                    });

                    // Refresh Select2 to show new options
                    $('.ar-number-select').trigger('change');

                    logDebug('A.R. Numbers dropdowns populated with', data.data.length, 'options');
                } else {
                    console.warn('No A.R. Numbers found or invalid response format');
                }
            })
            .catch(error => {
                console.error('Error fetching A.R. Numbers:', error);
                // Show error to user
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load A.R. Numbers: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
            });
    }

    // Call the function to fetch and populate AR numbers
    fetchAndPopulateArNumbers();
});
</script>
@endpush
