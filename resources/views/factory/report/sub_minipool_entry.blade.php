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
    /* Group of 6 styling instead of 3 */
    .excel-like tr:nth-child(6n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(6n+1) {
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

    /* Results table styles */
    .nonreactive { background-color: #d4edda !important; color: #155724 !important; font-weight: bold; }
    .borderline  { background-color: #fff3cd !important; color: #856404 !important; font-weight: bold; }
    .reactive    { background-color: #f8d7da !important; color: #721c24 !important; font-weight: bold; }
    .control     { background-color: #e0f7fa !important; font-weight: bold !important; }

    .table-sm th,
    .table-sm td {
        padding: 0.25rem;
    }

    .table thead th {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .table tbody td {
        font-size: 0.8rem;
    }

    /* Toast notification styles */
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
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body p-2">
                                    <form id="hbvUploadForm" class="report-upload-form" enctype="multipart/form-data">
                                    @csrf
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="test_type" value="hbv">

                                        <div class="row g-2 mb-2">
                                            <div class="col-md-12">
                                            <div class="form-group">
                                                    <label class="small mb-1">Sub Mini Pool HBV Upload</label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                               class="form-control form-control-sm file-input"
                                                               name="result_file"
                                                               id="hbv_file"
                                                               accept="application/pdf,.pdf"
                                                               required>
                                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                                            <i class="fa fa-folder-open"></i> Browse
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Allowed file types: PDF (.pdf)</small>
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
                        {{-- <div class="col-md-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <form id="hcvUploadForm" class="report-upload-form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="test_type" value="hcv">

                                        <div class="row g-2 mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="small mb-1">HCV Upload</label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                               class="form-control form-control-sm file-input"
                                                               name="result_file"
                                                               id="hcv_file"
                                                               accept="application/pdf,.pdf"
                                                               required>
                                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                                            <i class="fa fa-folder-open"></i> Browse
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Allowed file types: PDF (.pdf)</small>
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
                        </div> --}}
                        {{-- <div class="col-md-4">
                            <div class="card">
                                <div class="card-body p-2">
                                    <form id="hivUploadForm" class="report-upload-form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        <input type="hidden" name="test_type" value="hiv">

                                        <div class="row g-2 mb-2">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="small mb-1">HIV Upload</label>
                                                    <div class="input-group">
                                                        <input type="file"
                                                               class="form-control form-control-sm file-input"
                                                               name="result_file"
                                                               id="hiv_file"
                                                               accept="application/pdf,.pdf"
                                                               required>
                                                        <button class="btn btn-outline-secondary btn-sm browse-btn" type="button">
                                                            <i class="fa fa-folder-open"></i> Browse
                                                        </button>
                                                    </div>
                                                    <small class="text-muted">Allowed file types: PDF (.pdf)</small>
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
                        </div> --}}
                    </div>

                    <!-- Results Section -->
                    <div id="resultsSection" class="mt-4" style="display: none;">
                        <!-- Tab Navigation -->
                        <ul class="nav nav-tabs mb-3" id="resultsTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="hbv-results-tab" data-bs-toggle="tab" data-bs-target="#hbv-results" type="button" role="tab" aria-controls="hbv-results" aria-selected="true">Sub Mini Pool HBV Results</button>
                            {{-- </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="hcv-results-tab" data-bs-toggle="tab" data-bs-target="#hcv-results" type="button" role="tab" aria-controls="hcv-results" aria-selected="false">HCV Results</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="hiv-results-tab" data-bs-toggle="tab" data-bs-target="#hiv-results" type="button" role="tab" aria-controls="hiv-results" aria-selected="false">HIV Results</button> --}}
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="final-results-tab" data-bs-toggle="tab" data-bs-target="#final-results" type="button" role="tab" aria-controls="final-results" aria-selected="false">Sub Mini Pool Results</button>
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
                                                        <th>Sub Mini Pool ID</th>
                                                        <th>Mini Pool Number</th>
                                                        <th>Well Number</th>
                                                        <th>OD Value</th>
                                                        <th>HBV</th>
                                                        {{-- <th>HCV</th>
                                                        <th>HIV</th> --}}
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
<script>
    // Store all results
    let allResults = [];
    let isDataSaved = false;

        $(document).ready(function() {
        // Handle browse button clicks
        $('.browse-btn').on('click', function() {
            const form = $(this).closest('form');
            const fileInput = form.find('input[type="file"]');
            fileInput.click();
        });

        // Handle file input changes
        $('.file-input').on('change', function() {
            const fileName = $(this).val().split('\\').pop();
            if (fileName) {
                $(this).next('.browse-btn').text(fileName);
            }
        });

        // Handle form submissions
        $('.report-upload-form').on('submit', function(e) {
            e.preventDefault();

            const form = $(this);
            const fileInput = form.find('input[type="file"]');
            const progressBar = form.find('.progress');
            const progressBarFill = form.find('.progress-bar');
            const uploadBtn = form.find('.upload-btn');
            const spinner = uploadBtn.find('.spinner-border');

            if (!fileInput.val()) {
                alert('Please select a file to upload');
                return;
            }

            // Show loading indicator and progress bar
            uploadBtn.prop('disabled', true);
            spinner.removeClass('d-none');
            progressBar.show();

            // Simulate progress
            let progress = 0;
            const progressInterval = setInterval(function() {
                progress += 10;
                progressBarFill.css('width', progress + '%').attr('aria-valuenow', progress).text(progress + '%');
                if (progress >= 90) {
                    clearInterval(progressInterval);
                }
            }, 100);

            // Submit form via AJAX
            $.ajax({
                url: '{{ route("subminipool.upload-results") }}',
                method: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                success: function(response) {
                    // Complete progress bar
                    clearInterval(progressInterval);
                    progressBarFill.css('width', '100%').attr('aria-valuenow', 100).text('100%');

                    if (response.status === 'success') {
                        // Show results section
                        $('#resultsSection').show();
                        // Update results table with extracted data
                        updateResultsTable(response.data);

                        // Show success message with extraction details
                        const testType = response.data.test_type.toUpperCase();
                        const extractedCount = response.data.extracted_count || 0;
                        showToast('success', `${testType} results extracted successfully! Found ${extractedCount} samples for viewing.`);

                        // Clear file input
                        fileInput.val('');
                        fileInput.next('.browse-btn').html('<i class="fa fa-folder-open"></i> Browse');
                    } else {
                        showToast('error', 'Error: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    clearInterval(progressInterval);
                    console.error('Error uploading results:', error);
                    console.error('Response:', xhr.responseText);

                    let errorMessage = 'Error uploading results';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Invalid file format or validation error';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error occurred while processing the file';
                    }

                    showToast('error', errorMessage);
                },
                complete: function() {
                    // Reset button and hide progress
                    uploadBtn.prop('disabled', false);
                    spinner.addClass('d-none');
                    setTimeout(function() {
                        progressBar.hide();
                        progressBarFill.css('width', '0%').attr('aria-valuenow', 0).text('0%');
                    }, 1000);
                }
            });
        });
    });

    // Function to update results table
    function updateResultsTable(data) {
        if (data.results && Object.keys(data.results).length > 0) {
            // Convert extracted results to the format expected by populateAllTabs
            const newResults = [];
            Object.keys(data.results).forEach(patientId => {
                // Filter: Only include entries with underscore (sub mini pool entries)
                // Sub mini pools have format like: 250706188801_01, 250805160405_02
                if (!patientId.includes('_')) {
                    return; // Skip entries without underscore (regular patient IDs)
                }

                const result = data.results[patientId];
                // Extract mini pool number (part before underscore)
                // Example: 250706188801_01 -> 250706188801
                const miniPoolNumber = patientId.split('_')[0];

                newResults.push({
                    sub_mini_pool_id: patientId,
                    mini_pool_number: miniPoolNumber,
                    well_num: result.well_num || '',
                    od_value: result.od_value || '',
                    hbv: data.test_type === 'hbv' ? result.result : '',
                    hcv: data.test_type === 'hcv' ? result.result : '',
                    hiv: data.test_type === 'hiv' ? result.result : '',
                    final_result: result.result || ''
                });
            });

            // Merge with existing results instead of replacing them
            if (allResults.length === 0) {
                allResults = newResults;
            } else {
                // Create a map of existing results by patient ID
                const existingResultsMap = {};
                allResults.forEach(result => {
                    existingResultsMap[result.sub_mini_pool_id] = result;
                });

                // Update existing results or add new ones
                newResults.forEach(newResult => {
                    const patientId = newResult.sub_mini_pool_id;
                    if (existingResultsMap[patientId]) {
                        // Update existing result with new test data
                        const existing = existingResultsMap[patientId];
                        existing.well_num = newResult.well_num || existing.well_num;
                        existing.od_value = newResult.od_value || existing.od_value;
                        existing.hbv = newResult.hbv || existing.hbv;
                        existing.hcv = newResult.hcv || existing.hcv;
                        existing.hiv = newResult.hiv || existing.hiv;
                        // Update final_result only if it's not empty
                        if (newResult.final_result) {
                            existing.final_result = newResult.final_result;
                        }
                    } else {
                        // Add new result
                        allResults.push(newResult);
                    }
                });
            }

            populateAllTabs(allResults);
        }
    }

    // Function to populate all tabs
    function populateAllTabs(results) {
        // Populate individual test tabs
        populateTestTab('hbv', results);
        populateTestTab('hcv', results);
        populateTestTab('hiv', results);

        // Populate final results tab
        populateFinalResultsTable(results);
    }

    // Function to populate individual test tabs
    function populateTestTab(testType, results) {
        const contentDiv = $(`#${testType}-results-content`);
        contentDiv.empty();

        // Filter results for this test type
        const testResults = results.filter(result => result[testType] && result[testType] !== '');

        if (testResults.length === 0) {
            contentDiv.html('<div class="alert alert-info">No ' + testType.toUpperCase() + ' results available</div>');
            return;
        }

        // Create table for this test type
        const table = $(`
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Sub Mini Pool ID</th>
                            <th>Mini Pool Number</th>
                            <th>Well Number</th>
                            <th>OD Value</th>
                            <th>${testType.toUpperCase()} Result</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        `);

        const tbody = table.find('tbody');
        testResults.forEach(result => {
            const row = $('<tr>');
            let resultClass = '';
            if (result[testType] === 'reactive') {
                resultClass = 'reactive';
            } else if (result[testType] === 'nonreactive') {
                resultClass = 'nonreactive';
            } else if (result[testType] === 'borderline') {
                resultClass = 'borderline';
            }

            row.append($('<td>').text(result.sub_mini_pool_id || ''));
            row.append($('<td>').text(result.mini_pool_number || ''));
            row.append($('<td>').text(result.well_num || ''));
            row.append($('<td>').text(result.od_value || ''));
            row.append($('<td>').addClass(resultClass).text(result[testType] || ''));

            tbody.append(row);
        });

        contentDiv.append(table);
    }

    // Function to populate final results table
    function populateFinalResultsTable(results) {
        const tbody = $('#finalResultsTable');
        tbody.empty();

        results.forEach(result => {
            const row = $('<tr>');

            // Calculate final result based on all three test types
            let finalResult = 'Nonreactive';
            let resultClass = 'nonreactive';

            // If any test is reactive, final result is reactive
            if (result.hbv === 'reactive' || result.hcv === 'reactive' || result.hiv === 'reactive') {
                finalResult = 'Reactive';
                resultClass = 'reactive';
            }
            // If any test is borderline and none are reactive, final result is borderline
            else if (result.hbv === 'borderline' || result.hcv === 'borderline' || result.hiv === 'borderline') {
                finalResult = 'Borderline';
                resultClass = 'borderline';
            }

            row.append($('<td>').text(result.sub_mini_pool_id || ''));
            row.append($('<td>').text(result.mini_pool_number || ''));
            row.append($('<td>').text(result.well_num || ''));
            row.append($('<td>').text(result.od_value || ''));
            row.append($('<td>').addClass(result.hbv === 'reactive' ? 'reactive' : (result.hbv === 'nonreactive' ? 'nonreactive' : (result.hbv === 'borderline' ? 'borderline' : ''))).text(result.hbv || '-'));
            // row.append($('<td>').addClass(result.hcv === 'reactive' ? 'reactive' : (result.hcv === 'nonreactive' ? 'nonreactive' : (result.hcv === 'borderline' ? 'borderline' : ''))).text(result.hcv || '-'));
            // row.append($('<td>').addClass(result.hiv === 'reactive' ? 'reactive' : (result.hiv === 'nonreactive' ? 'nonreactive' : (result.hiv === 'borderline' ? 'borderline' : ''))).text(result.hiv || '-'));
            row.append($('<td>').addClass(resultClass).text(finalResult));

            tbody.append(row);
        });

        isDataSaved = false; // Reset save status when new data is loaded
    }

    // Save results function
    function saveResults() {
        if (allResults.length === 0) {
            showToast('error', 'No results to save');
            return;
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]')?.content ||
                     document.querySelector('input[name="_token"]')?.value;

        if (!token) {
            showToast('error', 'CSRF token not found');
            return;
        }

        // Show loading state
        const saveButton = document.querySelector('.card-header .btn-sm[onclick*="saveResults"]');
        const originalHtml = saveButton.innerHTML;
        saveButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        saveButton.disabled = true;

        // Send results to server for saving
        fetch('{{ route("subminipool.save-results") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({
                results: allResults,
                _token: token
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                isDataSaved = true;
                showToast('success', `Results saved successfully! ${data.saved_count} entries updated.`);
            } else {
                showToast('error', data.message || 'Failed to save results');
            }
        })
        .catch(error => {
            console.error('Error saving results:', error);
            showToast('error', 'An error occurred while saving results');
        })
        .finally(() => {
            // Restore button state
            saveButton.innerHTML = originalHtml;
            saveButton.disabled = false;
        });
    }

    // Toast notification function (same as ELISA upload)
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

    // Export functions (placeholder)
    function exportToCSV() {
        if (allResults.length === 0) {
            showToast('error', 'No results to export');
            return;
        }
        showToast('info', 'CSV export functionality will be implemented');
    }

    function exportToPDF() {
        if (allResults.length === 0) {
            showToast('error', 'No results to export');
            return;
        }
        showToast('info', 'PDF export functionality will be implemented');
    }
</script>
@endpush
