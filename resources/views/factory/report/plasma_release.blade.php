@extends('include.dashboardLayout')

@section('title', 'Plasma Release')

@push('styles')
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css">
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
    .badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.65rem;
        border-radius: 4px;
    }
    .badge.bg-info {
        background-color: #0dcaf0 !important;
        color: #000;
    }
    .me-1 {
        margin-right: 0.25rem !important;
    }
    .mb-1 {
        margin-bottom: 0.25rem !important;
    }
    .d-flex {
        display: flex !important;
    }
    .flex-wrap {
        flex-wrap: wrap !important;
    }
    .align-middle {
        vertical-align: middle !important;
    }
    .table-bordered > tbody > tr > td {
        border: 1px solid #dee2e6;
    }
    .table-bordered > tbody > tr > td[rowspan] {
        border-bottom: 2px solid #dee2e6;
    }
    .btn-group {
        display: inline-flex;
        gap: 0.25rem;
    }
    .btn-success, .btn-danger {
        background-color: #ffffff;
        border: 1px solid #dee2e6;
        color: #333;
    }
    .btn-success:hover {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    .btn-danger:hover {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .btn-success.active {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    .btn-danger.active {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    .submit-container {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        padding-bottom: 20px;
    }
    #submitSelectedData {
        background-color: #0c4c90;
        color: white;
        padding: 10px 30px;
        border-radius: 5px;
        border: none;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        opacity: 0.6;
        cursor: not-allowed;
        min-width: 150px;
    }
    #submitSelectedData.active {
        opacity: 1;
        cursor: pointer;
    }
    .btn i {
        font-size: 0.875rem;
    }
    .ms-2 {
        margin-left: 0.5rem !important;
    }
    .badge.bg-danger {
        background-color: #dc3545 !important;
        color: white;
        font-weight: normal;
        font-size: 0.75rem;
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
            <h4 class="text-center mb-0">Plasma Release</h4>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered excel-like">
                <thead>
                    <tr>
                        <th style="width: 30%;">AR Number</th>
                        <th style="width: 40%;">Mega Pool Number</th>
                        <th style="width: 30%;">Action</th>
                    </tr>
                </thead>
                <tbody id="plasma_release_ar-no">
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
        <!-- Submit button container -->
        <div class="submit-container">
            <button id="submitSelectedData" class="btn" disabled>
                <i class="fas fa-check-circle me-2"></i>Submit
            </button>
        </div>
    </div>
</div>


@endsection

@push('scripts')
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
<script>
$(document).ready(function() {
    let currentPage = {{ $pagination['current_page'] ?? 1 }};
    let selectedItems = [];
    const perPage = {{ $pagination['per_page'] ?? 20 }}; // Number of records per page
    const totalRecords = {{ $pagination['total'] ?? 0 }};
    const lastPage = {{ $pagination['last_page'] ?? 1 }};

    // Function to populate the table
    function populateTable(data) {
        const tbody = $('#plasma_release_ar-no');
        tbody.empty();

        if (data.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="3" class="text-center">No records found</td>
                </tr>
            `);
            return;
        }

        data.forEach(function(item) {
            // Create first row with rowspan for AR number only
                        const firstPool = item.mega_pools[0];
            tbody.append(`
                <tr>
                    <td rowspan="${item.rowspan}" class="align-middle" style="background-color: #f8f9fa;">${item.ar_no}</td>
                    <td>
                        ${firstPool.pool_no}
                        ${firstPool.status === 'reactive' ? '<span class="badge bg-danger ms-2">(Reactive)</span>' : ''}
                    </td>
                    <td class="text-center">
                        <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-success release-btn"
                                data-ar-no="${item.ar_no}"
                                data-mega-pool="${firstPool.pool_no}"
                                data-status="${firstPool.status}">
                                <i class="fas fa-check-circle me-1"></i> Release
                            </button>
                            <button class="btn btn-sm btn-danger reject-btn"
                                data-ar-no="${item.ar_no}"
                                data-mega-pool="${firstPool.pool_no}"
                                data-status="${firstPool.status}">
                                <i class="fas fa-times-circle me-1"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
            `);

            // Create additional rows for remaining mega pools
            for (let i = 1; i < item.mega_pools.length; i++) {
                const pool = item.mega_pools[i];
                tbody.append(`
                    <tr>
                        <td>
                            ${pool.pool_no}
                            ${pool.status === 'reactive' ? '<span class="badge bg-danger ms-2">(Reactive)</span>' : ''}
                        </td>
                        <td class="text-center">
                            <div class="btn-group" role="group">
                                <button class="btn btn-sm btn-success release-btn"
                                    data-ar-no="${item.ar_no}"
                                    data-mega-pool="${pool.pool_no}"
                                    data-status="${pool.status}">
                                    <i class="fas fa-check-circle me-1"></i> Release
                                </button>
                                <button class="btn btn-sm btn-danger reject-btn"
                                    data-ar-no="${item.ar_no}"
                                    data-mega-pool="${pool.pool_no}"
                                    data-status="${pool.status}">
                                    <i class="fas fa-times-circle me-1"></i> Reject
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Function to update pagination
    function updatePagination() {
        const pagination = $('#pagination');
        pagination.empty();

        // Previous button
        pagination.append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}" aria-label="Previous">
                    <span aria-hidden="true">&laquo;</span>
                </a>
            </li>
        `);

        // Page numbers
        for (let i = 1; i <= lastPage; i++) {
            if (
                i === 1 || // First page
                i === lastPage || // Last page
                (i >= currentPage - 2 && i <= currentPage + 2) // Pages around current page
            ) {
                pagination.append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (
                i === currentPage - 3 || // Show dots before current page
                i === currentPage + 3 // Show dots after current page
            ) {
                pagination.append(`
                    <li class="page-item disabled">
                        <span class="page-link">...</span>
                    </li>
                `);
            }
        }

        // Next button
        pagination.append(`
            <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}" aria-label="Next">
                    <span aria-hidden="true">&raquo;</span>
                </a>
            </li>
        `);

        // Update pagination info
        const start = (currentPage - 1) * perPage + 1;
        const end = Math.min(currentPage * perPage, totalRecords);
        $('#startRecord').text(start);
        $('#endRecord').text(end);
        $('#totalRecords').text(totalRecords);
    }

    // Load initial data
    @if(isset($initialData))
        populateTable(@json($initialData));
        updatePagination();
    @endif


    // Handle pagination clicks
    $(document).on('click', '.page-link', function(e) {
        e.preventDefault();
        const page = $(this).data('page');
        if (page && !$(this).parent().hasClass('disabled')) {
            loadPage(page);
        }
    });

    // Function to load page data
    function loadPage(page) {
        $.ajax({
            url: '{{ route("factory.report.plasma_release") }}',
            method: 'GET',
            data: { page: page },
            beforeSend: function() {
                // Show loading state
                $('#plasma_release_ar-no').html(`
                    <tr>
                        <td colspan="3" class="text-center">
                            <div class="loading-spinner"></div>
                            Loading...
                        </td>
                    </tr>
                `);
            },
            success: function(response) {
                currentPage = response.pagination.current_page;
                populateTable(response.data);
                updatePagination();
            },
            error: function(xhr) {
                $('#plasma_release_ar-no').html(`
                    <tr>
                        <td colspan="3" class="text-center text-danger">
                            Error loading data. Please try again.
                        </td>
                    </tr>
                `);
            }
        });
    }

    // Function to update submit button state
    function updateSubmitButton() {
        const $submitBtn = $('#submitSelectedData');
        if (selectedItems.length > 0) {
            $submitBtn.addClass('active').prop('disabled', false);
        } else {
            $submitBtn.removeClass('active').prop('disabled', true);
        }
    }

    // Function to handle item selection
    function handleItemSelection(button, action) {
        const arNo = button.data('ar-no');
        const megaPool = button.data('mega-pool');
        const status = button.data('status');

        // Remove any existing selection for this mega pool
        selectedItems = selectedItems.filter(item => !(item.arNo === arNo && item.megaPool === megaPool));

        // Remove active class from both buttons in the group
        button.closest('.btn-group').find('.btn').removeClass('active');

        // If the button was already active (same action), deselect it
        if (button.hasClass('active')) {
            button.removeClass('active');
        } else {
            // Add active class to the clicked button
            button.addClass('active');
            // Add the new selection
            selectedItems.push({
                arNo,
                megaPool,
                status,
                action
            });
        }

        updateSubmitButton();
    }

    // Handle release button click
    $(document).on('click', '.release-btn', function() {
        handleItemSelection($(this), 'release');
    });

    // Handle reject button click
    $(document).on('click', '.reject-btn', function() {
        handleItemSelection($(this), 'reject');
    });

    // Handle submit button click
    $(document).on('click', '#submitSelectedData', function() {
        if (selectedItems.length === 0) {
            alert('Please select items to submit');
            return;
        }

        const summary = selectedItems.map(item =>
            `${item.action.toUpperCase()} - AR No: ${item.arNo}, Mega Pool: ${item.megaPool}`
        ).join('\n');

                 Swal.fire({
             title: 'Confirm Submission',
             icon: 'question',
             showCancelButton: true,
             confirmButtonColor: '#0c4c90',
             cancelButtonColor: '#6c757d',
             confirmButtonText: 'Yes, Submit',
             cancelButtonText: 'Cancel'
         }).then((result) => {
             if (result.isConfirmed) {
                 // Show loading state
                 Swal.fire({
                     title: 'Processing...',
                     html: 'Please wait while we process your request.',
                     allowOutsideClick: false,
                     didOpen: () => {
                         Swal.showLoading();
                     }
                 });

                 // Submit the data
                 $.ajax({
                     url: '{{ route("factory.report.plasma_submit") }}',
                     method: 'POST',
                     data: {
                         items: selectedItems,
                         _token: '{{ csrf_token() }}'
                     },
                                         success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                title: 'Success!',
                                text: 'Successfully submitted all items',
                                icon: 'success',
                                confirmButtonColor: '#0c4c90'
                            }).then(() => {
                                selectedItems = [];
                                updateSubmitButton();
                                loadPage(currentPage);
                            });
                        } else {
                            Swal.fire({
                                title: 'Error',
                                text: response.message || 'An unknown error occurred',
                                icon: 'error',
                                confirmButtonColor: '#0c4c90'
                            });
                        }
                    },
                     error: function(xhr) {
                         Swal.fire({
                             title: 'Error!',
                             text: 'Error submitting data: ' + (xhr.responseJSON?.message || 'Please try again.'),
                             icon: 'error',
                             confirmButtonColor: '#0c4c90'
                         });
                     }
                 });
             }
         });
    });
});
</script>
@endpush
