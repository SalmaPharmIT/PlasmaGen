@extends('include.dashboardLayout')

@section('title', 'Report Upload')

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
    <div class="card">
                <div class="card-body p-2">
                    <form id="hbvUploadForm" class="report-upload-form" method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="test_type" value="HBV">
                        
                        <div class="row g-2 mb-2">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="small mb-1">HBV Upload</label>
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control form-control-sm file-input" 
                                               name="report_files[]" 
                                               accept=".res"
                                               multiple
                                               required>
                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                            <i class="fa fa-folder-open"></i> Browse
                                        </button>
                                    </div>
                                    <small class="text-muted">Allowed file types: RES (.res)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mb-2" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%"
                                 aria-valuenow="0" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">0%</div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm upload-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Upload & View
                                </button>
                            </div>
                </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
        <div class="card-body p-2">
                    <form id="hcvUploadForm" class="report-upload-form" method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data">
                @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="test_type" value="HCV">
                
                <div class="row g-2 mb-2">
                            <div class="col-md-12">
                        <div class="form-group">
                                    <label class="small mb-1">HCV Upload</label>
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control form-control-sm file-input" 
                                               name="report_files[]" 
                                               accept=".res"
                                               multiple
                                               required>
                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                            <i class="fa fa-folder-open"></i> Browse
                                        </button>
                                    </div>
                                    <small class="text-muted">Allowed file types: RES (.res)</small>
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div class="progress mb-2" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%"
                                 aria-valuenow="0" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">0%</div>
                        </div>

                        <div class="row mt-2">
                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm upload-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Upload & View
                                </button>
                    </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body p-2">
                    <form id="hivUploadForm" class="report-upload-form" method="POST" action="{{ route('report.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="test_type" value="HIV">

                <div class="row g-2 mb-2">
                    <div class="col-md-12">
                        <div class="form-group">
                                    <label class="small mb-1">HIV Upload</label>
                                    <div class="input-group">
                                        <input type="file" 
                                               class="form-control form-control-sm file-input" 
                                               name="report_files[]" 
                                               accept=".res"
                                               multiple
                                               required>
                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                            <i class="fa fa-folder-open"></i> Browse
                                        </button>
                                    </div>
                                    <small class="text-muted">Allowed file types: RES (.res)</small>
                                    <div class="invalid-feedback"></div>
                        </div>
                    </div>
                </div>

                        <!-- Progress Bar -->
                        <div class="progress mb-2" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 role="progressbar" 
                                 style="width: 0%"
                                 aria-valuenow="0" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">0%</div>
                </div>

                <div class="row mt-2">
                    <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary btn-sm upload-btn">
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    Upload & View
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Section -->
    <div id="resultsSection" class="mt-4" style="display: none;">
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs mb-3" id="resultsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="hbv-results-tab" data-bs-toggle="tab" data-bs-target="#hbv-results" type="button" role="tab" aria-controls="hbv-results" aria-selected="true">HBV Results</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hcv-results-tab" data-bs-toggle="tab" data-bs-target="#hcv-results" type="button" role="tab" aria-controls="hcv-results" aria-selected="false">HCV Results</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="hiv-results-tab" data-bs-toggle="tab" data-bs-target="#hiv-results" type="button" role="tab" aria-controls="hiv-results" aria-selected="false">HIV Results</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="final-results-tab" data-bs-toggle="tab" data-bs-target="#final-results" type="button" role="tab" aria-controls="final-results" aria-selected="false">Results</button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="resultsTabsContent">
            <div class="tab-pane fade show active" id="hbv-results" role="tabpanel" aria-labelledby="hbv-results-tab">
                <div id="hbv-results-content"></div>
            </div>
            <div class="tab-pane fade" id="hcv-results" role="tabpanel" aria-labelledby="hcv-results-tab">
                <div id="hcv-results-content"></div>
            </div>
            <div class="tab-pane fade" id="hiv-results" role="tabpanel" aria-labelledby="hiv-results-tab">
                <div id="hiv-results-content"></div>
            </div>
            <div class="tab-pane fade" id="final-results" role="tabpanel" aria-labelledby="final-results-tab">
                <div class="card">
                    <div class="card-header text-white" style="background-color: #0c4c90;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Final Results Summary</h5>
                            <div>
                                <button class="btn btn-sm btn-light me-2" onclick="saveResults(); return false;">
                                    <i class="fa fa-save"></i> Save
                                </button>
                                <a href="#" class="btn btn-sm btn-light me-2" onclick="exportToCSV(); return false;">
                                    <i class="fa fa-download"></i> CSV
                                </a>
                                <a href="#" class="btn btn-sm btn-light" onclick="exportToPDF(); return false;">
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>MiniPool ID</th>
                                        <th>Well Number</th>
                                        <th>OD Value</th>
                                        <th>Timestamp</th>
                                        <th>HBV</th>
                                        <th>HCV</th>
                                        <th>HIV</th>
                                        <th>Final Result</th>
                                    </tr>
                                </thead>
                                <tbody id="finalResultsTable">
                                    <!-- Results will be populated here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card {
        margin: 0.25rem;
    }
    .card-body {
        padding: 0.5rem;
    }
    .form-control-sm {
        height: 22px;
        min-height: 22px;
        padding: 0.1rem 0.2rem;
        font-size: 0.8rem;
    }
    label {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
    }
    .toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }
    .nonreactive { background-color: #d4edda !important; color: #155724 !important; font-weight: bold; }
    .borderline  { background-color: #fff3cd !important; color: #856404 !important; font-weight: bold; }
    .reactive    { background-color: #f8d7da !important; color: #721c24 !important; font-weight: bold; }
    .control     { background-color: #e0f7fa !important; font-weight: bold !important; }
    .chart-container { max-width: 600px; margin: auto; padding-top: 5px; }
    .progress {
        height: 4px;
        margin-bottom: 0.25rem;
    }
    .nav-tabs {
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 0.5rem;
    }
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        padding: 0.25rem 0.5rem;
        margin-right: 0.25rem;
        font-size: 0.9rem;
    }
    .nav-tabs .nav-link:hover {
        border: none;
        color: #0d6efd;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border: none;
        border-bottom: 2px solid #0d6efd;
        margin-bottom: -2px;
    }
    .table-sm th, 
    .table-sm td {
        padding: 0.25rem;
    }
    .card-title {
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }
    .mb-4 {
        margin-bottom: 0.5rem !important;
    }
    .mb-2 {
        margin-bottom: 0.25rem !important;
    }
    .mt-2 {
        margin-top: 0.25rem !important;
    }
    .p-2 {
        padding: 0.25rem !important;
    }
    .rounded {
        border-radius: 0.2rem !important;
    }
    .badge {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    .table-responsive {
        margin-bottom: 0;
    }
    .table thead th {
        font-size: 0.8rem;
        font-weight: 600;
    }
    .table tbody td {
        font-size: 0.8rem;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script>
    // Store all results by test type
    const allResults = {
        HBV: [],
        HCV: [],
        HIV: []
    };

    // Track if data is saved
    let isDataSaved = false;

    // Export functions
    function exportToCSV() {
        if (!isDataSaved) {
            showToast('warning', 'Please save the results before exporting to CSV. Click the Save button first.');
            return;
        }

        if (!window.exportData || window.exportData.length === 0) {
            showToast('error', 'No data available to export');
            return;
        }

        const headers = ['MiniPool ID', 'Well Number', 'OD Value', 'Timestamp', 'HBV', 'HCV', 'HIV', 'Final Result'];
        const csvContent = [
            headers.join(','),
            ...window.exportData.map(reading => {
                const finalResult = (reading.hbv === 'reactive' || reading.hcv === 'reactive' || reading.hiv === 'reactive') ? 'Reactive' :
                                  (reading.hbv === 'borderline' || reading.hcv === 'borderline' || reading.hiv === 'borderline') ? 'Borderline' : 'Non-Reactive';
                
                return [
                    reading.sequence_id,
                    reading.well_label,
                    reading.value,
                    reading.timestamp,
                    reading.hbv || '-',
                    reading.hcv || '-',
                    reading.hiv || '-',
                    finalResult
                ].join(',');
            })
        ].join('\n');

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', `results_${new Date().toISOString().split('T')[0]}.csv`);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function exportToPDF() {
        if (!isDataSaved) {
            showToast('warning', 'Please save the results before exporting to PDF. Click the Save button first.');
            return;
        }

        if (!window.exportData || window.exportData.length === 0) {
            showToast('error', 'No data available to export');
            return;
        }

        // Create a temporary div to hold our content
        const tempDiv = document.createElement('div');
        tempDiv.style.position = 'absolute';
        tempDiv.style.left = '-9999px';
        tempDiv.style.top = '-9999px';
        document.body.appendChild(tempDiv);

        // Create the content
        const content = `
            <div style="padding: 20px; font-family: Arial, sans-serif; max-width: 210mm; margin: 0 auto;">
                <!-- Header Section -->
                <div style="display: flex; align-items: center; margin-bottom: 30px; border-bottom: 2px solid #0c4c90; padding-bottom: 20px;">
                    <div style="flex: 0 0 30%;">
                        <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo" style="max-width: 150px; height: auto;">
                    </div>
                    <div style="flex: 1; text-align: center;">
                        <div style="font-size: 20px; font-weight: bold; color: #0c4c90; margin-bottom: 5px;">Results Summary Report</div>
                        <div style="font-size: 14px; color: #666;">Generated on: ${new Date().toLocaleString()}</div>
                    </div>
                </div>

                <!-- Results Table -->
                <div style="margin-top: 20px; overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 12px;">
                        <thead>
                            <tr>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">MiniPool ID</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">Well Number</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">OD Value</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">Timestamp</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">HBV</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">HCV</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">HIV</th>
                                <th style="border: 1px solid #ddd; padding: 8px; text-align: left; background-color: #f2f2f2; font-weight: bold;">Final Result</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${window.exportData.map(reading => {
                                const finalResult = (reading.hbv === 'reactive' || reading.hcv === 'reactive' || reading.hiv === 'reactive') ? 'Reactive' :
                                                  (reading.hbv === 'borderline' || reading.hcv === 'borderline' || reading.hiv === 'borderline') ? 'Borderline' : 'Non-Reactive';
                                const finalResultClass = finalResult === 'Reactive' ? 'danger' : finalResult === 'Borderline' ? 'info' : 'success';
                                
                                return `
                                    <tr>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.sequence_id}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.well_label}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.value}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.timestamp}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.hbv || '-'}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.hcv || '-'}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px;">${reading.hiv || '-'}</td>
                                        <td style="border: 1px solid #ddd; padding: 8px; color: ${finalResultClass === 'danger' ? '#dc3545' : finalResultClass === 'info' ? '#0dcaf0' : '#198754'}">${finalResult}</td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>

                <!-- Approval Section -->
                <div style="margin-top: 50px; display: flex; justify-content: flex-end;">
                    <div style="text-align: center; width: 300px;">
                        <div style="border-top: 1px solid #000; width: 200px; margin: 0 auto;"></div>
                        <div style="margin-top: 10px; font-size: 14px; font-weight: bold;">Approved by:</div>
                        <div style="margin-top: 5px; font-size: 14px;">Name: ___________________</div>
                        <div style="margin-top: 5px; font-size: 14px;">Designation: ___________________</div>
                        <div style="margin-top: 5px; font-size: 14px;">Date: ___________________</div>
                    </div>
                </div>
            </div>
        `;

        tempDiv.innerHTML = content;

        // Convert to canvas and then to PDF
        html2canvas(tempDiv, {
            scale: 2,
            useCORS: true,
            logging: false,
            allowTaint: true,
            width: 210 * 3.78, // Convert mm to pixels (1mm = 3.78px)
            height: tempDiv.scrollHeight
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF({
                orientation: 'portrait',
                unit: 'mm',
                format: 'a4',
                putOnlyUsedFonts: true
            });

            const imgWidth = 210; // A4 width in mm
            const pageHeight = 297; // A4 height in mm
            const imgHeight = canvas.height * imgWidth / canvas.width;
            let heightLeft = imgHeight;
            let position = 0;

            pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
            heightLeft -= pageHeight;

            while (heightLeft >= 0) {
                position = heightLeft - imgHeight;
                pdf.addPage();
                pdf.addImage(imgData, 'PNG', 0, position, imgWidth, imgHeight);
                heightLeft -= pageHeight;
            }

            // Save the PDF
            pdf.save(`results_${new Date().toISOString().split('T')[0]}.pdf`);
            
            // Clean up
            document.body.removeChild(tempDiv);
        });
    }

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

    function saveResults() {
        // Initialize window.exportData if it doesn't exist
        if (!window.exportData) {
            window.exportData = [];
        }

        if (window.exportData.length === 0) {
            showToast('error', 'No data available to save');
            return;
        }

        // Get CSRF token from meta tag or form input
        const token = document.querySelector('meta[name="csrf-token"]')?.content || 
                     document.querySelector('input[name="_token"]')?.value;

        if (!token) {
            showToast('error', 'CSRF token not found');
            return;
        }

        // Check if any of the files already exist
        const checkExistingFiles = async () => {
            try {
                const response = await fetch('{{ route("report.check-existing") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        readings: window.exportData
                    })
                });
                return await response.json();
            } catch (error) {
                console.error('Error checking existing files:', error);
                return { exists: false };
            }
        };

        // Show confirmation dialog
        const showConfirmationDialog = async () => {
            const dialog = document.createElement('div');
            dialog.className = 'modal fade';
            dialog.setAttribute('tabindex', '-1');
            dialog.innerHTML = `
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Confirm Save</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-warning mb-3">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Records already exist in the database.
                            </div>
                            <p class="mb-0">Do you want to save these records again?</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                            <button type="button" class="btn btn-primary" id="confirmSave">Yes</button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(dialog);
            const modal = new bootstrap.Modal(dialog);
            modal.show();

            return new Promise((resolve) => {
                dialog.querySelector('#confirmSave').addEventListener('click', () => {
                    modal.hide();
                    resolve(true);
                });
                dialog.addEventListener('hidden.bs.modal', () => {
                    document.body.removeChild(dialog);
                    resolve(false);
                });
            });
        };

        // Main save process
        const saveProcess = async () => {
            const saveButton = document.querySelector('.card-header .btn-sm[onclick*="saveResults"]');
            if (!saveButton) {
                showToast('error', 'Save button not found');
                return;
            }

            const originalHtml = saveButton.innerHTML;
            const originalDisabled = saveButton.disabled;

            try {
                // Check for existing files
                const checkResult = await checkExistingFiles();
                
                if (checkResult.exists) {
                    const shouldProceed = await showConfirmationDialog();
                    if (!shouldProceed) {
                        showToast('info', 'Save operation cancelled');
                        return;
                    }
                }

                // Show loading state
                saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
                saveButton.disabled = true;

                // Send data to server
                const response = await fetch('{{ route("report.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({
                        readings: window.exportData,
                        _token: token
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    isDataSaved = true;
                    // Enable export buttons
                    document.querySelectorAll('.card-header a[onclick*="exportTo"]').forEach(btn => {
                        btn.classList.remove('disabled');
                        btn.style.pointerEvents = 'auto';
                        btn.style.opacity = '1';
                    });
                    showToast('success', 'Results saved successfully');
                } else {
                    showToast('error', data.message || 'Failed to save results');
                }
            } catch (error) {
                showToast('error', 'An error occurred while saving');
                console.error('Error:', error);
            } finally {
                // Restore button state
                if (saveButton) {
                    saveButton.innerHTML = originalHtml;
                    saveButton.disabled = originalDisabled;
                }
            }
        };

        saveProcess();
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Initially disable export buttons
        document.querySelectorAll('.card-header a[onclick*="exportTo"]').forEach(btn => {
            btn.classList.add('disabled');
            btn.style.pointerEvents = 'none';
            btn.style.opacity = '0.5';
            // Add click handler for disabled state
            btn.addEventListener('click', function(e) {
                if (!isDataSaved) {
                    e.preventDefault();
                    const format = this.getAttribute('onclick').includes('CSV') ? 'CSV' : 'PDF';
                    showToast('warning', `Please save the results before exporting to ${format}. Click the Save button first.`);
                }
            });
        });

        const forms = document.querySelectorAll('.report-upload-form');
        
        forms.forEach(form => {
            const fileInput = form.querySelector('.file-input');
            const browseBtn = form.querySelector('.browse-btn');
            const uploadBtn = form.querySelector('.upload-btn');
            const spinner = uploadBtn.querySelector('.spinner-border');
            const progressBar = form.querySelector('.progress');
            const progressBarInner = progressBar.querySelector('.progress-bar');
            let selectedFiles = [];

            // Handle browse button click
            browseBtn.addEventListener('click', () => {
                fileInput.click();
            });

            // Handle file selection
            fileInput.addEventListener('change', function() {
                selectedFiles = Array.from(this.files);
                validateFiles(this);
            });

            // Handle form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!form.checkValidity() || !validateFiles(fileInput)) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                
                uploadBtn.disabled = true;
                spinner.classList.remove('d-none');
                progressBar.style.display = 'block';
                progressBarInner.style.width = '0%';
                progressBarInner.textContent = '0%';

                const formData = new FormData(form);
                
                const xhr = new XMLHttpRequest();
                xhr.open('POST', form.action, true);
                xhr.setRequestHeader('X-CSRF-TOKEN', form.querySelector('input[name="_token"]').value);
                xhr.setRequestHeader('Accept', 'application/json');

                xhr.upload.onprogress = function(e) {
                    if (e.lengthComputable) {
                        const percentComplete = (e.loaded / e.total) * 100;
                        progressBarInner.style.width = percentComplete + '%';
                        progressBarInner.textContent = Math.round(percentComplete) + '%';
                    }
                };

                xhr.onload = function() {
                    uploadBtn.disabled = false;
                    spinner.classList.add('d-none');
                    progressBar.style.display = 'none';

                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);
                        if (data.success) {
                            // Add new results to existing ones
                            data.data.forEach(result => {
                                if (result.test_type) {
                                    allResults[result.test_type].push(result);
                                }
                            });
                            showResults();
                            showToast('success', data.message);
                            form.reset();
                        } else {
                            showToast('error', data.message);
                        }
                    } else {
                        showToast('error', 'An error occurred while uploading the report.');
                    }
                };

                xhr.onerror = function() {
                    uploadBtn.disabled = false;
                    spinner.classList.add('d-none');
                    progressBar.style.display = 'none';
                    showToast('error', 'An error occurred while uploading the report.');
                };

                xhr.send(formData);
            });
        });

        function validateFiles(input) {
            const files = input.files;
            const allowedTypes = ['res'];
            let isValid = true;
            
            for (let i = 0; i < files.length; i++) {
                const fileType = files[i].name.split('.').pop().toLowerCase();
                if (!allowedTypes.includes(fileType)) {
                    isValid = false;
                    break;
                }
            }
            
            const feedback = input.parentElement.parentElement.querySelector('.invalid-feedback');
            
            if (!isValid) {
                input.setCustomValidity('Please select valid RES files only');
                feedback.textContent = 'Please select valid RES files only';
                input.classList.add('is-invalid');
            } else {
                input.setCustomValidity('');
                feedback.textContent = '';
                input.classList.remove('is-invalid');
            }

            return isValid;
        }

        function showResults() {
            const resultsSection = document.getElementById('resultsSection');
            resultsSection.style.display = 'block';

            // Update final results summary
            updateFinalResults();

            // Show results in respective tabs
            Object.entries(allResults).forEach(([type, results]) => {
                const tabContent = document.getElementById(`${type.toLowerCase()}-results-content`);
                if (results.length > 0) {
                    tabContent.innerHTML = createResultsHTML(results);
                    createCharts(results, tabContent);
                } else {
                    tabContent.innerHTML = '<div class="alert alert-info">No results available for this test type.</div>';
                }
            });
        }

        function updateFinalResults() {
            const finalResultsTable = document.getElementById('finalResultsTable');
            let allReadings = [];

            // Collect all readings from all test types
            Object.entries(allResults).forEach(([type, results]) => {
                results.forEach(result => {
                    if (result.readings) {
                        result.readings.forEach(reading => {
                            allReadings.push({
                                ...reading,
                                testType: type
                            });
                        });
                    }
                });
            });

            // Group readings by sequence_id and well_label
            const groupedReadings = {};
            allReadings.forEach(reading => {
                const key = `${reading.sequence_id}_${reading.well_label}`;
                if (!groupedReadings[key]) {
                    groupedReadings[key] = {
                        sequence_id: reading.sequence_id,
                        well_label: reading.well_label,
                        value: reading.value,
                        timestamp: reading.timestamp,
                        hbv: null,
                        hcv: null,
                        hiv: null
                    };
                }
                groupedReadings[key][reading.testType.toLowerCase()] = reading.category;
            });

            // Convert to array and sort by timestamp
            const sortedReadings = Object.values(groupedReadings).sort((a, b) => 
                new Date(a.timestamp) - new Date(b.timestamp)
            );

            // Store readings for export
            window.exportData = sortedReadings;

            // Generate table rows
            finalResultsTable.innerHTML = sortedReadings.map(reading => {
                // Determine final result
                let finalResult = 'Non-Reactive';
                let finalResultClass = 'success';
                
                // If any test is reactive, final result is reactive
                if (reading.hbv === 'reactive' || reading.hcv === 'reactive' || reading.hiv === 'reactive') {
                    finalResult = 'Reactive';
                    finalResultClass = 'danger';
                }
                // If any test is borderline and none are reactive, final result is borderline
                else if (reading.hbv === 'borderline' || reading.hcv === 'borderline' || reading.hiv === 'borderline') {
                    finalResult = 'Borderline';
                    finalResultClass = 'info';
                }

                return `
                <tr>
                    <td>${reading.sequence_id}</td>
                    <td>${reading.well_label}</td>
                    <td>${reading.value}</td>
                    <td>${reading.timestamp}</td>
                    <td>${reading.hbv ? `<span class="badge bg-${reading.hbv === 'nonreactive' ? 'success' : reading.hbv === 'borderline' ? 'info' : 'danger'}">${reading.hbv.charAt(0).toUpperCase() + reading.hbv.slice(1)}</span>` : '-'}</td>
                    <td>${reading.hcv ? `<span class="badge bg-${reading.hcv === 'nonreactive' ? 'success' : reading.hcv === 'borderline' ? 'info' : 'danger'}">${reading.hcv.charAt(0).toUpperCase() + reading.hcv.slice(1)}</span>` : '-'}</td>
                    <td>${reading.hiv ? `<span class="badge bg-${reading.hiv === 'nonreactive' ? 'success' : reading.hiv === 'borderline' ? 'info' : 'danger'}">${reading.hiv.charAt(0).toUpperCase() + reading.hiv.slice(1)}</span>` : '-'}</td>
                    <td><span class="badge bg-${finalResultClass}">${finalResult}</span></td>
                </tr>
                `;
            }).join('') || '<tr><td colspan="8" class="text-center">No readings available</td></tr>';
        }

        function createResultsHTML(results) {
            return results.map((result, index) => `
                <div class="card shadow-sm mb-5">
                    <div class="card-header text-white" style="background-color: #0c4c90;">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">${result.test_type} Results Analysis</h5>
                            <div>
                                <span class="badge bg-light text-dark me-2">${result.test_type || 'Unknown Test'}</span>
                                <a href="${result.file_path}" class="btn btn-sm btn-light" download>
                                    <i class="fa fa-download"></i> CSV
                                </a>
                                <a href="${result.file_path}" class="btn btn-sm btn-light" download>
                                    <i class="fa fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Test Information -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title" style="font-size: 1.0rem;padding: 5px 0 5px 0;">Test Information</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <tbody>
                                                    <tr>
                                                        <th class="table-light" style="width: 40%">File Name</th>
                                                        <td>${result.file_name}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table-light">Operator</th>
                                                        <td>${result.operator || 'N/A'}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table-light">Instrument</th>
                                                        <td>${result.instrument || 'N/A'}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="table-light">Protocol</th>
                                                        <td>${result.protocol || 'N/A'}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title" style="font-size: 1.0rem;padding: 5px 0 5px 0;">Results Summary</h6>
                                        <div class="row">
                                            <div class="col-4 text-center">
                                                <div class="p-2 text-white bg-success rounded">
                                                    <h4 class="mb-0">${result.summary?.nonreactive || 0}</h4>
                                                    <small>Non-Reactive</small>
                                                </div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="p-2 text-white bg-info rounded">
                                                    <h4 class="mb-0">${result.summary?.borderline || 0}</h4>
                                                    <small>Borderline</small>
                                                </div>
                                            </div>
                                            <div class="col-4 text-center">
                                                <div class="p-2 text-white bg-danger rounded">
                                                    <h4 class="mb-0">${result.summary?.reactive || 0}</h4>
                                                    <small>Reactive</small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Results Chart and Table -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title text-center">Results Distribution</h6>
                                        <div class="chart-container">
                                            <canvas id="resultsChart${result.test_type}${index}"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>MiniPool ID</th>
                                                        <th>Well Number</th>
                                                        <th>OD Value</th>
                                                        <th>Timestamp</th>
                                                        <th>Result</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${result.readings?.map(reading => `
                                                        <tr data-status="${reading.category}">
                                                            <td>${reading.sequence_id}</td>
                                                            <td>${reading.well_label}</td>
                                                            <td>${reading.value}</td>
                                                            <td>${reading.timestamp}</td>
                                                            <td class="${reading.category}">
                                                                <span class="badge bg-${reading.category === 'nonreactive' ? 'success' : reading.category === 'borderline' ? 'info' : 'danger'}">
                                                                    ${reading.category.charAt(0).toUpperCase() + reading.category.slice(1)}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    `).join('') || '<tr><td colspan="5" class="text-center">No readings available</td></tr>'}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
        }

        function createCharts(results, container) {
            results.forEach((result, index) => {
                if (result.summary) {
                    new Chart(container.querySelector(`#resultsChart${result.test_type}${index}`), {
                        type: 'doughnut',
                        data: {
                            labels: ['Non-Reactive', 'Borderline', 'Reactive'],
                            datasets: [{
                                data: [
                                    result.summary.nonreactive || 0,
                                    result.summary.borderline || 0,
                                    result.summary.reactive || 0
                                ],
                                backgroundColor: ['#198754', '#0dcaf0', '#dc3545']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 12,
                                        padding: 10
                                    }
                                }
                            }
                        }
                    });
                }
            });
        }
    });
</script>
@endpush
@endsection 