@extends('include.dashboardLayout')

@section('title', 'Generate Barcode')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white" style="background-color: #0c4c90;">
            <ul class="nav nav-tabs card-header-tabs" id="barcodeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active text-white" id="generate-tab" data-bs-toggle="tab" data-bs-target="#generate" type="button" role="tab" aria-controls="generate" aria-selected="true">
                        <i class="bi bi-qr-code me-1"></i>Generate Barcode
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link text-white" id="reprint-tab" data-bs-toggle="tab" data-bs-target="#reprint" type="button" role="tab" aria-controls="reprint" aria-selected="false">
                        <i class="bi bi-printer me-1"></i>Reprint Barcode
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <!-- Tab Content -->
            <div class="tab-content" id="barcodeTabsContent">
                <!-- Generate Barcode Tab -->
                <div class="tab-pane fade show active" id="generate" role="tabpanel" aria-labelledby="generate-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="barcodeForm" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="workstation_id" class="form-label">
                                            <i class="bi bi-pc-display me-1"></i>Workstation ID
                                        </label>
                                        <div class="input-group">
                                            <select class="form-select"
                                                   id="workstation_id"
                                                   name="workstation_id"
                                                   required>
                                                <option value="">Select Workstation ID</option>
                                                @for($i = 1; $i <= $noOfWorkstations; $i++)
                                                    <option value="{{ $i }}">{{ str_pad($i, 2, '0', STR_PAD_LEFT) }}</option>
                                                @endfor
                                            </select>
                                            <div class="invalid-feedback">Please select a Workstation ID.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="ar_number" class="form-label">
                                            <i class="bi bi-hash me-1"></i>A.R. Number
                                        </label>
                                        <div class="input-group">
                                            <select class="form-select"
                                                   id="ar_number"
                                                   name="ar_number"
                                                   required>
                                                <option value="">Select A.R. Number</option>
                                            </select>
                                            <div class="invalid-feedback">Please select A.R. Number.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <label for="ref_number" class="form-label">
                                            <i class="bi bi-file-text me-1"></i>Reference Document No.
                                        </label>
                                        <div class="input-group">
                                            <input type="text"
                                                   class="form-control"
                                                   id="ref_number"
                                                   name="ref_number"
                                                   required
                                                   readonly
                                                   value="{{ $refNumber }}"
                                                   placeholder="Enter Reference Number">
                                            <div class="invalid-feedback">Please enter Reference Number.</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-qr-code me-1"></i>Generate Barcodes
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Reprint Barcode Tab -->
                <div class="tab-pane fade" id="reprint" role="tabpanel" aria-labelledby="reprint-tab">
                    <div class="row">
                        <div class="col-md-12">
                            <form id="reprintForm" class="needs-validation" novalidate>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="mega_pool_select" class="form-label">
                                            <i class="bi bi-upc me-1"></i>Select Mega Pool
                                        </label>
                                        <div class="input-group">
                                            <select class="form-select"
                                                   id="mega_pool_select"
                                                   name="mega_pool_select"
                                                   required>
                                                <option value="">Select Mega Pool</option>
                                            </select>
                                            <div class="invalid-feedback">Please select a Mega Pool.</div>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        {{-- <label for="reprint_type" class="form-label">
                                            <i class="bi bi-tag me-1"></i>Barcode Type
                                        </label>
                                        <div class="input-group">
                                            <select class="form-select"
                                                   id="reprint_type"
                                                   name="reprint_type"
                                                   required>
                                                <option value="">Select Type</option>
                                                <option value="mega">Mega Pool</option>
                                                <option value="mini">Mini Pool</option>
                                                <option value="all">All Barcodes</option>
                                            </select>
                                            <div class="invalid-feedback">Please select Barcode Type.</div>
                                        </div> --}}
                                    </div>
                                    <div class="col-md-4" style="padding-top: 25px;">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-printer me-1"></i>Reprint Barcodes
                                        </button>
                                    </div>
                                </div>

                                {{-- <div class="d-grid gap-2 mt-3">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-printer me-1"></i>Reprint Barcodes
                                        </button>
                                </div> --}}
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        {{-- <div class="card-body"> --}}
            <div class="row">
                <div class="col-md-12">
                    <div id="barcodeResults" style="display: none;">
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 mb-4">
                                        <div class="card-header text-white" style="background-color: #0c4c90; padding: 15px;">
                                            <h6 class="mb-0">Plasma Bag Mega Pool Label</h6>
                                        </div>
                                        <div class="row g-4 my-2 px-4">
                                            <div class="col-md-6">
                                                <div class="card shadow-sm">
                                                    <div class="card-body p-0">
                                                        <table class="table table-bordered mb-0">
                                                            <tr>
                                                                <td colspan="2" class="py-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div style="width: 30%;" class="text-start">
                                                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                                        </div>
                                                                        <div class="flex-grow-1 text-center">Plasma Bag Mega Pool Label</div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                                                <td id="displayArNumber1" class="px-3"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Mega Pool No.</td>
                                                                <td>
                                                                    <div id="megapoolBarcode1" class="text-center py-2"></div>
                                                                    <div class="text-center">
                                                                        <span id="megapoolNumber1"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                                                <td class="py-3"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                                                <td id="displayRefNumber1" class="px-3"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="card shadow-sm">
                                                    <div class="card-body p-0">
                                                        <table class="table table-bordered mb-0">
                                                            <tr>
                                                                <td colspan="2" class="py-2">
                                                                    <div class="d-flex align-items-center">
                                                                        <div style="width: 30%;" class="text-start">
                                                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                                        </div>
                                                                        <div class="flex-grow-1 text-center">Plasma Bag Mega Pool Label</div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                                                <td id="displayArNumber2" class="px-3"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Mega Pool No.</td>
                                                                <td>
                                                                    <div id="megapoolBarcode2" class="text-center py-2"></div>
                                                                    <div class="text-center">
                                                                        <span id="megapoolNumber2"></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                                                <td class="py-3"></td>
                                                            </tr>
                                                            <tr>
                                                                <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                                                <td id="displayRefNumber2" class="px-3"></td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="card shadow-sm">
                                            <div class="card-header text-white" style="background-color: #f35c24; padding: 15px;">
                                                <h6 class="mb-0">Plasma Bag Mini Pool Label</h6>
                                            </div>
                                            <div class="card-body p-0">
                                                <div id="minipoolBarcodes" class="row g-4 my-2 px-4">
                                                    <!-- Mini pool detailed labels will be inserted here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 mt-4">
                                        <div class="card shadow-sm">
                                            <div class="card-body">
                                                <div id="minipoolBarcodesCompact" class="row g-2">
                                                    <!-- Compact barcode labels will be inserted here -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end mb-3 d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-primary" onclick="saveBarcodes()" id="saveBarcodesBtn">
                                <i class="bi bi-printer me-1"></i>Save Barcodes
                            </button>
                            <div id="printButtonContainer" class="d-inline-block">
                                <!-- Generate tab print button -->
                                <button type="button" class="btn btn-secondary" id="printBarcodesBtn" disabled>
                                    <i class="bi bi-printer me-1"></i>Print Barcodes
                                </button>
                                <!-- Reprint tab print button -->
                                <button type="button" class="btn btn-secondary" id="reprintBarcodesBtn" style="display: none;">
                                    <i class="bi bi-printer me-1"></i>Print Barcodes
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            {{-- </div> --}}
        </div>
    </div>
</div>

@push('styles')
<style>
    .barcode-container {
        padding: 15px;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        background-color: #fff;
    }
    .table {
        margin-bottom: 0;
    }
    .table td {
        padding: 8px 15px;
        vertical-align: middle;
        font-size: 14px;
    }
    .table tr:first-child td {
        border-top: none;
    }
    #megapoolBarcode1 svg, #megapoolBarcode2 svg {
        max-width: 100%;
        height: auto;
    }
    .shadow-sm {
        box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
    }
    #minipoolBarcodes .card {
        border: 1px solid #dee2e6;
    }
    #minipoolBarcodes .card-body {
        min-height: 100px;
    }

    /* Header Tabs Styles */
    .card-header-tabs {
        margin: 0;
        border-bottom: none;
    }
    .card-header-tabs .nav-link {
        border: none;
        color: rgba(255, 255, 255, 0.8);
        padding: 0.75rem 1.25rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    .card-header-tabs .nav-link:hover {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.1);
    }
    .card-header-tabs .nav-link.active {
        color: #fff;
        background-color: rgba(255, 255, 255, 0.2);
        border-bottom: 2px solid #fff;
    }
    .card-header {
        padding: 0;
    }

    @media print {
        body * {
            visibility: hidden;
        }
        #barcodeResults, #barcodeResults * {
            visibility: visible;
        }
        #barcodeResults {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            margin: 0;
            padding: 0;
        }
        .no-print {
            display: none !important;
        }
        /* Hide the buttons container */
        .text-end.mb-3 {
            display: none !important;
        }
        /* Hide outer card structure */
        .container-fluid > .card {
            border: none !important;
            box-shadow: none !important;
            background: none !important;
        }
        .container-fluid > .card > .card-header {
            display: none !important;
        }
        .container-fluid > .card > .card-body {
            padding: 0 !important;
            background: none !important;
        }
        /* Keep inner barcode cards */
        #barcodeResults .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin: 0 0 10px 0 !important;
            padding: 0 !important;
            page-break-inside: avoid;
            background: #fff !important;
        }
        #barcodeResults .card-header {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #000 !important;
            padding: 5px 10px !important;
            font-size: 12px !important;
        }
        #barcodeResults .card-body {
            padding: 5px !important;
            background: #fff !important;
        }
        .table {
            border: 1px solid #000 !important;
            margin: 0 !important;
        }
        .table td {
            border: 1px solid #000 !important;
            padding: 3px 5px !important;
            background: #fff !important;
            font-size: 11px !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
        }
        .shadow-sm {
            box-shadow: none !important;
        }
        .row.g-4 {
            gap: 10px !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        #minipoolBarcodes .card {
            border: 1px solid #000 !important;
            page-break-inside: avoid;
            background: #fff !important;
        }
        .col-md-6, .col-md-2, .col-sm-4 {
            padding: 5px !important;
        }
        .my-2 {
            margin: 5px 0 !important;
        }
        .px-4 {
            padding: 0 5px !important;
        }
        .mb-3, .mb-4 {
            margin-bottom: 10px !important;
        }
        .py-2 {
            padding: 3px 0 !important;
        }
        .py-3 {
            padding: 5px 0 !important;
        }
        .px-3 {
            padding: 0 5px !important;
        }
        @page {
            margin: 0.5cm;
            size: auto;
        }
        /* Adjust barcode size */
        svg {
            max-width: 100% !important;
            height: auto !important;
        }
        /* Ensure text is visible */
        td {
            color: #000 !important;
        }
        /* Logo adjustments */
        img {
            max-height: 25px !important;
            width: auto !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
// Add this debug console log function to help track the issue
function logDebug(message, data) {
    console.log(`[DEBUG] ${message}`, data);
}

// Define these functions globally
function saveBarcodes() {
    // Get the form data
    const workstationId = document.getElementById('workstation_id').value;
    const arNumber = document.getElementById('ar_number').value;
    const refNumber = document.getElementById('ref_number').value;

    // Get the mega pool and mini pool numbers from the page
    const megaPool = document.getElementById('megapoolNumber1').textContent;
    const miniPools = [];

    // Get all mini pool numbers
    document.querySelectorAll('#minipoolBarcodes .card span').forEach(span => {
        const minipoolNumber = span.textContent;
        if (minipoolNumber && minipoolNumber.trim() !== '') {
            miniPools.push(minipoolNumber);
        }
    });

    // Check if we have the minimum required data
    if (!workstationId || !arNumber || !refNumber || !megaPool || miniPools.length === 0) {
        Swal.fire({
            title: 'Incomplete Data',
            text: 'Please ensure all required fields are filled and barcodes are generated.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0c4c90'
        });
        return;
    }

    // Send data to server
    fetch('{{ route("barcode.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            workstation_id: workstationId,
            ar_number: arNumber,
            ref_number: refNumber,
            mega_pool: megaPool,
            mini_pools: miniPools
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: 'Success!',
                text: 'Barcodes saved successfully! You can now print the barcodes.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0c4c90'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enable print button and hide save button
                    document.getElementById('printBarcodesBtn').disabled = false;
                    document.getElementById('saveBarcodesBtn').style.display = 'none';
                }
            });
        } else {
            Swal.fire({
                title: 'Error!',
                text: 'Error saving barcodes: ' + data.message,
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0c4c90'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            title: 'Error!',
            text: 'An error occurred while saving barcodes.',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#0c4c90'
        });
    });
}

function numberToWord(num) {
    const words = ['ZERO', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN'];
    return words[num] || num.toString();
}

function downloadCsv(fileType = 'MEGA', isReprint = false) {
    // Prevent the default action
    event.preventDefault();
    event.stopPropagation();

    // Check if results are showing and contains data
    const resultsVisible = document.getElementById('barcodeResults').style.display === 'block';

    if (!resultsVisible) {
        Swal.fire({
            title: 'No Barcodes Found',
            text: 'Please generate or reprint barcodes first before downloading CSV.',
            icon: 'warning',
            confirmButtonText: 'OK'
        });
        return;
    }

    // Show confirmation dialog before proceeding
    Swal.fire({
        title: 'Confirm Download',
        text: 'You can only download once after saving. Do you want to proceed?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Download',
        cancelButtonText: 'No, Cancel',
        confirmButtonColor: '#0c4c90'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get data from the displayed barcodes
            let arNumber, refNumber, megaPool, miniPools = [];

            // Get data from the page
            arNumber = document.getElementById('displayArNumber1').textContent || '';
            refNumber = document.getElementById('displayRefNumber1').textContent || '';
            megaPool = document.getElementById('megapoolNumber1').textContent || '';

            // Find all mini pool barcodes
            const minipoolElements = document.querySelectorAll('#minipoolBarcodes .card span');
            if (minipoolElements && minipoolElements.length > 0) {
                miniPools = Array.from(minipoolElements).map(el => el.textContent).filter(text => text.trim() !== '');
            }

            // Check if we have the minimum required data
            if (!arNumber || !refNumber || !megaPool || miniPools.length === 0) {
                Swal.fire({
                    title: 'Incomplete Data',
                    text: 'Could not find all required barcode data. Please regenerate barcodes.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading indicator
            const loadingIndicator = document.createElement('div');
            loadingIndicator.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; z-index: 9999;"><div style=""><img src="{{ asset('assets/img/printloader.gif') }}" alt="Loading" width="200" height="200"></div></div>';
            document.body.appendChild(loadingIndicator);

            // Create and submit form for download
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("bartender.download-csv") }}';
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);

            // Add data fields
            const fields = {
                ar_number: arNumber,
                ref_number: refNumber,
                mega_pool: megaPool,
                file_type: 'MEGA',
                print_number: 1,
                print_word: isReprint ? 'reprint' : 'print'  // Set value based on isReprint flag
            };

            // Add all fields to form
            Object.entries(fields).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            });

            // Add mini pools
            miniPools.forEach((miniPool, index) => {
                const miniPoolInput = document.createElement('input');
                miniPoolInput.type = 'hidden';
                miniPoolInput.name = `mini_pools[${index}]`;
                miniPoolInput.value = miniPool;
                form.appendChild(miniPoolInput);
            });

            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();

            // Remove the form after submission
            setTimeout(() => {
                document.body.removeChild(form);
                document.body.removeChild(loadingIndicator);

                // Disable print buttons after successful download
                const printBarcodesBtn = document.getElementById('printBarcodesBtn');
                const reprintBarcodesBtn = document.getElementById('reprintBarcodesBtn');

                if (printBarcodesBtn) printBarcodesBtn.disabled = true;
                if (reprintBarcodesBtn) reprintBarcodesBtn.disabled = true;

                // Show message about generating new barcodes
                Swal.fire({
                    title: 'Download Complete',
                    text: 'To download again, please generate or reprint new barcodes.',
                    icon: 'success',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
            }, 2000);
        }
    });
}

// FIX: Only one document.addEventListener here, not duplicated
document.addEventListener('DOMContentLoaded', function() {
    // Preload animation GIF
    const preloadImage = new Image();
    preloadImage.src = "{{ asset('assets/img/animation.gif') }}";

    // Setup tabs, existing events, etc.

    // Add tab switching event listener
    const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabButtons.forEach(button => {
        button.addEventListener('shown.bs.tab', function (e) {
            // Clear barcode results when switching tabs
            document.getElementById('barcodeResults').style.display = 'none';
            document.getElementById('megapoolBarcode1').innerHTML = '';
            document.getElementById('megapoolBarcode2').innerHTML = '';
            document.getElementById('minipoolBarcodes').innerHTML = '';
            document.getElementById('minipoolBarcodesCompact').innerHTML = '';
            document.getElementById('displayArNumber1').textContent = '';
            document.getElementById('displayArNumber2').textContent = '';
            document.getElementById('displayRefNumber1').textContent = '';
            document.getElementById('displayRefNumber2').textContent = '';
            document.getElementById('megapoolNumber1').textContent = '';
            document.getElementById('megapoolNumber2').textContent = '';

            // Handle buttons based on active tab
            const saveBtn = document.getElementById('saveBarcodesBtn');
            const printBtn = document.getElementById('printBarcodesBtn');
            const reprintBtn = document.getElementById('reprintBarcodesBtn');

            if (e.target.id === 'generate-tab') {
                saveBtn.style.display = 'inline-block';
                printBtn.style.display = 'inline-block';
                reprintBtn.style.display = 'none';
            } else {
                saveBtn.style.display = 'none';
                printBtn.style.display = 'none';
                reprintBtn.style.display = 'inline-block';
            }
        });
    });

    // Initially set up buttons for Generate tab
    document.getElementById('saveBarcodesBtn').style.display = 'inline-block';
    document.getElementById('printBarcodesBtn').style.display = 'inline-block';
    document.getElementById('reprintBarcodesBtn').style.display = 'none';

    // Add click handler for print buttons
    const printBarcodesBtn = document.getElementById('printBarcodesBtn');
    if (printBarcodesBtn) {
        printBarcodesBtn.addEventListener('click', function() {
            downloadCsv('MEGA', false);  // false for normal print
        });
    }

    const reprintBarcodesBtn = document.getElementById('reprintBarcodesBtn');
    if (reprintBarcodesBtn) {
        reprintBarcodesBtn.addEventListener('click', function() {
            const megaPoolSelect = document.getElementById('mega_pool_select');
            const selectedOption = megaPoolSelect.options[megaPoolSelect.selectedIndex];

            if (!selectedOption || !selectedOption.value) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select a Mega Pool first.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
                return;
            }

            downloadCsv('MEGA', true);  // true for reprint
        });
    }

    // Add event listener for mega pool selection in Reprint tab
    const megaPoolSelect = document.getElementById('mega_pool_select');
    if (megaPoolSelect) {
        megaPoolSelect.addEventListener('change', function() {
            const printBtn = document.getElementById('printBarcodesBtn');
            if (this.value) {
                printBtn.disabled = false; // Enable print button when mega pool is selected
            } else {
                printBtn.disabled = true; // Disable if no mega pool selected
            }
        });
    }

    // Fetch A.R. Numbers and populate dropdown
    const arSelect = document.getElementById('ar_number');
    if (arSelect) {
        logDebug('Fetching A.R. Numbers...');
        fetch('{{ route("barcode.ar-numbers") }}')
            .then(response => {
                logDebug('A.R. Numbers response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                logDebug('A.R. Numbers data received:', data);
                if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                    // Clear existing options except the first one
                    while (arSelect.options.length > 1) {
                        arSelect.remove(1);
                    }

                    // Add new options
                    data.data.forEach(arNumber => {
                        const option = document.createElement('option');
                        option.value = arNumber;
                        option.textContent = arNumber;
                        arSelect.appendChild(option);
                    });

                    logDebug('A.R. Numbers dropdown populated with', data.data.length, 'options');
                } else {
                    console.warn('No A.R. Numbers found or invalid response format');
                }
            })
            .catch(error => {
                console.error('Error fetching A.R. Numbers:', error);
                // Show error to user
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load A.R. Numbers: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
            });
    } else {
        console.error('A.R. Number dropdown element not found');
    }

    // Fetch Mega Pools for reprint dropdown
    if (megaPoolSelect) {
        logDebug('Fetching Mega Pools...');
        fetch('{{ route("barcode.mega-pools") }}')
            .then(response => {
                logDebug('Mega Pools response status:', response.status);
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                logDebug('Mega Pools data received:', data);
                if (data.success && Array.isArray(data.data) && data.data.length > 0) {
                    // Clear existing options except the first one
                    while (megaPoolSelect.options.length > 1) {
                        megaPoolSelect.remove(1);
                    }

                    // Add new options
                    data.data.forEach(entry => {
                        const option = document.createElement('option');
                        option.value = entry.mega_pool_no;
                        option.textContent = entry.mega_pool_no;
                        option.dataset.arNo = entry.ar_no;
                        option.dataset.refNo = entry.ref_doc_no;
                        megaPoolSelect.appendChild(option);
                    });

                    logDebug('Mega Pools dropdown populated with', data.data.length, 'options');
                } else {
                    console.warn('No Mega Pools found or invalid response format');
                }
            })
            .catch(error => {
                console.error('Error fetching Mega Pools:', error);
                // Show error to user
                Swal.fire({
                    title: 'Error!',
                    text: 'Failed to load Mega Pools: ' + error.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
            });
    }

    const form = document.getElementById('barcodeForm');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

        // Disable print button when form is submitted
        document.getElementById('printBarcodesBtn').disabled = true;

        const formData = {
            ar_number: document.getElementById('ar_number').value,
            ref_number: document.getElementById('ref_number').value,
            workstation_id: document.getElementById('workstation_id').value
        };

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generating...';
        submitBtn.disabled = true;

        fetch('{{ route("barcode.generate.codes") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show results section
                document.getElementById('barcodeResults').style.display = 'block';

                // Display AR Number and Ref Number
                document.getElementById('displayArNumber1').textContent = formData.ar_number;
                document.getElementById('displayArNumber2').textContent = formData.ar_number;
                document.getElementById('displayRefNumber1').textContent = formData.ref_number;
                document.getElementById('displayRefNumber2').textContent = formData.ref_number;

                // Generate Mega Pool barcodes
                document.getElementById('megapoolBarcode1').innerHTML = `<svg id="megapool1"></svg>`;
                document.getElementById('megapoolNumber1').textContent = data.megapool;
                JsBarcode("#megapool1", data.megapool, {
                    format: "code128",
                    width: 1,
                    height: 40,
                    displayValue: true,
                    fontSize: 10,
                    margin: 2,
                    textMargin: 2
                });

                document.getElementById('megapoolBarcode2').innerHTML = `<svg id="megapool2"></svg>`;
                document.getElementById('megapoolNumber2').textContent = data.megapool;
                JsBarcode("#megapool2", data.megapool, {
                    format: "code128",
                    width: 1,
                    height: 40,
                    displayValue: true,
                    fontSize: 10,
                    margin: 2,
                    textMargin: 2
                });

                // Generate Mini Pool detailed labels
                const minipoolsHtml = data.minipools.map(minipool => `
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td colspan="2" class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30%;" class="text-start">
                                                    <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                </div>
                                                <div class="flex-grow-1 text-center">Plasma Bag Mini Pool Label</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                        <td class="px-3">${formData.ar_number}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Mini Pool No.</td>
                                        <td>
                                            <div class="text-center py-2">
                                                <svg id="minipool_detailed_${minipool}"></svg>
                                            </div>
                                            <div class="text-center">
                                                <span>${minipool}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                        <td class="py-3"></td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                        <td class="px-3">${formData.ref_number}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                `).join('');

                document.getElementById('minipoolBarcodes').innerHTML = minipoolsHtml;

                // Generate barcodes for detailed mini pool labels
                data.minipools.forEach(minipool => {
                    JsBarcode(`#minipool_detailed_${minipool}`, minipool, {
                        format: "code128",
                        width: 2,
                        height: 50,
                        displayValue: false,
                        margin: 2,
                        textMargin: 0
                    });
                });

                // Add compact barcodes
                const compactHtml = `
                    <div class="col-md-2 col-sm-4 mb-2">
                        <div class="text-center">
                            <div style="font-size: 12px; margin-bottom: 5px;">MEGA POOL No.</div>
                            <div class="text-center py-2">
                                <svg id="megapool_compact"></svg>
                            </div>
                            <div class="text-center">
                                <span>${data.megapool}</span>
                            </div>
                        </div>
                    </div>
                    ${data.minipools.map(minipool => `
                        <div class="col-md-2 col-sm-4 mb-2">
                            <div class="text-center">
                                <div style="font-size: 12px; margin-bottom: 5px;">MINI POOL No.</div>
                                <div class="text-center py-2">
                                    <svg id="minipool_compact_${minipool}"></svg>
                                </div>
                                <div class="text-center">
                                    <span>${minipool}</span>
                                </div>
                            </div>
                        </div>
                    `).join('')}`;

                document.getElementById('minipoolBarcodesCompact').innerHTML = compactHtml;

                // Generate barcodes for compact labels
                // Generate mega pool barcode
                JsBarcode(`#megapool_compact`, data.megapool, {
                    format: "code128",
                    width: 1,
                    height: 30,
                    displayValue: false,
                    margin: 2,
                    textMargin: 0
                });

                // Generate mini pool barcodes
                data.minipools.forEach(minipool => {
                    JsBarcode(`#minipool_compact_${minipool}`, minipool, {
                        format: "code128",
                        width: 1,
                        height: 30,
                        displayValue: false,
                        margin: 2,
                        textMargin: 0
                    });
                });

                // Scroll to results
                document.getElementById('barcodeResults').scrollIntoView({ behavior: 'smooth' });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while generating barcodes.');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // New code for Reprint Barcode tab
    const reprintForm = document.getElementById('reprintForm');

    reprintForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (!reprintForm.checkValidity()) {
            e.stopPropagation();
            reprintForm.classList.add('was-validated');
            return;
        }

        const selectedOption = megaPoolSelect.options[megaPoolSelect.selectedIndex];
        const formData = {
            ar_number: selectedOption.dataset.arNo,
            ref_number: selectedOption.dataset.refNo,
            mega_pool: selectedOption.value,
            mini_pools: [] // Will be populated from the API response
        };

        // Show loading state
        const submitBtn = reprintForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
        submitBtn.disabled = true;

        fetch('{{ route("barcode.reprint") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update formData with mini pools from response
                const printData = {
                    ar_number: data.ar_number,
                    ref_number: data.ref_number,
                    mega_pool: data.megapool,
                    mini_pools: data.minipools
                };

                // Display formatted data in the UI without showing printer dialog
                displayBarcodeResults(data);

                // Enable the print button for direct printing
                const printBtn = document.getElementById('printBarcodesBtn');
                printBtn.disabled = false;
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message,
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0c4c90'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred while reprinting barcodes.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0c4c90'
            });
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // Add new function for printing reprinted barcodes
    function printReprintBarcodes(arNumber, refNumber, megaPool, miniPools) {
        // Show a loading indicator
        const loadingIndicator = document.createElement('div');
        loadingIndicator.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; z-index: 9999;"><div style=""><img src="{{ asset('assets/img/printloader.gif') }}" alt="Loading" width="200" height="200"></div></div>';
        document.body.appendChild(loadingIndicator);

        // Prepare data for label printing
        const data = {
            ar_number: arNumber,
            ref_number: refNumber,
            mega_pool: megaPool,
            mini_pools: miniPools
        };

        // Use the same server-side preview endpoint for reprints
        fetch('{{ route("labels.preview") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(html => {
            // Remove loading indicator
            document.body.removeChild(loadingIndicator);

            // Open preview in new window
            const printWindow = window.open('', '_blank');
            if (printWindow) {
                // Use the server-generated HTML directly
                printWindow.document.write(html);
                printWindow.document.close();
                console.log('Reprint preview window opened with server-rendered content');

                // Wait for window to load before opening print dialog
                printWindow.onload = function() {
                    printWindow.print();
                    printWindow.onafterprint = function() {
                        printWindow.close();
                    };
                };
            } else {
                alert('Please allow pop-ups to view the print preview');
            }
        })
        .catch(error => {
            // Remove loading indicator
            document.body.removeChild(loadingIndicator);
            console.error('Error:', error);
            alert('An error occurred while generating reprint preview: ' + error.message);
        });
    }

    // Remove the onclick binding for the print button
    // Find and update the existing onclick binding for the print button in the existing code
    document.getElementById('printBarcodesBtn').onclick = null;

    // Add click handlers for dropdown items
    document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const copies = this.getAttribute('data-copies');
            downloadCsv('MEGA', false); // false for normal print
        });
    });

    // Add event listener for print format change
    document.getElementById('printFormat').addEventListener('change', function() {
        const format = this.value;
        const printServerLabel = document.querySelector('label[for="usePrintServerOption"]');

        if (format === 'PGL') {
            // Update print server label for PGL
            printServerLabel.textContent = 'Use Print Server for PGL printing';

            // Update print server URL placeholder if it exists
            const printServerUrl = document.getElementById('printServerUrl');
            if (printServerUrl) {
                printServerUrl.placeholder = 'e.g., http://print-server-ip:8081/print-server.php';
            }
        } else {
            // Reset to default ZPL text
            printServerLabel.textContent = 'Use Print Server for ZPL printing';

            // Reset print server URL placeholder
            const printServerUrl = document.getElementById('printServerUrl');
            if (printServerUrl) {
                printServerUrl.placeholder = 'e.g., http://print-server-ip:8081/print-server.php';
            }
        }
    });

    // Add handler for the "Use Print Server" checkbox
    document.getElementById('usePrintServerOption').addEventListener('change', function() {
        const printServerContainer = document.getElementById('printServerUrlContainer');
        if (this.checked) {
            printServerContainer.style.display = 'block';
        } else {
            printServerContainer.style.display = 'none';
        }
    });

    // Helper function to display barcode results on screen
    function displayBarcodeResults(data) {
        // Show results section
        document.getElementById('barcodeResults').style.display = 'block';

        // Display AR Number and Ref Number
        document.getElementById('displayArNumber1').textContent = data.ar_number;
        document.getElementById('displayArNumber2').textContent = data.ar_number;
        document.getElementById('displayRefNumber1').textContent = data.ref_number;
        document.getElementById('displayRefNumber2').textContent = data.ref_number;

        // Generate Mega Pool barcodes
        document.getElementById('megapoolBarcode1').innerHTML = `<svg id="megapool1"></svg>`;
        document.getElementById('megapoolNumber1').textContent = data.megapool;
        JsBarcode("#megapool1", data.megapool, {
            format: "code128",
            width: 1,
            height: 40,
            displayValue: true,
            fontSize: 10,
            margin: 2,
            textMargin: 2
        });

        document.getElementById('megapoolBarcode2').innerHTML = `<svg id="megapool2"></svg>`;
        document.getElementById('megapoolNumber2').textContent = data.megapool;
        JsBarcode("#megapool2", data.megapool, {
            format: "code128",
            width: 1,
            height: 40,
            displayValue: true,
            fontSize: 10,
            margin: 2,
            textMargin: 2
        });

        // Generate Mini Pool detailed labels
        const minipoolsHtml = data.minipools.map(minipool => `
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0">
                            <tr>
                                <td colspan="2" class="py-2">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 30%;" class="text-start">
                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                        </div>
                                        <div class="flex-grow-1 text-center">Plasma Bag Mini Pool Label</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                <td class="px-3">${data.ar_number}</td>
                            </tr>
                            <tr>
                                <td style="background-color: #f8f9fa;">Mini Pool No.</td>
                                <td>
                                    <div class="text-center py-2">
                                        <svg id="minipool_detailed_${minipool}"></svg>
                                    </div>
                                    <div class="text-center">
                                        <span>${minipool}</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                <td class="py-3"></td>
                            </tr>
                            <tr>
                                <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                <td class="px-3">${data.ref_number}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        `).join('');

        document.getElementById('minipoolBarcodes').innerHTML = minipoolsHtml;

        // Generate barcodes for detailed mini pool labels
        data.minipools.forEach(minipool => {
            JsBarcode(`#minipool_detailed_${minipool}`, minipool, {
                format: "code128",
                width: 2,
                height: 50,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
        });

        // Add compact barcodes
        const compactHtml = `
            <div class="col-md-2 col-sm-4 mb-2">
                <div class="text-center">
                    <div style="font-size: 12px; margin-bottom: 5px;">MEGA POOL No.</div>
                    <div class="text-center py-2">
                        <svg id="megapool_compact"></svg>
                    </div>
                    <div class="text-center">
                        <span>${data.megapool}</span>
                    </div>
                </div>
            </div>
            ${data.minipools.map(minipool => `
                <div class="col-md-2 col-sm-4 mb-2">
                    <div class="text-center">
                        <div style="font-size: 12px; margin-bottom: 5px;">MINI POOL No.</div>
                        <div class="text-center py-2">
                            <svg id="minipool_compact_${minipool}"></svg>
                        </div>
                        <div class="text-center">
                            <span>${minipool}</span>
                        </div>
                    </div>
                </div>
            `).join('')}`;

        document.getElementById('minipoolBarcodesCompact').innerHTML = compactHtml;

        // Generate barcodes for compact labels
        // Generate mega pool barcode
        JsBarcode(`#megapool_compact`, data.megapool, {
            format: "code128",
            width: 1,
            height: 30,
            displayValue: false,
            margin: 2,
            textMargin: 0
        });

        // Generate mini pool barcodes
        data.minipools.forEach(minipool => {
            JsBarcode(`#minipool_compact_${minipool}`, minipool, {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
        });

        // Scroll to results
        document.getElementById('barcodeResults').scrollIntoView({ behavior: 'smooth' });
    }
}); // Close the document.addEventListener that started on line 467

// Add this new code after your existing document.addEventListener('DOMContentLoaded', function() {
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers for dropdown items
    document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const copies = this.getAttribute('data-copies');
            downloadCsv('MEGA', false); // false for normal print
        });
    });

    // Rest of your existing DOMContentLoaded code...
});

// For CSS missing file
document.addEventListener('DOMContentLoaded', function() {
    // Check if any 404 errors are related to select2-override.css
    console.warn("If you see a 404 error for select2-override.css, you can ignore it or create this file in the /public/css directory.");
});
</script>
@endpush
@endsection
