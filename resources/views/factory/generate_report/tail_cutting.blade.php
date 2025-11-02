@extends('include.dashboardLayout')

@push('styles')
<!-- Add SheetJS for Excel export -->
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<!-- Add SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Add Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<!-- Add Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card {
        margin: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .form-control-sm, .form-select-sm {
        height: 32px; /* Increased from 22px for better Firefox compatibility */
        padding: 0.25rem 0.5rem; /* Increased padding */
        font-size: 0.8rem;
        width: 100%;
        border-radius: 4px;
    }
    .table.excel-like {
        margin-bottom: 0;
        width: 100%;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        border-collapse: collapse;
    }
    .excel-like td {
        padding: 8px 10px; /* Increased padding */
        vertical-align: middle;
        position: relative;
        font-size: 0.85rem;
        transition: background-color 0.2s;
        height: 40px; /* Fixed height for table cells */
    }
    .excel-like thead th {
        position: sticky;
        top: 0;
        background-color: #f6f9ff;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        padding: 12px 10px; /* Increased padding */
        vertical-align: middle;
        z-index: 5;
        height: 46px; /* Fixed height for header cells */
    }
    .excel-like tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
    }
    .excel-like td.text-center {
        padding: 8px;
        line-height: 1.2;
    }
    .table-bordered {
        border: 1px solid #dee2e6;
    }
    .table-bordered > :not(caption) > * > * {
        border-width: 1px;
        border-color: #dee2e6;
    }
    .table-bordered > thead > tr > th {
        border-bottom-width: 2px;
        /* border-color: #012970; */
    }
    /* Ensure consistent rendering across browsers */
    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin; /* Firefox scrollbar */
    }
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background-color: #adb5bd;
        border-radius: 4px;
    }
    .form-group label.small {
        font-size: 0.8rem !important;
        margin-bottom: 0.1rem !important;
        font-weight: 500;
        color: #495057;
    }
    .btn-sm {
        padding: 0.25rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 4px;
        font-weight: 500;
        height: 32px; /* Fixed height for buttons */
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .input-group-sm > .btn {
        height: 32px; /* Match form-control-sm height */
    }
    .pagination .page-link {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
        height: 32px; /* Fixed height for pagination links */
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pagination .active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
    #reportBody tr:nth-child(even) {
        background-color: rgba(0, 0, 0, 0.02);
    }
    /* Fixed layout for more consistent rendering */
    #reportTable {
        table-layout: fixed;
    }
    #reportTable th:nth-child(1) { width: 5%; }
    #reportTable th:nth-child(2) { width: 12%; }
    #reportTable th:nth-child(3) { width: 12%; }
    #reportTable th:nth-child(4) { width: 12%; }
    #reportTable th:nth-child(5) { width: 12%; }
    #reportTable th:nth-child(6) { width: 10%; }
    #reportTable th:nth-child(7) { width: 12%; }
    #reportTable th:nth-child(8) { width: 12%; }
    #reportTable th:nth-child(9) { width: 13%; }

    /* Better support for Firefox */
    @-moz-document url-prefix() {
        .form-control-sm, .form-select-sm {
            padding-top: 0.2rem;
            padding-bottom: 0.2rem;
        }
        .table-responsive {
            scrollbar-width: thin;
            scrollbar-color: #adb5bd transparent;
        }
    }

    /* Print styles */
    @media print {
        .no-print {
            display: none !important;
        }
        .table {
            width: 100% !important;
        }
        .table td, .table th {
            padding: 4px !important;
            font-size: 12px !important;
        }
    }
    /* Select2 Custom Styles */
    .select2-container {
        width: 100% !important;
    }
    .select2-container .select2-selection--single {
        height: 32px !important;
        min-height: 32px !important;
        font-size: 0.8rem !important;
        padding: 0 !important;
        border: 1px solid #ced4da !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 0.4rem !important;
        padding-right: 1.2rem !important;
        font-size: 0.8rem !important;
        color: #212529 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 30px !important;
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
    <h1>Tail Cutting Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Tail Cutting Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Search Form -->
                    <div class="row g-2 mb-3 no-print">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small mb-1">Select AR No.</label>
                                <div class="input-group input-group-sm">
                                    <select class="form-control form-control-sm select2" id="ar_number" name="ar_number">
                                        <option value="">Select AR No.</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2" style="margin-top: 25px;">
                            <button type="button" class="btn btn-primary" id="generateReport">
                                <i class="bi bi-search"></i> Generate
                            </button>
                        </div>
                        <div class="col-md-6 d-flex align-items-end justify-content-end gap-2">
                            <button type="button" class="btn btn-success btn-sm" id="exportExcel">
                                <i class="bi bi-file-excel"></i> Export Excel
                            </button>
                            <button type="button" class="btn btn-info btn-sm" id="printReport">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>

                    <!-- Report Results Card -->
                    <div class="card mb-0 border">
                        {{-- <div class="card-header bg-light py-2 d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0 text-primary">
                                <i class="bi bi-table"></i> Tail Cutting Report Data
                            </h5>
                            <span class="badge bg-primary" id="recordCount">0 Records</span>
                        </div> --}}
                        <div class="card-body p-0">
                            <!-- Report Results Table -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm border-dark excel-like" id="reportTable">
                                    <thead>
                                        <tr>
                                            <th class="text-center">No.</th>
                                            <th class="text-center">Mega Pool No.</th>
                                            <th class="text-center">Mini Pool No.</th>
                                            <th class="text-center">Donor ID</th>
                                            <th class="text-center">Donation Date</th>
                                            <th class="text-center">Blood Group</th>
                                            <th class="text-center">Bag Volume (ML)</th>
                                            <th class="text-center">Tail Cutting</th>
                                            <th class="text-center">Prepared By</th>
                                        </tr>
                                    </thead>
                                    <tbody id="reportBody">
                                        <tr>
                                            <td colspan="9" class="text-center py-3">
                                                <div class="alert alert-info mb-0">Enter mega pool number and click Generate to view data</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                                            <td colspan="6" class="text-end" style="padding: 10px;">
                                                <strong>Total Volume in Liters:</strong>
                                            </td>
                                            <td class="text-center" id="totalVolume" style="padding: 10px;">
                                                0.00
                                            </td>
                                            <td colspan="2"></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <!-- Pagination -->
                        <div class="card-footer bg-white d-flex justify-content-between align-items-center py-2 no-print">
                            <div class="dataTables_info small text-muted" id="paginationInfo">
                                Showing 0 to 0 of 0 entries
                            </div>
                            <div class="dataTables_paginate paging_simple_numbers" id="pagination">
                                <!-- Pagination will be inserted here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<!-- Add Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        let currentPage = 1;
        const perPage = 80;
        let totalRecords = 0;
        let allData = []; // Store all data for Excel export

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
            currentPage = 1;
            fetchData();
        });

        // Print Report
        $('#printReport').click(function() {
            const arNumber = $('#ar_number').val();
            if (!arNumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Required',
                    text: 'Please select AR number',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Create a hidden iframe
            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            // Load the print template in the iframe
            iframe.src = `{{ route('factory.generate_report.tail_cutting.print') }}?ar_number=${arNumber}`;

            // When the iframe loads, trigger print
            iframe.onload = function() {
                try {
                    iframe.contentWindow.print();
                } catch (e) {
                    console.error('Print failed:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Print Failed',
                        text: 'Please try again',
                        confirmButtonColor: '#3085d6'
                    });
                }
                // Remove the iframe after printing
                setTimeout(() => {
                    document.body.removeChild(iframe);
                }, 1000);
            };
        });

        // Export to Excel
        $('#exportExcel').click(function() {
            if (allData.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Data',
                    text: 'No data to export',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Calculate total volume
            let totalVolumeML = 0;
            allData.forEach(item => {
                totalVolumeML += parseFloat(item.bag_volume_ml) || 0;
            });
            const totalVolumeLiters = (totalVolumeML / 1000).toFixed(2);

            // Prepare data for Excel
            const excelData = allData.map((item, index) => ({
                'No.': index + 1,
                'Mega Pool No.': item.mega_pool_no,
                'Mini Pool No.': item.mini_pool_number,
                'Donor ID': item.donor_id,
                'Donation Date': item.donation_date,
                'Blood Group': item.blood_group,
                'Bag Volume (ML)': item.bag_volume_ml,
                'Tail Cutting': item.tail_cutting,
                'Prepared By': item.prepared_by
            }));

            // Add total row
            excelData.push({
                'No.': '',
                'Mega Pool No.': '',
                'Mini Pool No.': '',
                'Donor ID': '',
                'Donation Date': '',
                'Blood Group': 'Total Volume in Liters:',
                'Bag Volume (ML)': totalVolumeLiters,
                'Tail Cutting': '',
                'Prepared By': ''
            });

            // Create worksheet
            const ws = XLSX.utils.json_to_sheet(excelData);

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Tail Cutting Report");

            // Generate Excel file
            const arNumber = $('#ar_number').val();
            XLSX.writeFile(wb, `Tail_Cutting_Report_${arNumber}.xlsx`);
        });

        function fetchData() {
            const arNumber = $('#ar_number').val();

            if (!arNumber) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Input Required',
                    text: 'Please select AR number',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            // Show loading state
            $('#generateReport').prop('disabled', true)
                .html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');

            // Make AJAX call to fetch data
            $.ajax({
                url: '{{ route("factory.generate_report.tail_cutting.fetch") }}',
                method: 'POST',
                data: {
                    ar_number: arNumber,
                    page: currentPage,
                    per_page: perPage,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        totalRecords = response.total;
                        allData = response.data; // Store all data for Excel export
                        updateTable(response.data);
                        updatePagination();
                        // Update record count badge
                        $('#recordCount').text(`${totalRecords} Records`);
                    } else {
                        $('#reportBody').html(`
                            <tr>
                                <td colspan="9" class="text-center py-3">
                                    <div class="alert alert-warning mb-0">${response.message || 'No data found'}</div>
                                </td>
                            </tr>
                        `);
                        updatePaginationInfo(0, 0, 0);
                        $('#recordCount').text('0 Records');
                        $('#totalVolume').text('0.00');
                        allData = []; // Clear data
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    $('#reportBody').html(`
                        <tr>
                            <td colspan="9" class="text-center py-3">
                                <div class="alert alert-danger mb-0">Error fetching data. Please try again.</div>
                            </td>
                        </tr>
                    `);
                    updatePaginationInfo(0, 0, 0);
                    $('#recordCount').text('0 Records');
                    $('#totalVolume').text('0.00');
                    allData = []; // Clear data
                },
                complete: function() {
                    // Reset button state
                    $('#generateReport').prop('disabled', false).html('<i class="bi bi-search"></i> Generate');
                }
            });
        }

        function updateTable(data) {
            let html = '';
            if (data.length > 0) {
                data.forEach((detail, index) => {
                    const rowNumber = ((currentPage - 1) * perPage) + index + 1;
                    html += `
                        <tr>
                            <td class="text-center">${rowNumber}</td>
                            <td class="text-center">${detail.mega_pool_no}</td>
                            <td class="text-center">${detail.mini_pool_number}</td>
                            <td class="text-center">${detail.donor_id}</td>
                            <td class="text-center">${detail.donation_date}</td>
                            <td class="text-center">${detail.blood_group}</td>
                            <td class="text-center">${detail.bag_volume_ml}</td>
                            <td class="text-center">${detail.tail_cutting}</td>
                            <td class="text-center">${detail.prepared_by}</td>
                        </tr>
                    `;
                });

                // Calculate total volume in liters from all data
                let totalVolumeML = 0;
                allData.forEach(item => {
                    totalVolumeML += parseFloat(item.bag_volume_ml) || 0;
                });
                const totalVolumeLiters = (totalVolumeML / 1000).toFixed(2);
                $('#totalVolume').text(totalVolumeLiters);
            } else {
                html = `
                    <tr>
                        <td colspan="9" class="text-center py-3">
                            <div class="alert alert-warning mb-0">No data found</div>
                        </td>
                    </tr>
                `;
                $('#totalVolume').text('0.00');
            }
            $('#reportBody').html(html);
            updatePaginationInfo(data.length, totalRecords);
        }

        function updatePagination() {
            const totalPages = Math.ceil(totalRecords / perPage);
            let paginationHtml = '<ul class="pagination pagination-sm mb-0">';

            // Previous button
            paginationHtml += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            `;

            // Calculate pagination range
            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, startPage + 4);
            if (endPage - startPage < 4) {
                startPage = Math.max(1, endPage - 4);
            }

            // First page link if not in range
            if (startPage > 1) {
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="1">1</a>
                    </li>
                `;
                if (startPage > 2) {
                    paginationHtml += `
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                    `;
                }
            }

            // Page numbers
            for (let i = startPage; i <= endPage; i++) {
                paginationHtml += `
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            // Last page link if not in range
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    paginationHtml += `
                        <li class="page-item disabled">
                            <a class="page-link" href="#">...</a>
                        </li>
                    `;
                }
                paginationHtml += `
                    <li class="page-item">
                        <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>
                `;
            }

            // Next button
            paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            `;

            paginationHtml += '</ul>';
            $('#pagination').html(paginationHtml);
        }

        function updatePaginationInfo(currentCount, total) {
            const start = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
            const end = Math.min(start + currentCount - 1, total);
            $('#paginationInfo').text(`Showing ${start} to ${end} of ${total} entries`);
        }

        // Handle pagination clicks
        $(document).on('click', '.page-link', function(e) {
            e.preventDefault();
            const newPage = $(this).data('page');
            if (newPage && newPage !== currentPage) {
                currentPage = newPage;
                fetchData();
            }
        });
    });
</script>
@endpush
