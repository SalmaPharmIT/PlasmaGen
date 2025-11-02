@extends('include.dashboardLayout')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    <h1>Plasma Rejection</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Plasma Rejection</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('nat.plasma.rejection.store') }}" id="plasmaRejectionForm">
                        @csrf
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
                        </div>

                        <!-- Report Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark header-table">
                                <tr>
                                    <td class="logo-cell">
                                        <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo">
                                    </td>
                                    <td colspan="5" class="text-center">
                                        <h5 class="mb-0">PLASMA REJECTION RECORD</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <strong>Blood Centre Name & City:</strong> <span id="display_blood_centre"></span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark data-table">
                                <thead>
                                    <tr>
                                        <td class="text-center sr-no-col">Sr.No.</td>
                                        <td class="text-center donor-id-col">Mini Pool ID / Mega Pool ID</td>
                                        <td class="text-center blood-group-col">Status</td>
                                        <td class="text-center remarks-col">Remarks</td>
                                    </tr>
                                </thead>
                                <tbody id="reportBody">
                                    <tr>
                                        <td colspan="6" class="text-center py-3">
                                            <div class="alert alert-info mb-0">Select Blood Centre and Pickup Date to view data</div>
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
<script>
$(document).ready(function() {
    // Set up CSRF token for all AJAX requests
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Initialize Select2 for AR Number
    $('#ar_number').select2({
        placeholder: "Select A.R. Number",
        allowClear: true,
        width: '100%',
        ajax: {
            url: '{{ route("get.ar.numbers") }}',
            dataType: 'json',
            delay: 250,
            processResults: function(data) {
                return {
                    results: $.map(data, function(item) {
                        return {
                            text: item.ar_no,
                            id: item.ar_no
                        }
                    })
                };
            },
            cache: true
        }
    });

    // Handle AR Number selection
    $('#ar_number').on('change', function() {
        const arNumber = $(this).val();

        if (!arNumber) {
            $('#reportBody').html('<tr><td colspan="6" class="text-center py-3"><div class="alert alert-info mb-0">Select A.R. Number to view data</div></td></tr>');
            return;
        }

        // Show loading state
        $('#reportBody').html('<tr><td colspan="6" class="text-center py-3"><div class="alert alert-info mb-0">Loading...</div></td></tr>');

        // Make AJAX call to get data
        $.ajax({
            url: '{{ route("plasma.rejection.details") }}',
            method: 'GET',
            data: {
                ar_number: arNumber
            },
            success: function(response) {
                if (response.status === 'success' && response.data.length > 0) {
                    // Display blood centre and pickup date
                    $('#display_blood_centre').text(response.blood_centre || '-');
                    $('#display_pickup_date').text(response.pickup_date || '-');
                    $('#display_date').text(new Date().toLocaleDateString('en-GB'));

                    let html = '';
                    response.data.forEach((item, index) => {
                        html += `<tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${item.mini_pool_id || '-'}</td>
                            <td class="text-center">${item.status || 'Unknown'}</td>
                            <td class="text-center">
                                <select class="form-control form-control-sm remarks-select" name="remarks[${item.id}]">
                                    <option value="">Select Remarks</option>
                                    <option value="re-test">Re-test</option>
                                    <option value="quality-rejected">Quality Rejected</option>
                                </select>
                            </td>
                            <input type="hidden" name="item_ids[]" value="${item.id}">
                            <input type="hidden" name="source_types[${item.id}]" value="${item.source_type || 'unknown'}">
                            <input type="hidden" name="mini_pool_ids[${item.id}]" value="${item.mini_pool_id || ''}">
                        </tr>`;
                    });
                    $('#reportBody').html(html);
                } else {
                    $('#reportBody').html('<tr><td colspan="6" class="text-center py-3"><div class="alert alert-info mb-0">No data found</div></td></tr>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                $('#reportBody').html('<tr><td colspan="6" class="text-center py-3"><div class="alert alert-danger mb-0">Error loading data</div></td></tr>');
            }
        });
    });

    // Handle rejection type checkboxes
    $('.rejection-type').on('change', function() {
        let selectedTypes = [];
        $('.rejection-type:checked').each(function() {
            selectedTypes.push($(this).val());
        });
    });

    // Add event listener for remarks header dropdown
    $('#remarksHeader').on('change', function() {
        const selectedValue = $(this).val();
        if (selectedValue) {
            $('.remarks-select').val(selectedValue);
        }
    });

    // Update the form submission code to collect remarks from dropdowns
    $('#plasmaRejectionForm').on('submit', function(e) {
        e.preventDefault();

        const arNumber = $('#ar_number').val();

        if (!arNumber) {
            alert('Please select an A.R. Number');
            return;
        }

        // Collect items data
        const items = [];
        let hasRemarks = false;

        $('input[name="item_ids[]"]').each(function(index) {
            const itemId = $(this).val();
            const remarks = $(`select[name="remarks[${itemId}]"]`).val();
            const sourceType = $(`input[name="source_types[${itemId}]"]`).val();
            const miniPoolId = $(`input[name="mini_pool_ids[${itemId}]"]`).val();

            if (remarks) {
                hasRemarks = true;
            }

            items.push({
                id: itemId,
                remarks: remarks,
                source_type: sourceType,
                mini_pool_id: miniPoolId
            });
        });

        if (!hasRemarks) {
            alert('Please add remarks for at least one item');
            return;
        }

        if (items.length === 0) {
            alert('No items found to submit');
            return;
        }

        // Show loading state
        Swal.fire({
            title: 'Saving...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Prepare data for submission as JSON
        const submissionData = {
            ar_number: arNumber,
            items: items,
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        // Submit form data
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: JSON.stringify(submissionData),
            contentType: 'application/json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'An error occurred while saving the data'
                });
            }
        });
    });
});
</script>
@endpush
