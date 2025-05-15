@extends('include.dashboardLayout')

@section('title', 'Generate Barcode')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white" style="background-color: #0c4c90;">
            <h5 class="mb-0">Generate Barcode</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="barcodeForm" class="needs-validation" novalidate>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="workstation_id" class="form-label">
                                    <i class="bi bi-pc-display me-1"></i>Workstation ID
                                </label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control" 
                                           id="workstation_id" 
                                           name="workstation_id" 
                                           min="1" 
                                           max="99" 
                                           required
                                           placeholder="Enter ID (1-99)">
                                    <div class="invalid-feedback">Please enter a valid workstation ID (1-99).</div>
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
                                        <div class="card-header text-white" style="background-color: #0c4c90;">
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
                                            <div class="card-header text-white" style="background-color: #f35c24">
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
                        <div class="text-end mb-3">
                            <button type="button" class="btn btn-primary" onclick="saveBarcodes()">
                                <i class="bi bi-printer me-1"></i>Save Barcodes
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="printBarcodes()">
                                <i class="bi bi-printer me-1"></i>Print Barcodes
                            </button>
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
        }
        .no-print {
            display: none !important;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
        }
        .table td {
            border: 1px solid #000 !important;
            padding: 5px 10px !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
        }
        .shadow-sm {
            box-shadow: none !important;
        }
        .row.g-4 {
            gap: 2rem !important;
        }
        #minipoolBarcodes .card {
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch A.R. Numbers and populate dropdown
    fetch('{{ route("barcode.ar-numbers") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const arSelect = document.getElementById('ar_number');
                data.data.forEach(arNumber => {
                    const option = document.createElement('option');
                    option.value = arNumber;
                    option.textContent = arNumber;
                    arSelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error fetching A.R. Numbers:', error);
        });

    const form = document.getElementById('barcodeForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }

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

                // Add mega pool compact barcodes to minipoolBarcodesCompact
                const megapoolCompactHtml = `
                    <div class="col-md-2 col-sm-4 mb-2">
                        <div class="text-center">
                            <div style="font-size: 12px; margin-bottom: 5px;">MEGA POOL No.</div>
                            <div class="text-center py-2">
                                <svg id="megapool_compact_1"></svg>
                            </div>
                            <div class="text-center">
                                <span>${data.megapool}</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 col-sm-4 mb-2">
                        <div class="text-center">
                            <div style="font-size: 12px; margin-bottom: 5px;">MEGA POOL No.</div>
                            <div class="text-center py-2">
                                <svg id="megapool_compact_2"></svg>
                            </div>
                            <div class="text-center">
                                <span>${data.megapool}</span>
                            </div>
                        </div>
                    </div>
                `;

                // Generate mini pool compact barcodes HTML
                const minipoolsCompactHtml = data.minipools.map(minipool => `
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
                `).join('');

                // Combine mega pool and mini pool compact barcodes
                document.getElementById('minipoolBarcodesCompact').innerHTML = megapoolCompactHtml + minipoolsCompactHtml;

                // Generate barcodes for mega pool compact labels
                JsBarcode("#megapool_compact_1", data.megapool, {
                    format: "code128",
                    width: 1,
                    height: 30,
                    displayValue: false,
                    margin: 2,
                    textMargin: 0
                });
                JsBarcode("#megapool_compact_2", data.megapool, {
                    format: "code128",
                    width: 1,
                    height: 30,
                    displayValue: false,
                    margin: 2,
                    textMargin: 0
                });

                // Generate barcodes for mini pool compact labels
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
});

function saveBarcodes() {
    // Get the form data
    const workstationId = document.getElementById('workstation_id').value;
    const arNumber = document.getElementById('ar_number').value;
    const refNumber = document.getElementById('ref_number').value;
    
    // Get the mega pool and mini pool numbers from the page
    const megaPool = document.getElementById('megapoolNumber1').textContent;
    const miniPools = [];
    
    // Get all mini pool numbers
    document.querySelectorAll('#minipoolBarcodes .card').forEach(card => {
        const minipoolNumber = card.querySelector('span').textContent;
        miniPools.push(minipoolNumber);
    });

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
                text: 'Barcodes saved successfully!',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#0c4c90'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Refresh the page after clicking OK
                    window.location.reload();
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

function printBarcodes() {
    window.print();
}
</script>
@endpush
@endsection 