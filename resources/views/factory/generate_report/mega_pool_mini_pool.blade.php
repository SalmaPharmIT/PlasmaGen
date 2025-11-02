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
    .excel-like td:nth-child(8) { width: 80px; }
    .excel-like td:nth-child(9) { width: 100px; }
    .excel-like td:nth-child(10) { width: 60px; }
    .excel-like td:nth-child(11) { width: 100px; }
    .excel-like td:nth-child(12) { width: 80px; }
    .excel-like td:nth-child(13) { width: 100px; }
    .excel-like td:nth-child(14) { width: 80px; }
    .excel-like td:nth-child(15) { width: 120px; }

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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small mb-1">Select AR No.</label>
                                <select class="form-control form-control-sm select2" id="ar_number" name="ar_number">
                                    <option value="">Select AR No.</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-primary btn-sm" id="generateReport">Generate Report</button>
                            <button type="button" class="btn btn-success btn-sm ms-2" id="printReport" disabled><i class="bi bi-printer"></i> Print</button>
                            <button type="button" class="btn btn-info btn-sm ms-2" id="exportExcel" disabled><i class="bi bi-file-excel"></i> Export Excel</button>
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
                                    <td colspan="4">
                                        <strong>Blood Centre Name & City: <span class="alert alert-success text-dark" id="display_blood_centre" style="padding:5px; margin-bottom :2px;">-</span></strong>
                                    </td>
                                    {{-- <td colspan="3">
                                        <strong>Work Station No.:</strong> <span class="alert alert-success text-dark" id="display_work_station_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td> --}}
                                    <td colspan="3">
                                        <strong>Pickup Date:</strong> <span class="alert alert-success text-dark" id="display_pickup_date" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>A.R. No.:</strong> <span class="alert alert-success text-dark" id="display_ar_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    <td colspan="3">
                                        <strong>GRN No.:</strong> <span class="alert alert-success text-dark" id="display_grn_no" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td>
                                    {{-- <td colspan="4">
                                        <strong>Date:</strong> <span class="alert alert-success text-dark" id="display_date" style="padding:5px; margin-bottom :2px;">-</span>
                                    </td> --}}
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
                                        Mega Pool<br>Number
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
                                        Test Results<br>Mega Pool
                                    </td>
                                </tr>
                            </thead>
                            <tbody id="reportBody">
                                <tr>
                                    <td colspan="13" class="text-center py-3">
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
                                        Approved By (QA)<br>(Sign/ Date)
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
<!-- SheetJS library for Excel export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<!-- FileSaver.js for saving files -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>

    $(document).ready(function() {
        // Initialize select2
        $('#ar_number').select2({
            ajax: {
                url: '{{ route("factory.generate_report.fetch_ar_numbers") }}',
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.items
                    };
                },
                cache: true
            },
            placeholder: 'Select AR No.'
        });

        // Bind click event using event delegation
        $(document).on('click', '#generateReport', function(e) {
            e.preventDefault();
            console.log('Generate Report button clicked');

            const arNumber = $('#ar_number').val();

            console.log('Form Values:', {
                arNumber
            });

            if (!arNumber) {
                alert('Please select AR number');
                return;
            }

            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Make AJAX call to fetch data
            $.ajax({
                url: '{{ route("factory.generate_report.fetch_mega_pool_data") }}',
                method: 'POST',
                data: {
                    ar_number: arNumber,
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

                        // Generate table rows
                        let html = '';
                        let miniPoolRowspan = 0;
                        let details = response.data.details;

                        // First pass: count rows per mini pool and mega pool
                        let miniPoolCounts = {};
                        let megaPoolCounts = {};

                        details.forEach(detail => {
                            miniPoolCounts[detail.mini_pool_number] = (miniPoolCounts[detail.mini_pool_number] || 0) + 1;
                            megaPoolCounts[detail.mega_pool_no] = (megaPoolCounts[detail.mega_pool_no] || 0) + 1;
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

                            // Check if this is the first row of a new mini pool
                            const isNewMiniPool = (index === 0 || details[index - 1].mini_pool_number !== detail.mini_pool_number);

                            // Check if this is the first row of a new mega pool
                            const isNewMegaPool = (index === 0 || details[index - 1].mega_pool_no !== detail.mega_pool_no);

                            // Add mini pool bag volume (rowspan per mini pool)
                            if (isNewMiniPool) {
                                miniPoolRowspan = miniPoolCounts[detail.mini_pool_number];
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_bag_volume}</td>`;
                            }

                            // Add mega pool number (rowspan per mega pool)
                            if (isNewMegaPool) {
                                html += `<td class="p-0 text-center" rowspan="${megaPoolCounts[detail.mega_pool_no]}">${detail.mega_pool_no}</td>`;
                            }

                            // Add mini pool specific cells (rowspan per mini pool)
                            if (isNewMiniPool) {
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_number}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.tail_cutting}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.prepared_by}</td>`;
                                html += `<td class="p-0 text-center" rowspan="${miniPoolRowspan}">${detail.mini_pool_test_result}</td>`;
                            }

                            // Add mega pool test result (rowspan per mega pool)
                            if (isNewMegaPool) {
                                html += `<td class="p-0 text-center" rowspan="${megaPoolCounts[detail.mega_pool_no]}">${detail.mega_pool_test_result}</td>`;
                            }

                            html += '</tr>';
                        });

                        $('#reportBody').html(html);
                        $('#totalVolume').val(response.data.total_volume);

                        // Enable print and export buttons
                        $('#printReport, #exportExcel').prop('disabled', false);
                    } else {
                        $('#reportBody').html(`
                            <tr>
                                <td colspan="13" class="text-center py-3">
                                    <div class="alert alert-warning mb-0">${response.message || 'No data found'}</div>
                                </td>
                            </tr>
                        `);
                        $('#totalVolume').val('');

                        // Disable print and export buttons
                        $('#printReport, #exportExcel').prop('disabled', true);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    $('#reportBody').html(`
                        <tr>
                            <td colspan="13" class="text-center py-3">
                                <div class="alert alert-danger mb-0">Error fetching data. Please try again.</div>
                            </td>
                        </tr>
                    `);
                    $('#totalVolume').val('');

                    // Disable print and export buttons
                    $('#printReport, #exportExcel').prop('disabled', true);
                },
                complete: function() {
                    // Reset button state
                    $('#generateReport').prop('disabled', false).text('Generate Report');
                }
            });
        });

        // Print functionality
        $(document).on('click', '#printReport', function() {
            const arNumber = $('#ar_number').val();

            // Create a form to submit to the print route
            const form = $('<form>', {
                'method': 'POST',
                'action': '{{ route("factory.generate_report.print_mega_pool_report") }}',
                'target': '_blank'
            });

            // Add CSRF token
            form.append($('<input>', {
                'type': 'hidden',
                'name': '_token',
                'value': '{{ csrf_token() }}'
            }));

            // Add AR number
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'ar_number',
                'value': arNumber
            }));

            // Append form to body, submit it, and remove it
            $('body').append(form);
            form.submit();
            form.remove();
        });

        // Excel export functionality
        $(document).on('click', '#exportExcel', function() {
            const arNumber = $('#ar_number').val();
            const fileName = 'Mega_Pool_Mini_Pool_Report_' + arNumber + '.xlsx';

            // Create workbook and worksheet
            let wb = XLSX.utils.book_new();

            // Get header information
            const headerData = [
                ['Plasma Mini Pool and Mega Pool Handling Record'],
                [''],
                ['Blood Centre Name & City:', $('#display_blood_centre').text()],
                ['Pickup Date:', $('#display_pickup_date').text()],
                ['A.R. No.:', $('#display_ar_no').text()],
                ['GRN No.:', $('#display_grn_no').text()],
                [''],
            ];

                                // Column headers
                    const columnHeaders = [
                        'No. of Bags', 'No. of Bags in Mini Pool', 'Donor ID', 'Donation Date',
                        'Blood Group', 'Bag Volume in ML', 'Mini Pool Bag Volume in Liter',
                        'Mega Pool Number', 'Mini Pool Number / Segment No.', 'Tail Cutting Done',
                        'Mini Pool Sample Prepared By (Sign/Date)', 'Test Results Mini Pool',
                        'Test Results Mega Pool'
                    ];

            headerData.push(columnHeaders);

                                    // Extract data from table and handle merged cells
                        const tableData = [];
                        const mergedCells = []; // Track merged cells for Excel

                        // First pass: collect all data and track merged cells
                        $('#reportBody tr').each(function(rowIndex) {
                            const rowData = [];
                            $(this).find('td').each(function(colIndex) {
                                const cellText = $(this).text().trim();
                                const rowspan = parseInt($(this).attr('rowspan')) || 1;

                                if (rowspan > 1) {
                                    // Add merge range info
                                    mergedCells.push({
                                        s: {r: rowIndex + headerData.length, c: colIndex},
                                        e: {r: rowIndex + headerData.length + rowspan - 1, c: colIndex}
                                    });
                                }

                                // Skip empty cells (those that are part of a rowspan from above)
                                if ($(this).is(':empty')) {
                                    // Find the cell above that contains this data
                                    let aboveRow = rowIndex - 1;
                                    while (aboveRow >= 0) {
                                        const aboveCell = $('#reportBody tr').eq(aboveRow).find('td').eq(colIndex);
                                        if (aboveCell.length && !aboveCell.is(':empty')) {
                                            rowData.push(aboveCell.text().trim());
                                            break;
                                        }
                                        aboveRow--;
                                    }
                                } else {
                                    rowData.push(cellText);
                                }
                            });
                            tableData.push(rowData);
            });

            // Combine all data
            const allData = [...headerData, ...tableData];

            // Add footer data
            allData.push(['']);
            allData.push(['Total Volume in Liters:', $('#totalVolume').val()]);
            allData.push(['']);
            allData.push(['Entered By/ Done By (WH/PPT) (Sign/ Date)', '', '', '']);
            allData.push(['Reviewed By (PPT/WH) (Sign/ Date)', '', '', '']);
            allData.push(['Approved By (QA) (Sign/ Date)', '', '', '']);

            // Create worksheet and add to workbook
            const ws = XLSX.utils.aoa_to_sheet(allData);

            // Set column widths
            const colWidths = [
                { wch: 10 }, { wch: 15 }, { wch: 15 }, { wch: 15 },
                { wch: 10 }, { wch: 15 }, { wch: 20 }, { wch: 20 },
                { wch: 25 }, { wch: 15 }, { wch: 25 }, { wch: 20 },
                { wch: 20 }
            ];
            ws['!cols'] = colWidths;

            // Add merge cells configuration
            ws['!merges'] = mergedCells;

            // Add worksheet to workbook
            XLSX.utils.book_append_sheet(wb, ws, 'Report');

            // Generate Excel file and trigger download
            XLSX.writeFile(wb, fileName);
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
