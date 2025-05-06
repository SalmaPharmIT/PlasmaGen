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
    .form-group label.small {
        font-size: 0.8rem !important;
        margin-bottom: 0.1rem !important;
    }

    .report-header {
        border: 1px solid #000;
        margin-bottom: 1rem;
    }
    .report-header table {
        width: 100%;
        margin-bottom: 0;
    }
    .report-header td {
        padding: 4px 8px;
        border: 1px solid #000;
    }
    .report-title {
        text-align: center;
        font-weight: bold;
        font-size: 1.1rem;
        padding: 8px;
        border-bottom: 1px solid #000;
    }
    .header-label {
        font-weight: 500;
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<div class="pagetitle">
    <h1>Mega Pool Mini Pool Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Mega Pool Mini Pool Report</li>
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
                                    <select class="form-control-sm select2-bloodbank" name="blood_bank[]" data-placeholder="Select Blood Centre">
                                        <option></option>
                                        @foreach($bloodCenters as $center)
                                            <option value="{{ $center['id'] }}">{{ $center['text'] }}</option>
                                        @endforeach
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
                                <label class="small mb-1">Enter Mega Pool Number</label>
                                <input type="text" class="form-control form-control-sm" id="mega_pool_number" name="mega_pool_number" placeholder="Enter mega pool number">
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
                                        <h5 class="mb-0">Plasma Mini Pool and Mega Pool Handling Record</h5>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7">
                                        <strong>Blood Centre Name & City: <span class="alert alert-success text-dark" id="display_blood_centre" style="padding:5px; margin-bottom :2px;">-</span></strong>
                                    </td>
                                    <td colspan="3">
                                        <strong>Work Station No.:</strong> <span class="alert alert-success text-dark" id="display_work_station_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="4">
                                        <strong>Date:</strong> <span class="alert alert-success text-dark" id="display_date" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <strong>Pickup Date:</strong> <span class="alert alert-success text-dark" id="display_pickup_date" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="4">
                                        <strong>A.R. No.:</strong> <span class="alert alert-success text-dark" id="display_ar_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>GRN No.:</strong> <span class="alert alert-success text-dark" id="display_grn_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="4">
                                        <strong>Mega Pool No.:</strong> <span class="alert alert-success text-dark" id="display_mega_pool" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                </tr>
                                <!-- Table Column Headers -->
                                <tr class="alert alert-danger text-dark">
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        No. of<br>Bags
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        No. of Bags in<br>Mini Pool
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
                                        Mini Pool Bag<br>Volume in Liter
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Mini Pool Number /<br>Segment No.
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Tail<br>Cutting<br>Done
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Mini Pool Sample<br>Prepared By<br>(Sign/Date)
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Test Results<br>Mini Pool
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Mega Pool Sample<br>Prepared By<br>(Sign/Date)
                                    </td>
                                    <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                        Test Results<br>Mega Pool
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
                                {{-- <tr>
                                    <td colspan="2" class="align-middle">Note:Results of NAT-</td>
                                    <td colspan="3" style="background-color: #f8f9fa;">
                                        <div class="d-flex align-items-center">
                                            <div class="border border-dark px-4 py-1 me-2"></div> Reactive/
                                            <div class="border border-dark px-4 py-1 mx-2"></div> Non-Reactive
                                        </div>
                                    </td>
                                    <td colspan="9"></td>
                                </tr> --}}
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

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    
    $(document).ready(function() {
        
        // Initialize Select2
        $('.select2-bloodbank').select2({
            theme: 'default',
            width: '100%',
            placeholder: "Select Blood Centre",
            allowClear: true,
            dropdownParent: $('body'),
            minimumInputLength: 0
        }).on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            $('#display_blood_centre').text(selectedText || '-');
        });

        // Bind click event using event delegation
        $(document).on('click', '#generateReport', function(e) {
            e.preventDefault();
            console.log('Generate Report button clicked');
            
            const bloodBankId = $('.select2-bloodbank').val();
            const pickupDate = $('#start_date').val();
            const megaPoolNumber = $('#mega_pool_number').val();

            console.log('Form Values:', {
                bloodBankId,
                pickupDate,
                megaPoolNumber
            });
            
            if (!bloodBankId || !pickupDate || !megaPoolNumber) {
                alert('Please fill in all fields');
                return;
            }

            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Make AJAX call to fetch data
            $.ajax({
                url: '{{ route("factory.generate_report.fetch_mega_pool_data") }}',
                method: 'POST',
                data: {
                    blood_bank_id: bloodBankId,
                    pickup_date: pickupDate,
                    mega_pool_number: megaPoolNumber,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('AJAX Success:', response);
                    if (response.success) {
                        // Update header information
                        $('#display_blood_centre').text(response.data.header.blood_centre);
                        $('#display_date').text(response.data.header.date);
                        $('#display_pickup_date').text(response.data.header.pickup_date);
                        $('#display_work_station_no').text(response.data.header.work_station_no);
                        $('#display_ar_no').text(response.data.header.ar_no);
                        $('#display_grn_no').text(response.data.header.grn_no);
                        $('#display_mega_pool').text(response.data.header.mega_pool);

                        // Generate table rows
                        let html = '';
                        let currentMiniPool = null;
                        let miniPoolRowspan = 0;
                        let details = response.data.details;

                        // First pass: count rows per mini pool
                        let miniPoolCounts = {};
                        details.forEach(detail => {
                            miniPoolCounts[detail.mini_pool_number] = (miniPoolCounts[detail.mini_pool_number] || 0) + 1;
                        });

                        // Second pass: generate HTML
                        details.forEach((detail, index) => {
                            html += '<tr>';
                            html += `<td class="p-0 text-center">${detail.row_number}</td>`;
                            html += `<td class="p-0 text-center">${detail.bags_in_mini_pool}</td>`;
                            html += `<td class="p-0 text-center">${detail.donor_id}</td>`;
                            html += `<td class="p-0 text-center">${detail.donation_date}</td>`;
                            html += `<td class="p-0 text-center">${detail.blood_group}</td>`;
                            html += `<td class="p-0 text-center">${detail.bag_volume_ml}</td>`;

                            // If this is the first row of a mini pool, add the rowspan cells
                            if (currentMiniPool !== detail.mini_pool_number) {
                                currentMiniPool = detail.mini_pool_number;
                                miniPoolRowspan = miniPoolCounts[detail.mini_pool_number];
                                
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_bag_volume}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_number}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.tail_cutting}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.prepared_by}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_test_result}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.prepared_by}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mega_pool_test_result}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}"></td>`;
                            }

                            html += '</tr>';
                        });

                        $('#reportBody').html(html);
                        $('#totalVolume').val(response.data.total_volume);
                    } else {
                        $('#reportBody').html(`
                            <tr>
                                <td colspan="14" class="text-center py-3">
                                    <div class="alert alert-warning mb-0">${response.message || 'No data found'}</div>
                                </td>
                            </tr>
                        `);
                        $('#totalVolume').val('');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    $('#reportBody').html(`
                        <tr>
                            <td colspan="14" class="text-center py-3">
                                <div class="alert alert-danger mb-0">Error fetching data. Please try again.</div>
                            </td>
                        </tr>
                    `);
                    $('#totalVolume').val('');
                },
                complete: function() {
                    // Reset button state
                    $('#generateReport').prop('disabled', false).text('Generate Report');
                }
            });
        });

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-GB');
        }

        // Log that all event handlers are bound
        console.log('All event handlers bound');
    });
</script>
@endpush 