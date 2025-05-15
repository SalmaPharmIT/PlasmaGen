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
    /* Group of 3 styling */
    .excel-like tr:nth-child(3n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(3n+1) {
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
    /* .excel-like td:nth-child(13) { width: 80px; } */
    .excel-like td:nth-child(14) { width: 120px; }

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
</style>
@endpush

@section('content')

<div class="pagetitle">
    <h1>Sub Mini Pool Entry</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Sub Mini Pool Entry</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('sub-mini-pool-entries.store') }}" id="subMiniPoolForm">
                        @csrf
                        <!-- Search Form -->
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Mini Pool Number</label>
                                    <select class="form-select select2" id="mini_pool_id" name="mini_pool_id">
                                        <option value="">Select Mini Pool Number</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden inputs for form data -->
                        <input type="hidden" name="mega_pool_no" id="mega_pool_no">
                        <input type="hidden" name="mini_pool_number" id="mini_pool_number">
                        <input type="hidden" name="sub_mini_pool_no" id="sub_mini_pool_no">
                        <input type="hidden" name="timestamp" id="timestamp">

                        <!-- Report Results Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm border-dark excel-like">
                                <thead>
                                    <!-- Header Information -->
                                    {{-- <tr>
                                        <td class="logo-cell" style="width: 100px;">
                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo" style="max-width: 180px; height: auto;">
                                        </td>
                                        <td colspan="10" class="text-center">
                                            <h5 class="mb-0">RESAMPLING AND HANDLING OF SUB MINI POOL RECORD</h5>
                                        </td>
                                    </tr> --}}
                                    <tr>
                                        <td colspan="6">
                                            <strong>Blood Centre Name & City:</strong> <span id="display_blood_centre">-</span>
                                        </td>
                                        <td colspan="3">
                                            <strong>Work Station No.:</strong> <span id="work_station_no">-</span>
                                        </td>
                                        
                                    </tr>
                                    <tr>
                                        <td colspan="1">
                                            <strong>Pickup Date:</strong> <span id="display_pickup_date">-</span>
                                        </td>
                                        <td colspan="2">
                                            <strong>A.R. No.:</strong> <span id="display_ar_no">-</span>
                                        </td>
                                        <td colspan="2">
                                            <strong>GRN No.:</strong> <span id="display_grn_no">-</span>
                                        </td>
                                        <td colspan="2">
                                            <strong>Mega Pool No.:</strong> <span id="display_mega_pool">-</span>
                                        </td>
                                        <td colspan="2">
                                            <strong>Mini Pool No.:</strong> <span id="display_mini_pool">-</span>
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
                                            Sub Mini Pool<br>Number
                                        </td>
                                        <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                            Tail<br>Cutting<br>Done
                                        </td>
                                        {{-- <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                            Sub Mini Pool<br>Sample Prepared By<br>(Sign/Date)
                                        </td>
                                        <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                            Test Results<br>Sub Mini Pool
                                        </td>
                                        <td class="text-center" style="background-color: transparent; font-weight: 500;">
                                            Discarded/<br>Dispensed for<br>Batch No./<br>Remarks
                                        </td> --}}
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
                                {{-- <tfoot>
                                    <tr>
                                        <td colspan="6" class="text-end pe-2">
                                            <span class="fw-bold">Total Volume in Liters:</span>
                                        </td>
                                        <td class="p-0">
                                            <input type="text" class="form-control form-control-sm border-0 px-1 text-end" id="totalVolume" readonly>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr> --}}
                                    <!-- NAT Results Row -->
                                    {{-- <tr>
                                        <td colspan="4" class="align-middle">Note:Results of NAT</td>
                                        <td colspan="3" style="background-color: #f8f9fa;">
                                            <div class="d-flex align-items-center">
                                                <div class="border border-dark px-4 py-1 me-2"></div> Reactive/
                                                <div class="border border-dark px-4 py-1 mx-2"></div> Non-Reactive
                                            </div>
                                        </td>
                                        <td colspan="5"></td>
                                    </tr> --}}
                                    <!-- Signature Rows -->
                                    {{-- <tr>
                                        <td colspan="3" style="background-color: #f8f9fa;">
                                            Entered By/ Done By<br>(WH/PPT) (Sign/ Date)
                                        </td>
                                        <td colspan="6"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="background-color: #f8f9fa;">
                                            Reviewed By (PPT/WH)<br>(Sign/ Date)
                                        </td>
                                        <td colspan="6"></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" style="background-color: #f8f9fa;">
                                            Verified By (QA)<br>(Sign/ Date)
                                        </td>
                                        <td colspan="6"></td>
                                    </tr> --}}
                                </tfoot>
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
    // Wrap everything in a try-catch to catch any errors
    try {
        
        $(document).ready(function() {
                        
            // Initialize Select2
            $('#mini_pool_id').select2({
                placeholder: "Search Mini Pool Number",
                allowClear: true,
                width: '100%'
            });

            // Fetch reactive mini pools
            $.ajax({
                url: '{{ route("factory.report.get-reactive-minipools") }}',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log('AJAX Response received:', response);
                    if (Array.isArray(response) && response.length > 0) {
                        response.forEach(function(pool) {
                            $('#mini_pool_id').append(new Option(pool.text, pool.id));
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching mini pools:', error);
                }
            });

            // Handle selection change
            $('#mini_pool_id').on('change', function() {
                const selectedText = $(this).find('option:selected').text();
                const selectedValue = $(this).val();
                $('#display_mini_pool').text(selectedText || '-');
                $('#mini_pool_number').val(selectedText);

                if (selectedValue) {
                    // Fetch mini pool data
                    $.ajax({
                        url: `/factory/report/get-minipool-data/${selectedValue}`,
                        method: 'GET',
                        success: function(response) {
                            console.log('Mini pool data response:', response);
                            if (response.success) {
                                // Update display fields
                                $('#display_mega_pool').text(response.data.mega_pool_no || '-');
                                $('#mega_pool_no').val(response.data.mega_pool_no);
                                $('#display_ar_no').text(response.data.ar_no || '-');
                                $('#display_pickup_date').text(response.data.pickup_date || '-');
                                $('#display_date').text(response.data.display_date || '-');
                                $('#display_blood_centre').text(response.data.blood_bank_name || '-');
                                $('#work_station_no').text(response.data.work_station || '-');
                                $('#display_grn_no').text(response.data.ref_doc_no || '-');
                                $('#totalVolume').val(response.data.total_volume || '0.00');
                                
                                // Update table with donor details
                                if (response.data.details && response.data.details.length > 0) {
                                    let html = '';
                                    let currentSubMiniPool = null;
                                    let subMiniPoolRowspan = 0;
                                    let subMiniPoolCounts = {};
                                    let subMiniPoolNumbers = new Set();

                                    // Count rows per sub-mini pool
                                    response.data.details.forEach(detail => {
                                        subMiniPoolCounts[detail.sub_mini_pool_number] = (subMiniPoolCounts[detail.sub_mini_pool_number] || 0) + 1;
                                    });

                                    response.data.details.forEach((detail, index) => {
                                        html += '<tr>';
                                        html += `<td class="p-0 text-center">${detail.row_number}</td>`;
                                        html += `<td class="p-0 text-center">${detail.bags_in_mini_pool}</td>`;
                                        html += `<td class="p-0 text-center">${detail.donor_id}</td>`;
                                        html += `<td class="p-0 text-center">${detail.donation_date}</td>`;
                                        html += `<td class="p-0 text-center">${detail.blood_group}</td>`;
                                        html += `<td class="p-0 text-center">${detail.bag_volume_ml}</td>`;

                                        // If this is the first row of a sub-mini pool, add the rowspan cells
                                        if (currentSubMiniPool !== detail.sub_mini_pool_number) {
                                            currentSubMiniPool = detail.sub_mini_pool_number;
                                            subMiniPoolRowspan = subMiniPoolCounts[detail.sub_mini_pool_number];
                                            
                                            html += `<td class="p-0 text-center" rowspan="${subMiniPoolRowspan}">${detail.sub_mini_pool_number}</td>`;
                                            html += `<td class="p-0 text-center" rowspan="${subMiniPoolRowspan}">Yes</td>`;
                                        }
                                        html += '</tr>';

                                        if (detail.sub_mini_pool_number) {
                                            subMiniPoolNumbers.add(detail.sub_mini_pool_number);
                                        }
                                    });

                                    // Set the sub mini pool numbers as comma-separated string
                                    $('#sub_mini_pool_no').val(Array.from(subMiniPoolNumbers).join(','));
                                    $('#timestamp').val(new Date().toISOString());

                                    $('#reportBody').html(html);
                                } else {
                                    $('#reportBody').html('<tr><td colspan="14" class="text-center py-3"><div class="alert alert-success mb-0">No data found</div></td></tr>');
                                }
                            } else {
                                console.log('No data found in response');
                                // Clear display fields if no data found
                                clearDisplayFields();
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching mini pool data:', error);
                            console.error('Response:', xhr.responseText);
                            // Clear display fields on error
                            clearDisplayFields();
                        }
                    });
                } else {
                    // Clear display fields if no mini pool selected
                    clearDisplayFields();
                }
            });

            function clearDisplayFields() {
                $('#display_mega_pool').text('-');
                $('#display_ar_no').text('-');
                $('#display_pickup_date').text('-');
                $('#display_date').text('-');
                $('#display_blood_centre').text('-');
                $('#work_station_no').text('-');
                $('#display_grn_no').text('-');
                $('#reportBody').html('<tr><td colspan="14" class="text-center py-3"><div class="alert alert-success mb-0">No data found</div></td></tr>');
            }

            // Form submission
            $('#subMiniPoolForm').on('submit', function(e) {
                e.preventDefault();
                
                // Validate required fields
                if (!$('#mini_pool_id').val()) {
                    alert('Please select a Mini Pool Number');
                    return false;
                }

                // Submit the form
                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        console.log('Response:', response); // Debug log
                        if (response && response.status === 'success') {
                            alert(response.message || 'Data saved successfully!');
                            // Optionally refresh the page or clear the form
                            location.reload();
                        } else {
                            alert(response.message || 'Error saving data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error details:', {xhr, status, error}); // Debug log
                        let errorMessage = 'Error saving data. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        
                        alert(errorMessage);
                    }
                });
            });
        });
    } catch (error) {
        console.error('Global error caught:', error);
    }
</script>
@endpush 