@extends('include.dashboardLayout')

@section('title', 'AR Number List')

@push('styles')
<style>
    .card {
        margin: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        border: none;
    }
    .card-header {
        background-color: #0c4c90 !important;
        border-bottom: none;
        padding: 1rem;
    }
    .card-header h4 {
        color: white;
        font-weight: 500;
        margin: 0;
    }
    .card-body {
        padding: 1.25rem;
    }
    .table.excel-like {
        margin-bottom: 0;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .excel-like th {
        background-color: #f8f9fa;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 0.75rem 0.5rem;
        white-space: normal;
        vertical-align: middle;
        line-height: 1.2;
        text-align: center;
        border-bottom: 2px solid #dee2e6;
        color: #495057;
    }
    .excel-like td {
        padding: 0.75rem 0.5rem;
        height: auto;
        vertical-align: middle;
        position: relative;
        font-size: 0.85rem;
        border-bottom: 1px solid #dee2e6;
        color: #212529;
    }
    .excel-like tbody tr {
        transition: all 0.2s ease;
    }
    .excel-like tbody tr:hover {
        background-color: #f8f9fa;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .excel-like input {
        background: transparent;
        width: 100%;
        border: none;
        padding: 0.5rem;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    .excel-like input:focus {
        box-shadow: none;
        background: #fff;
        outline: none;
    }
    .table-bordered > :not(caption) > * > * {
        border-width: 1px;
    }
    .pagination {
        margin: 1rem 0;
        justify-content: center;
    }
    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        font-size: 0.85rem;
        color: #0c4c90;
        border: 1px solid #dee2e6;
        margin: 0 2px;
        border-radius: 4px;
    }
    .pagination .page-item.active .page-link {
        background-color: #0c4c90;
        border-color: #0c4c90;
        color: white;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #0c4c90;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    #paginationInfo {
        font-size: 0.85rem;
        color: #6c757d;
    }
    .table-responsive {
        border-radius: 0.25rem;
        box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.05);
    }
    .no-records {
        text-align: center;
        padding: 2rem;
        color: #6c757d;
        font-style: italic;
    }
    /* Loading spinner styles */
    .loading-spinner {
        display: inline-block;
        width: 1.5rem;
        height: 1.5rem;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #0c4c90;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    .header-actions {
        display: flex;
        gap: 0.5rem;
    }
    .btn-action {
        background-color: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: white;
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
        border-radius: 4px;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .btn-action:hover {
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        border-color: rgba(255, 255, 255, 0.3);
    }
    .btn-action i {
        font-size: 1rem;
    }
    @media print {
        .header-actions, .pagination, #paginationInfo {
            display: none !important;
        }
        .card {
            box-shadow: none !important;
        }
        .table.excel-like {
            box-shadow: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-center mb-0">AR Number List</h4>
            <div class="header-actions">
                <button type="button" class="btn-action" id="exportBtn" title="Export to Excel">
                    <i class="bi bi-file-earmark-excel"></i>
                    Export
                </button>
                <button type="button" class="btn-action" id="printBtn" title="Print List">
                    <i class="bi bi-printer"></i>
                    Print
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered excel-like">
                <thead>
                    <tr>
                        <th>SL No.</th>
                        <th>Pickup Date</th>
                        <th>Date of Receipt</th>
                        <th>GRN No.</th>
                        <th>Blood Bank Name</th>
                        <th>Plasma Qty(Ltr)</th>
                        <th>AR No.</th>
                        <th>Entered By</th>
                        <th>Remarks</th>
                    </tr>
                </thead>
                <tbody id="arListTableBody">
                    <!-- Data will be populated here -->
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted" id="paginationInfo">
                Showing <span id="startRecord">0</span> to <span id="endRecord">0</span> of <span id="totalRecords">0</span> entries
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination" id="pagination">
                    <!-- Pagination will be populated here -->
                </ul>
            </nav>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentPage = 1;
    const perPage = 20; // Number of records per page

    function loadARList(page) {
        // Show loading state
        Swal.fire({
            title: 'Loading...',
            text: 'Please wait while we fetch the data.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch paginated AR list data
        $.ajax({
            url: '{{ route("plasma.get-ar-list") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                page: page,
                per_page: perPage
            },
            success: function(response) {
                Swal.close();
                
                if (response.status === 'success') {
                    // Populate the table with the data
                    const tbody = $('#arListTableBody');
                    tbody.empty();

                    if (response.data.length === 0) {
                        tbody.append('<tr><td colspan="9" class="no-records">No records found</td></tr>');
                        return;
                    }

                    response.data.forEach((entry, index) => {
                        const rowIndex = ((page - 1) * perPage) + index + 1;
                        tbody.append(`
                            <tr>
                                <td>${rowIndex}</td>
                                <td>${entry.pickup_date || '-'}</td>
                                <td>${entry.receipt_date || '-'}</td>
                                <td>${entry.grn_no || '-'}</td>
                                <td>${entry.blood_bank_name || '-'}</td>
                                <td>${entry.plasma_qty || '-'}</td>
                                <td>${entry.ar_no || '-'}</td>
                                <td>${entry.entered_by || '-'}</td>
                                <td>${entry.remarks || '-'}</td>
                            </tr>
                        `);
                    });

                    // Update pagination info
                    const startRecord = ((page - 1) * perPage) + 1;
                    const endRecord = Math.min(startRecord + response.data.length - 1, response.total);
                    $('#startRecord').text(startRecord);
                    $('#endRecord').text(endRecord);
                    $('#totalRecords').text(response.total);

                    // Generate pagination links
                    const totalPages = Math.ceil(response.total / perPage);
                    const pagination = $('#pagination');
                    pagination.empty();

                    // Previous button
                    pagination.append(`
                        <li class="page-item ${page === 1 ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${page - 1}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    `);

                    // Page numbers
                    let startPage = Math.max(1, page - 2);
                    let endPage = Math.min(totalPages, startPage + 4);
                    
                    if (endPage - startPage < 4) {
                        startPage = Math.max(1, endPage - 4);
                    }

                    if (startPage > 1) {
                        pagination.append(`
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="1">1</a>
                            </li>
                            ${startPage > 2 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                        `);
                    }

                    for (let i = startPage; i <= endPage; i++) {
                        pagination.append(`
                            <li class="page-item ${i === page ? 'active' : ''}">
                                <a class="page-link" href="#" data-page="${i}">${i}</a>
                            </li>
                        `);
                    }

                    if (endPage < totalPages) {
                        pagination.append(`
                            ${endPage < totalPages - 1 ? '<li class="page-item disabled"><span class="page-link">...</span></li>' : ''}
                            <li class="page-item">
                                <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                            </li>
                        `);
                    }

                    // Next button
                    pagination.append(`
                        <li class="page-item ${page === totalPages ? 'disabled' : ''}">
                            <a class="page-link" href="#" data-page="${page + 1}" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    `);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to fetch AR list'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMessage = 'Failed to fetch AR list';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    }

    // Initial load
    loadARList(currentPage);

    // Handle pagination clicks
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).parent().hasClass('disabled')) {
            currentPage = page;
            loadARList(page);
        }
    });

    // Export to Excel functionality
    $('#exportBtn').click(function() {
        // Show loading state
        Swal.fire({
            title: 'Preparing Export...',
            text: 'Please wait while we prepare your data for export.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Fetch all data for export
        $.ajax({
            url: '{{ route("plasma.get-ar-list") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                export: true
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Create CSV content
                    let csvContent = "SL No.,Pickup Date,Date of Receipt,GRN No.,Blood Bank Name,Plasma Qty(Ltr),AR No.,Entered By,Remarks\n";
                    
                    response.data.forEach((entry, index) => {
                        csvContent += `${index + 1},${entry.pickup_date || ''},${entry.receipt_date || ''},${entry.grn_no || ''},${entry.blood_bank_name || ''},${entry.plasma_qty || ''},${entry.ar_no || ''},${entry.entered_by || ''},${entry.remarks || ''}\n`;
                    });

                    // Create and download file
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const link = document.createElement("a");
                    const url = URL.createObjectURL(blob);
                    link.setAttribute("href", url);
                    link.setAttribute("download", "AR_Number_List_" + new Date().toISOString().split('T')[0] + ".csv");
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    Swal.close();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Export Failed',
                        text: response.message || 'Failed to export data'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                let errorMessage = 'Failed to export data';
                
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.message) {
                        errorMessage = response.message;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: errorMessage
                });
            }
        });
    });

    // Print functionality
    $('#printBtn').click(function() {
        window.open('{{ route("plasma.ar-list.print") }}', '_blank');
    });
});
</script>
@endpush 