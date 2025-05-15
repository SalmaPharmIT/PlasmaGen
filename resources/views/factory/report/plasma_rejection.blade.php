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
</style>
@endpush

@section('content')

<div class="pagetitle">
    <h1>Plasma Rejection Sheet</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Plasma Rejection Sheet</li>
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
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small mb-1">Blood Centre Name & City</label>
                                    <div>
                                        <select class="form-control-sm select2-bloodbank" name="blood_bank" id="blood_bank" data-placeholder="Select Blood Centre">
                                            <option></option>
                                            @foreach($bloodCenters as $center)
                                                <option value="{{ $center['id'] }}">{{ $center['text'] }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="small mb-1">Pick Date</label>
                                    <input type="date" class="form-control form-control" id="pickup_date" name="pickup_date">
                                </div>
                            </div>
                            <div class="col-md-2">
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label class="small mb-1">&nbsp;</label>
                                    <button type="button" class="btn btn-primary w-100" id="searchBtn">Get Data</button>
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
                                    <td colspan="5" class="text-center">
                                        <h5 class="mb-0">PLASMA REJECTION RECORD</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <strong>Type of Rejection:</strong> 
                                        <div class="d-inline-block ms-2">
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input rejection-type" type="checkbox" value="damage" id="rejection_damaged">
                                                <label class="form-check-label" for="rejection_damaged">Damaged</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input rejection-type" type="checkbox" value="rejection" id="rejection_hemolyzed">
                                                <label class="form-check-label" for="rejection_hemolyzed">Hemolyzed(Red)</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input rejection-type" type="checkbox" value="expiry" id="rejection_expiry">
                                                <label class="form-check-label" for="rejection_expiry">Expiry</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input rejection-type" type="checkbox" value="quality" id="rejection_quality">
                                                <label class="form-check-label" for="rejection_quality">Quality Rejected</label>
                                            </div>
                                        </div>
                                    </td>
                                    <td colspan="2">
                                        <strong>Date:</strong> <span id="display_date"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <strong>Blood Centre Name & City:</strong> <span id="display_blood_centre"></span>
                                    </td>
                                    <td colspan="2">
                                        <strong>Pickup Date:</strong> <span id="display_pickup_date"></span>
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
                                        <td class="text-center bag-volume-col">Volume</td>
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

    // Initialize Select2 for blood bank
    $('.select2-bloodbank').select2({
        placeholder: "Select Blood Centre",
        allowClear: true,
        width: '100%'
    });

    // Handle blood bank selection
    $('#blood_bank').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        $('#display_blood_centre').text(selectedText || '-');
    });

    // Handle pickup date selection
    $('#pickup_date').on('change', function() {
        const selectedDate = $(this).val();
        if (selectedDate) {
            const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB', {
                day: '2-digit',
                month: '2-digit',
                year: 'numeric'
            });
            $('#display_pickup_date').text(formattedDate);
            $('#display_date').text(formattedDate);
        } else {
            $('#display_pickup_date').text('-');
            $('#display_date').text('-');
        }
    });

    // Handle search button click
    $('#searchBtn').on('click', function() {
        const bloodBank = $('#blood_bank').val();
        const pickupDate = $('#pickup_date').val();

        if (!bloodBank) {
            alert('Please select a Blood Centre');
            return;
        }

        if (!pickupDate) {
            alert('Please select a Pickup Date');
            return;
        }

        // Show loading state
        $('#reportBody').html('<tr><td colspan="6" class="text-center py-3"><div class="alert alert-info mb-0">Loading...</div></td></tr>');

        // Make AJAX call to get data
        $.ajax({
            url: '{{ route("mini.pool.details") }}',
            method: 'GET',
            data: {
                blood_centre_name: bloodBank,
                pickup_date: pickupDate
            },
            success: function(response) {
            console.log("response");
            console.log(response);
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((item, index) => {
                        const testResults = [];
                        if (item.hiv === 'reactive') testResults.push('HIV');
                        if (item.hbv === 'reactive') testResults.push('HBV');
                        if (item.hcv === 'reactive') testResults.push('HCV');
                        
                        html += `<tr>
                            <td class="text-center">${index + 1}</td>
                            <td class="text-center">${item.mini_pool_id || '-'}</td>
                            <td class="text-center">${item.donation_date || '-'}</td>
                            <td class="text-center">${item.blood_group || '-'}</td>
                            <td class="text-center">${item.bag_volume_ml || '-'}</td>
                            <td contenteditable="true" class="remarks-cell" data-mini-pool-id="${item.mini_pool_id}" style="min-width: 150px;"></td>
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

    // Handle form submission
    $('#plasmaRejectionForm').on('submit', function(e) {
        e.preventDefault();

        const bloodBank = $('#blood_bank').val();
        const pickupDate = $('#pickup_date').val();
        const selectedTypes = [];
        $('.rejection-type:checked').each(function() {
            selectedTypes.push($(this).val());
        });

        if (!bloodBank) {
            alert('Please select a Blood Centre');
            return;
        }

        if (!pickupDate) {
            alert('Please select a Pickup Date');
            return;
        }

        if (selectedTypes.length === 0) {
            alert('Please select at least one rejection type');
            return;
        }

        // Collect mini pool data
        const miniPools = [];
        $('#reportBody tr').each(function() {
            const miniPoolId = $(this).find('td[data-mini-pool-id]').data('mini-pool-id');
            const remarks = $(this).find('.remarks-cell').text().trim();
            
            if (miniPoolId) {
                miniPools.push({
                    mini_pool_id: miniPoolId,
                    remarks: remarks
                });
            }
        });

        if (miniPools.length === 0) {
            alert('No mini pools found to submit');
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

        // Submit form data
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: {
                blood_bank: bloodBank,
                pickup_date: pickupDate,
                rejection_type: selectedTypes,
                mini_pools: miniPools
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