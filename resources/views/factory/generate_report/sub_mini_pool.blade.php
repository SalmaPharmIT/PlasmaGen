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
    }
    .excel-like td {
        padding: 4px 8px;
        vertical-align: middle;
        position: relative;
        font-size: 0.8rem;
    }
    .excel-like td.text-center {
        padding: 8px;
        line-height: 1.2;
    }
    .table-bordered > :not(caption) > * > * {
        border-width: 1px;
    }
    /* Group of 12 styling */
    .excel-like tr:nth-child(12n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) {
        border-top: 2px solid #000;
    }

    /* Header Styles */
    .logo-cell {
        width: 200px;
        padding: 8px;
    }
    .logo-cell img {
        max-width: 180px;
        height: auto;
    }
    
    /* Column Width Styles */
    .excel-like td:nth-child(1) { width: 40px; }
    .excel-like td:nth-child(2) { width: 40px; }
    .excel-like td:nth-child(3) { width: 60px; }
    .excel-like td:nth-child(4) { width: 40px; }
    .excel-like td:nth-child(5) { width: 60px; }
    .excel-like td:nth-child(6) { width: 60px; }
    .excel-like td:nth-child(7) { width: 60px; }
    .excel-like td:nth-child(8) { width: 100px; }
    .excel-like td:nth-child(9) { width: 60px; }
    .excel-like td:nth-child(10) { width: 100px; }
    .excel-like td:nth-child(11) { width: 80px; }
    .excel-like td:nth-child(12) { width: 100px; }
    .excel-like td:nth-child(13) { width: 80px; }
    .excel-like td:nth-child(14) { width: 120px; }

    /* Select2 Custom Styles */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 22px !important;
        min-height: 22px !important;
        font-size: 0.8rem !important;
        padding: 0 !important;
        border: 1px solid #ced4da !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 22px !important;
        padding-left: 0.4rem !important;
        padding-right: 1.2rem !important;
        font-size: 0.8rem !important;
        color: #212529 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 20px !important;
        width: 20px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow b {
        margin-left: -4px !important;
    }
    .select2-dropdown {
        font-size: 0.8rem !important;
    }
    .select2-search--dropdown .select2-search__field {
        padding: 2px 4px !important;
        font-size: 0.8rem !important;
    }
    .select2-results__option {
        padding: 2px 4px !important;
        font-size: 0.8rem !important;
    }
</style>
@endpush

@section('content')
<div class="pagetitle">
    <h1>Sub Mini Pool Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Sub Mini Pool Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small mb-1">Blood Centre Name & City</label>
                                <div>
                                    <select class="form-control form-control-sm select2" name="blood_centre_id" id="blood_centre_id" required>
                                        <option value="">Select Blood Centre</option>
                                        @forelse($bloodCenters ?? [] as $center)
                                            <option value="{{ $center->id }}">{{ $center->name }} - {{ $center->city }}</option>
                                        @empty
                                            <option value="" disabled>No blood centers available</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small mb-1">Pick Date</label>
                                <input type="date" class="form-control form-control-sm" id="start_date" name="start_date">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="small mb-1">Enter Sub Mini Pool Number</label>
                                <input type="text" class="form-control form-control-sm" id="sub_mini_pool_number" name="sub_mini_pool_number" placeholder="Enter sub mini pool number">
                            </div>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm" id="generateReport">Generate Report</button>
                        </div>
                    </div>

                    <!-- Report Results Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm border-dark excel-like">
                            <thead>
                                <!-- Header Information -->
                                <tr>
                                    <td class="logo-cell" style="width: 200px;">
                                        <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo" style="max-width: 180px; height: auto;">
                                    </td>
                                    <td colspan="13" class="text-center">
                                        <h5 class="mb-0">RESAMPLING AND HANDLING OF SUB MINI POOL RECORD</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <strong>Blood Centre Name & City:</strong> <span id="display_blood_centre">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>Work Station No.:</strong> 01
                                    </td>
                                    <td colspan="4">
                                        <strong>Date:</strong> <span id="display_date">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <strong>Pickup Date:</strong> <span id="display_pickup_date">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>A.R. No.:</strong> <span id="display_ar_no">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>GRN No.:</strong> <span id="display_grn_no">-</span>
                                    </td>
                                    <td colspan="4">
                                        <strong>Mega Pool No.:</strong> <span id="display_mega_pool">-</span>
                                    </td>
                                </tr>
                                <!-- Table Column Headers -->
                                <tr>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        No. of<br>Bags
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        No. of Bags in<br>Sub Mini Pool
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Donor<br>ID
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Donation<br>Date
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Blood<br>Group
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Bag Volume<br>in ML
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Sub Mini Pool<br>Volume in Liter
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Sub Mini Pool<br>Number
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Tail<br>Cutting<br>Done
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Sub Mini Pool<br>Sample Prepared By<br>(Sign/Date)
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Test Results<br>Sub Mini Pool
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Discarded/<br>Dispensed for<br>Batch No./<br>Remarks
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="reportBody">
                                <tr>
                                    <td colspan="14" class="text-center py-3">
                                        <div class="alert alert-success mb-0">No data found</div>
                                    </td>
                                </tr>
                            </tbody>
                            <!-- Total Row -->
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-end pe-2">
                                        <span class="fw-bold">Total Volume in Liters:</span>
                                    </td>
                                    <td class="p-0">
                                        <input type="text" class="form-control form-control-sm border-0 px-1 text-end" id="totalVolume" readonly>
                                    </td>
                                    <td colspan="7"></td>
                                </tr>
                                <!-- NAT Results Row -->
                                <tr>
                                    <td colspan="4" class="align-middle">Note:Results of NAT</td>
                                    {{-- <td colspan="3" style="background-color: #f8f9fa;">
                                        <div class="d-flex align-items-center">
                                            <div class="border border-dark px-4 py-1 me-2"></div> Reactive/
                                            <div class="border border-dark px-4 py-1 mx-2"></div> Non-Reactive
                                        </div>
                                    </td> --}}
                                    <td colspan="9"></td>
                                </tr>
                                <!-- Signature Rows -->
                                <tr>
                                    <td colspan="4" style="background-color: #f8f9fa;">
                                        Entered By/ Done By<br>(WH/PPT) (Sign/ Date)
                                    </td>
                                    <td colspan="4"></td>
                                    <td colspan="3"></td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: #f8f9fa;">
                                        Reviewed By (PPT/WH)<br>(Sign/ Date)
                                    </td>
                                    <td colspan="4"></td>
                                    <td colspan="3"></td>
                                    <td colspan="3"></td>
                                </tr>
                                <tr>
                                    <td colspan="4" style="background-color: #f8f9fa;">
                                        Verified By (QA)<br>(Sign/ Date)
                                    </td>
                                    <td colspan="4"></td>
                                    <td colspan="3"></td>
                                    <td colspan="3"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            theme: 'default',
            placeholder: "Select Blood Centre",
            allowClear: true,
            width: '100%',
            minimumResultsForSearch: 10,
            dropdownAutoWidth: true
        }).on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            $('#display_blood_centre').text(selectedText || '-');
        });

        $('#generateReport').click(function() {
            const bloodCentreId = $('#blood_centre_id').val();
            const date = $('#start_date').val();
            const subMiniPoolNumber = $('#sub_mini_pool_number').val();
            
            if (!bloodCentreId || !date || !subMiniPoolNumber) {
                alert('Please fill in all fields');
                return;
            }

            // Update header displays
            $('#display_date').text(formatDate(date));
            $('#display_pickup_date').text(formatDate(date));
            $('#display_sub_mini_pool').text(subMiniPoolNumber);
            // For demo purposes, using static values for AR and GRN
            $('#display_ar_no').text('AR/RM10001/0317/24/0953');
            $('#display_grn_no').text('HP0953');

            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Here you would make an AJAX call to your backend to get the report data
            // For now, we'll simulate an API call with setTimeout
            setTimeout(function() {
                // Simulate no data found scenario
                if (subMiniPoolNumber === "TEST") {
                    $('#reportBody').html('<tr><td colspan="14" class="text-center py-3">No data found for the selected parameters</td></tr>');
                    $('#totalVolume').val('');
                } else {
                    generateDummyData();
                }
                
                // Reset button state
                $('#generateReport').prop('disabled', false).text('Generate Report');
            }, 1000);
        });

        function generateDummyData() {
            let html = '';
            for (let i = 1; i <= 72; i++) {
                html += `
                    <tr>
                        <td class="p-0 text-center">${i}</td>
                        <td class="p-0 text-center">${((i - 1) % 12) + 1}</td>
                        <td class="p-0 text-center">DONOR${i}</td>
                        <td class="p-0 text-center">2024-03-${String(i % 28 + 1).padStart(2, '0')}</td>
                        <td class="p-0 text-center">A+</td>
                        <td class="p-0 text-center">250</td>
                        ${(i - 1) % 12 === 0 ? `
                        <td class="p-0 text-center" rowspan="12">3.00</td>
                        <td class="p-0 text-center" rowspan="12">SMP${Math.floor((i-1)/12 + 1)}</td>
                        <td class="p-0 text-center" rowspan="12">Yes</td>
                        <td class="p-0 text-center" rowspan="12"></td>
                        <td class="p-0 text-center" rowspan="12"></td>
                        <td class="p-0 text-center" rowspan="12"></td>
                        <td class="p-0 text-center" rowspan="12"></td>
                        <td class="p-0 text-center" rowspan="12"></td>
                        ` : ''}
                    </tr>
                `;
            }
            
            $('#reportBody').html(html);
            $('#totalVolume').val('18.00'); // 6 groups * 3.00L
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB');
        }
    });
</script>
@endsection 