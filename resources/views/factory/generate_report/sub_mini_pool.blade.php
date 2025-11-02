@extends('include.dashboardLayout')

@push('styles')
<style>
    .card {
        margin: 0.5rem;
    }
    .form-control-sm, .form-select-sm {
        height: 38px;
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 0.9rem;
    }
    .table-sm th,
    .table-sm td {
        padding: 0.5rem;
        font-size: 0.9rem;
    }
    .table thead th {
        font-weight: 600;
        background-color: #f8f9fa;
    }
    .nonreactive {
        background-color: #d4edda !important;
        color: #155724 !important;
        font-weight: bold;
    }
    .borderline {
        background-color: #fff3cd !important;
        color: #856404 !important;
        font-weight: bold;
    }
    .reactive {
        background-color: #f8d7da !important;
        color: #721c24 !important;
        font-weight: bold;
    }
    .report-header {
        background-color: #0c4c90;
        color: white;
        padding: 1rem;
        border-radius: 0.25rem 0.25rem 0 0;
    }
    .loading-spinner {
        display: none;
        text-align: center;
        padding: 2rem;
    }
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }
</style>
@endpush

@section('content')

<div class="pagetitle">
    <h1>Sub Mini Pool Report</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item">Reports</li>
            <li class="breadcrumb-item active">Sub Mini Pool Report</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <!-- Filter Section -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="miniPoolSelect" class="form-label">Select Mini Pool <span class="text-danger">*</span></label>
                            <select class="form-select" id="miniPoolSelect" required>
                                <option value="">-- Select Mini Pool --</option>
                                    </select>
                        </div>
                        <div class="col-md-3">
                            <label for="subMiniPoolSelect" class="form-label">Select Sub Mini Pool (Optional)</label>
                            <select class="form-select" id="subMiniPoolSelect" disabled>
                                <option value="">-- All Sub Mini Pools --</option>
                            </select>
                        </div>
                        <div class="col-md-6" style="padding-top: 30px;">
                            <div class="d-flex gap-2 justify-content-end">
                                <button type="button" class="btn btn-primary btn-sm" id="generateReportBtn" onclick="generateReport()">
                                    <i class="bi bi-file-earmark-text"></i> Generate Report
                                </button>
                                <button type="button" class="btn btn-success btn-sm" id="exportPdfBtn" onclick="exportToPDF()" disabled>
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </button>
                                <button type="button" class="btn btn-info btn-sm"  id="exportExcelBtn" onclick="exportToExcel()" disabled>
                                    <i class="bi bi-file-excel"></i> Export Excel
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Loading Spinner -->
                    <div class="loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading report data...</p>
                    </div>

                    <!-- Report Section -->
                    <div id="reportSection" style="display: none;">
                        <div class="card">
                            <div class="report-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-1">Sub Mini Pool ELISA Test Report</h5>
                                        <p class="mb-0" id="reportTitle">Mini Pool: <span id="selectedMiniPool"></span></p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0">Generated: <span id="reportDate"></span></p>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <!-- Summary Cards -->
                                {{-- <div class="row mb-4">
                                    <div class="col-md-3">
                                        <div class="card text-center">
                                            <div class="card-body">
                                                <h3 class="mb-0" id="totalCount">0</h3>
                                                <p class="text-muted mb-0">Total Samples</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-success text-white">
                                            <div class="card-body">
                                                <h3 class="mb-0" id="nonreactiveCount">0</h3>
                                                <p class="mb-0">Nonreactive</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-warning text-dark">
                                            <div class="card-body">
                                                <h3 class="mb-0" id="borderlineCount">0</h3>
                                                <p class="mb-0">Borderline</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card text-center bg-danger text-white">
                                            <div class="card-body">
                                                <h3 class="mb-0" id="reactiveCount">0</h3>
                                                <p class="mb-0">Reactive</p>
                                            </div>
                                        </div>
                                    </div>
                                </div> --}}

                                <!-- Results Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover table-sm" id="reportTable">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Sub Mini Pool ID</th>
                                                <th>Mini Pool Number</th>
                                                <th>Well Number</th>
                                                <th>OD Value</th>
                                                <th>Ratio</th>
                                                <th>HBV</th>
                                                <th>HCV</th>
                                                <th>HIV</th>
                                                <th>Final Result</th>
                                                <th>Test Date</th>
                                            </tr>
                                        </thead>
                                        <tbody id="reportTableBody">
                                            <!-- Data will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    let reportData = [];

    $(document).ready(function() {
        // Load mini pools on page load
        loadMiniPools();

        // Handle mini pool selection
        $('#miniPoolSelect').on('change', function() {
            const miniPoolNumber = $(this).val();
            if (miniPoolNumber) {
                loadSubMiniPools(miniPoolNumber);
            } else {
                $('#subMiniPoolSelect').prop('disabled', true).html('<option value="">-- All Sub Mini Pools --</option>');
            }
        });
    });

    // Load mini pools that have sub mini pool test results
    function loadMiniPools() {
        $.ajax({
            url: '{{ route("subminipool.get-mini-pools-with-results") }}',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#miniPoolSelect');
                    select.html('<option value="">-- Select Mini Pool --</option>');

                    response.data.forEach(function(miniPool) {
                        select.append(`<option value="${miniPool}">${miniPool}</option>`);
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading mini pools:', error);
                showToast('error', 'Failed to load mini pools');
            }
        });
    }

    // Load sub mini pools for selected mini pool
    function loadSubMiniPools(miniPoolNumber) {
        $.ajax({
            url: '{{ route("subminipool.get-sub-mini-pools-by-mini-pool") }}',
            method: 'GET',
            data: { mini_pool_number: miniPoolNumber },
            success: function(response) {
                if (response.status === 'success') {
                    const select = $('#subMiniPoolSelect');
                    select.html('<option value="">-- All Sub Mini Pools --</option>');

                    response.data.forEach(function(subMiniPool) {
                        select.append(`<option value="${subMiniPool.sub_mini_pool_id}">${subMiniPool.sub_mini_pool_id}</option>`);
                    });

                    select.prop('disabled', false);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading sub mini pools:', error);
                showToast('error', 'Failed to load sub mini pools');
            }
        });
    }

    // Generate report
    function generateReport() {
        const miniPoolNumber = $('#miniPoolSelect').val();
        const subMiniPoolId = $('#subMiniPoolSelect').val();

        if (!miniPoolNumber) {
            showToast('error', 'Please select a mini pool');
            return;
        }

        // Show loading
        $('#loadingSpinner').show();
        $('#reportSection').hide();
        $('#generateReportBtn').prop('disabled', true);

        // Fetch report data
        $.ajax({
            url: '{{ route("subminipool.get-report-data") }}',
            method: 'GET',
            data: {
                mini_pool_number: miniPoolNumber,
                sub_mini_pool_id: subMiniPoolId
            },
            success: function(response) {
                if (response.status === 'success') {
                    reportData = response.data;
                    displayReport(reportData, miniPoolNumber, subMiniPoolId);
                    showToast('success', 'Report generated successfully');
                } else {
                    showToast('error', response.message || 'Failed to generate report');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error generating report:', error);
                showToast('error', 'Failed to generate report');
            },
            complete: function() {
                $('#loadingSpinner').hide();
                $('#generateReportBtn').prop('disabled', false);
            }
        });
    }

    // Display report
    function displayReport(data, miniPoolNumber, subMiniPoolId) {
        // Update header
        $('#selectedMiniPool').text(miniPoolNumber);
        $('#reportDate').text(new Date().toLocaleString());

        // Calculate summary
        let total = data.length;
        let nonreactive = 0;
        let borderline = 0;
        let reactive = 0;

        data.forEach(function(item) {
            const result = (item.final_result || '').toLowerCase();
            if (result === 'nonreactive') nonreactive++;
            else if (result === 'borderline') borderline++;
            else if (result === 'reactive') reactive++;
        });

        $('#totalCount').text(total);
        $('#nonreactiveCount').text(nonreactive);
        $('#borderlineCount').text(borderline);
        $('#reactiveCount').text(reactive);

        // Populate table
        const tbody = $('#reportTableBody');
        tbody.empty();

        data.forEach(function(item, index) {
            const row = $('<tr>');

            // Determine result classes
            const hbvClass = getResultClass(item.hbv);
            const hcvClass = getResultClass(item.hcv);
            const hivClass = getResultClass(item.hiv);
            const finalClass = getResultClass(item.final_result);

            row.append($('<td>').text(index + 1));
            row.append($('<td>').text(item.sub_mini_pool_id || '-'));
            row.append($('<td>').text(item.mini_pool_number || '-'));
            row.append($('<td>').text(item.well_num || '-'));
            row.append($('<td>').text(item.od_value || '-'));
            row.append($('<td>').text(item.ratio || '-'));
            row.append($('<td>').addClass(hbvClass).text(item.hbv || '-'));
            row.append($('<td>').addClass(hcvClass).text(item.hcv || '-'));
            row.append($('<td>').addClass(hivClass).text(item.hiv || '-'));
            row.append($('<td>').addClass(finalClass).text(item.final_result || '-'));
            row.append($('<td>').text(item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'));

            tbody.append(row);
        });

        // Show report and enable export buttons
        $('#reportSection').show();
        $('#exportPdfBtn, #exportExcelBtn').prop('disabled', false);
    }

    // Get result class for styling
    function getResultClass(result) {
        if (!result) return '';
        const r = result.toLowerCase();
        if (r === 'reactive') return 'reactive';
        if (r === 'nonreactive') return 'nonreactive';
        if (r === 'borderline') return 'borderline';
        return '';
    }

    // Export to PDF
    function exportToPDF() {
        if (reportData.length === 0) {
            showToast('error', 'No data to export');
            return;
        }

        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4');

        // Add title
        doc.setFontSize(16);
        doc.text('Sub Mini Pool ELISA Test Report', 14, 15);

        doc.setFontSize(10);
        doc.text(`Mini Pool: ${$('#selectedMiniPool').text()}`, 14, 22);
        doc.text(`Generated: ${$('#reportDate').text()}`, 14, 27);

        // Prepare table data
        const tableData = reportData.map((item, index) => [
            index + 1,
            item.sub_mini_pool_id || '-',
            item.mini_pool_number || '-',
            item.well_num || '-',
            item.od_value || '-',
            item.ratio || '-',
            item.hbv || '-',
            item.hcv || '-',
            item.hiv || '-',
            item.final_result || '-',
            item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'
        ]);

        // Add table
        doc.autoTable({
            head: [['S.No', 'Sub Mini Pool ID', 'Mini Pool', 'Well', 'OD Value', 'Ratio', 'HBV', 'HCV', 'HIV', 'Final Result', 'Date']],
            body: tableData,
            startY: 32,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [12, 76, 144] }
        });

        // Save PDF
        doc.save(`sub_mini_pool_report_${new Date().getTime()}.pdf`);
        showToast('success', 'PDF exported successfully');
    }

    // Export to Excel
    function exportToExcel() {
        if (reportData.length === 0) {
            showToast('error', 'No data to export');
            return;
        }

        // Prepare data for Excel
        const excelData = reportData.map((item, index) => ({
            'S.No': index + 1,
            'Sub Mini Pool ID': item.sub_mini_pool_id || '-',
            'Mini Pool Number': item.mini_pool_number || '-',
            'Well Number': item.well_num || '-',
            'OD Value': item.od_value || '-',
            'Ratio': item.ratio || '-',
            'HBV': item.hbv || '-',
            'HCV': item.hcv || '-',
            'HIV': item.hiv || '-',
            'Final Result': item.final_result || '-',
            'Test Date': item.created_at ? new Date(item.created_at).toLocaleDateString() : '-'
        }));

        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(excelData);

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, 'Sub Mini Pool Report');

        // Save file
        XLSX.writeFile(wb, `sub_mini_pool_report_${new Date().getTime()}.xlsx`);
        showToast('success', 'Excel exported successfully');
    }

    // Toast notification function
    function showToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;

        document.body.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        toast.addEventListener('hidden.bs.toast', function() {
            document.body.removeChild(toast);
        });
    }
</script>
@endpush
