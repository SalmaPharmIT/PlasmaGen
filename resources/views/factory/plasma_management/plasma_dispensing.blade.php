@extends('include.dashboardLayout')

@section('title', 'Plasma Dispensing')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card {
        margin: 0.5rem;
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
    .excel-like th {
        background-color: #f8f9fa;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.2rem;
        white-space: normal;
        vertical-align: middle;
        line-height: 1.2;
        text-align: center;
    }
    .excel-like td {
        padding: 0;
        height: 24px;
        vertical-align: middle;
        position: relative;
    }
    .excel-like input {
        background: transparent;
        width: 100%;
        border: none;
        padding: 0.1rem 0.2rem;
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
    label {
        font-size: 0.8rem;
        font-weight: 500;
        color: #495057;
        margin-bottom: 0.3rem;
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
    }
    .select2-container--default .select2-selection--single {
        height: 32px !important;
        min-height: 32px !important;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px !important;
        padding-left: 12px !important;
        font-size: 0.9rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 32px !important;
    }
    .form-row {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 1.5rem;
        padding: 0 15px;
    }

    .form-group {
        margin-bottom: 0;
    }

    .form-group.blood-centre {
        flex: 2;
    }

    .form-group.date {
        flex: 1;
    }

    .form-group.actions {
        flex: 0 0 auto;
        margin-left: auto;
    }

    .form-control, .select2-container {
        width: 100%;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 0;
    }

    .btn-export {
        background-color: #28a745;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-export:hover {
        background-color: #218838;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn-print {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
    }

    .btn-print:hover {
        background-color: #0056b3;
        transform: translateY(-1px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .btn i {
        font-size: 16px;
    }

    @media print {
        .btn {
            display: none !important;
        }
        .card {
            border: none !important;
        }
        .card-body {
            padding: 0 !important;
        }
        .table {
            border-collapse: collapse !important;
        }
        .table td, .table th {
            border: 1px solid #000 !important;
        }
    }
</style>
@endpush

@push('head')
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
@endpush

@section('content')
<div class="card">
    <div class="card-header text-white" style="background-color: #0c4c90;">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-center mb-0">Plasma Despense Summary</h4>
            <div class="header-actions">
                <button type="button" class="btn-action" onclick="exportToExcel()" title="Export to Excel">
                    <i class="fa-solid fa-file-excel"></i>
                    Export
                </button>
                <button type="button" class="btn-action" onclick="printForm()" title="Print List">
                    <i class="fa-solid fa-print"></i>
                    Print
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">

        <form id="plasmaDispenseForm">
            <div class="form-row">
                <div class="col-md-4">
                    <label for="batch_number" class="form-label">
                        <i class="bi bi-hash me-1"></i>Batch No
                    </label>
                    <div class="input-group">
                        <select class="form-select"
                               id="batch_number"
                               name="batch_number"
                               required>
                            <option value="">Select Batch No</option>
                        </select>
                        <div class="invalid-feedback">Please select Batch No.</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered excel-like">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 10%;">A.R. No.</th>
                            <th rowspan="2" style="width: 20%;">Mega Pool No./ Mini Pool No./ Donor ID</th>
                            <th rowspan="2" style="width: 15%;">Requested Valume</th>
                            <th rowspan="2" style="width: 15%;">Issued Valume</th>
                            <th rowspan="2" style="width: 15%;">Dispensed By Sign/ Date<br>(Warehouse)</th>
                        </tr>
                    </thead>
                    <tbody id="plasmaTableBody">
                        {{-- <tr>
                            <td rowspan="8" id="arNoCell"></td>
                            <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                            <td><input type="text" class="form-control-sm" name="requested_volume[]"></td>
                            <td><input type="text" class="form-control-sm" name="issued_volume[]"></td>
                            <td><input type="text" class="form-control-sm" name="dispensed_by[]"></td>
                        </tr>
                        @for ($i = 0; $i < 7; $i++)
                        <tr>
                            <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                            <td><input type="text" class="form-control-sm" name="requested_volume[]"></td>
                            <td><input type="text" class="form-control-sm" name="issued_volume[]"></td>
                            <td><input type="text" class="form-control-sm" name="dispensed_by[]"></td>
                        </tr> --}}
                        {{-- @endfor --}}
                    </tbody>
                </table>
            </div>

            <div class="row mt-5">
                <div class="col-12">
                    <table class="table table-borderless" style="width: 100%;">
                        <tr>
                            <td style="width: 50%; padding-left: 50px;">
                                <div style="margin-bottom: 30px;">
                                    <div style="margin-bottom: 10px;">Done By:</div>
                                    <div style="margin-bottom: 40px; border-bottom: 1px solid #000;"></div>
                                    <div style="margin-bottom: 10px;">Signature:</div>
                                    <div style="border-bottom: 1px solid #000;"></div>
                                </div>
                            </td>
                            <td style="width: 50%; padding-left: 50px;">
                                <div style="margin-bottom: 30px;">
                                    <div style="margin-bottom: 10px;">Reviewed By:</div>
                                    <div style="margin-bottom: 40px; border-bottom: 1px solid #000;"></div>
                                    <div style="margin-bottom: 10px;">Signature:</div>
                                    <div style="border-bottom: 1px solid #000;"></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Load SheetJS library dynamically
    function loadSheetJS() {
        return new Promise((resolve, reject) => {
            if (window.XLSX) {
                resolve(window.XLSX);
                return;
            }

            const script = document.createElement('script');
            script.src = 'https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js';
            script.onload = () => resolve(window.XLSX);
            script.onerror = () => reject(new Error('Failed to load SheetJS library'));
            document.head.appendChild(script);
        });
    }

    $(document).ready(function() {
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 for Batch Number dropdown
        $('#batch_number').select2({
            placeholder: "Select Batch Number",
            allowClear: true,
            width: '100%',
            dropdownCssClass: 'select2-dropdown-large',
            theme: 'default'
        });

        // Fetch Batch Numbers and populate dropdown
        $.ajax({
            url: '{{ route("plasma.get-by-batch-number", ["batch_number" => "all"]) }}',
            method: 'GET',
            success: function(response) {
                if (response.status === 'success' && Array.isArray(response.data) && response.data.length > 0) {
                    // Clear existing options except the first one
                    $('#batch_number option:not(:first)').remove();

                    // Add new options
                    response.data.forEach(function(item) {
                        $('#batch_number').append(new Option(item.batch_no, item.batch_no));
                    });
                } else {
                    console.warn('No Batch Numbers found or invalid response format');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching Batch Numbers:', error);
            }
        });

        // Handle Batch number selection
        $('#batch_number').on('change', function() {
            const batchNo = $(this).val();

            if (!batchNo) {
                // Clear the table if no batch number is selected
                resetTableData();
                return;
            }

            // Make AJAX call to get data by batch number
            $.ajax({
                url: '{{ route("plasma.get-by-batch-number", ["batch_number" => "__BATCH_NO__"]) }}'.replace('__BATCH_NO__', encodeURIComponent(batchNo)),
                method: 'GET',
                success: function(response) {
                    console.log("response");
                    console.log(response);
                    if (response.status === 'success') {
                        populateTableWithData(response.data, batchNo);
                    } else {
                        console.error('Error fetching data for batch number:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX error fetching data for batch number:', error);
                }
            });
        });

        // Function to reset table data
        function resetTableData() {
            $('#arNoCell').text('');
            $('#plasmaTableBody tr').each(function() {
                $(this).find('input[name="pool_no[]"]').val('');
                $(this).find('input[name="requested_volume[]"]').val('');
                $(this).find('input[name="issued_volume[]"]').val('');
                $(this).find('input[name="dispensed_by[]"]').val('');
            });
        }

        // Function to populate table with data
        function populateTableWithData(data, batchNo) {
            // Clear the table body completely
            $('#plasmaTableBody').empty();

            // Check if we have batch details
            if (data.batch_details && data.batch_details.length > 0) {
                // Group by AR number for better display
                const arGroups = {};
                data.batch_details.forEach(detail => {
                    if (!arGroups[detail.ar_no]) {
                        arGroups[detail.ar_no] = [];
                    }
                    arGroups[detail.ar_no].push(detail);
                });

                let html = '';

                // Process each AR number group
                Object.keys(arGroups).forEach((arNo, arIndex) => {
                    const details = arGroups[arNo];

                    // Create the first row with rowspan for this AR number
                    html += `
                        <tr>
                            <td rowspan="${details.length}" id="arNoCell_${arIndex}">${arNo || 'N/A'}</td>
                            <td><input type="text" class="form-control-sm" name="pool_no[]" value="${details[0].mini_pool_id || ''}"></td>
                            <td><input type="text" class="form-control-sm" name="requested_volume[]" value="${details[0].total_volume || ''}"></td>
                            <td><input type="text" class="form-control-sm" name="issued_volume[]" value="${details[0].issued_volume || ''}"></td>
                            <td><input type="text" class="form-control-sm" name="dispensed_by[]" value="${details[0].created_by_name || '{{ Auth::user()->name }}'}"></td>
                        </tr>
                    `;

                    // Add the remaining rows for this AR number
                    for (let i = 1; i < details.length; i++) {
                        const detail = details[i];
                        html += `
                            <tr>
                                <td><input type="text" class="form-control-sm" name="pool_no[]" value="${detail.mini_pool_id || ''}"></td>
                                <td><input type="text" class="form-control-sm" name="requested_volume[]" value="${detail.total_volume || ''}"></td>
                                <td><input type="text" class="form-control-sm" name="issued_volume[]" value="${detail.issued_volume || ''}"></td>
                                <td><input type="text" class="form-control-sm" name="dispensed_by[]" value="${detail.created_by_name || '{{ Auth::user()->name }}'}"></td>
                            </tr>
                        `;
                    }
                });

                // Append all rows to the table at once
                $('#plasmaTableBody').html(html);

                console.log("Populated table with batch details");
            } else {
                console.log("No batch details found in the response");
                // Add a message row if no data
                $('#plasmaTableBody').html(`
                    <tr>
                        <td colspan="5" class="text-center">No data found for batch number: ${batchNo}</td>
                    </tr>
                `);
            }
        }

        // Function to fetch bag status details
        function fetchBagStatusDetails() {
            const bloodBankId = $('#blood_bank').val();
            const pickupDate = $('input[name="date"]').val();

            if (!bloodBankId || !pickupDate) {
                return;
            }

            $.ajax({
                url: '{{ route("plasma.dispensing.get-bag-status") }}',
                method: 'POST',
                data: {
                    blood_bank_id: bloodBankId,
                    pickup_date: pickupDate
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Clear existing table rows
                        $('#plasmaTableBody tr').remove();

                        if (response.data.length === 0) {
                            return;
                        }

                        // Add new rows
                        response.data.forEach(function(item) {
                            const row = `
                                <tr>
                                    <td><input type="text" class="form-control-sm" name="ar_no[]" value="${item.ar_no || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="pool_no[]" value="${item.mini_pool_id}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="requested_volume[]" value="" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="issued_volume[]" value="${item.issued_volume || ''}" readonly></td>
                                    <td>
                                        <input type="text" class="form-control-sm" name="dispensed_by[]" value="${item.created_by_name || '{{ Auth::user()->name }}'}" readonly>
                                        <input type="hidden" name="created_by[]" value="${item.created_by || '{{ Auth::id() }}'}">
                                    </td>
                                </tr>
                            `;
                            $('#plasmaTableBody').append(row);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching bag status details:', xhr.responseText);
                }
            });
        }

        // Add event listeners for blood bank and date changes
        $('#blood_bank, input[name="date"]').on('change', fetchBagStatusDetails);
    });

    function printForm() {
        const batchNo = $('#batch_number').val();
        const formData = {
            batchNo: batchNo,
            items: []
        };

        // Get all rows and their input values
        $('#plasmaTableBody tr').each(function() {
            const row = $(this);
            // Find the AR number for this row
            let arNo = '';
            const arCell = row.find('td[id^="arNoCell_"]');

            if (arCell.length > 0) {
                // This row has an AR number cell
                arNo = arCell.text();
            } else {
                // This row doesn't have an AR number cell, find the previous row's AR number
                const rowIndex = row.index();
                for (let i = rowIndex - 1; i >= 0; i--) {
                    const prevRow = $('#plasmaTableBody tr').eq(i);
                    const prevArCell = prevRow.find('td[id^="arNoCell_"]');
                    if (prevArCell.length > 0) {
                        arNo = prevArCell.text();
                        break;
                    }
                }
            }

            formData.items.push({
                ar_no: arNo,
                pool_no: row.find('input[name="pool_no[]"]').val() || '',
                requested_volume: row.find('input[name="requested_volume[]"]').val() || '',
                issued_volume: row.find('input[name="issued_volume[]"]').val() || '',
                dispensed_by: row.find('input[name="dispensed_by[]"]').val() || ''
            });
        });

        // Open print template in new window
        const printWindow = window.open('{{ route("plasma.dispensing.print") }}', '_blank');

        // Send data to print template
        printWindow.onload = function() {
            printWindow.postMessage(formData, window.location.origin);
        };
    }

    async function exportToExcel() {
        try {
            // Load SheetJS library if not already loaded
            const XLSX = await loadSheetJS();

            // Create data array
            const data = [];
            const batchNo = $('#batch_number').val();

            // Add headers
            data.push([
                'Batch No.',
                'A.R. No.',
                'Mega Pool No./ Mini Pool No./ Donor ID',
                'Requested Volume',
                'Issued Volume',
                'Dispensed By'
            ]);

            // Get all rows and their input values
            $('#plasmaTableBody tr').each(function() {
                const row = $(this);

                // Find the AR number for this row
                let arNo = '';
                const arCell = row.find('td[id^="arNoCell_"]');

                if (arCell.length > 0) {
                    // This row has an AR number cell
                    arNo = arCell.text();
                } else {
                    // This row doesn't have an AR number cell, find the previous row's AR number
                    const rowIndex = row.index();
                    for (let i = rowIndex - 1; i >= 0; i--) {
                        const prevRow = $('#plasmaTableBody tr').eq(i);
                        const prevArCell = prevRow.find('td[id^="arNoCell_"]');
                        if (prevArCell.length > 0) {
                            arNo = prevArCell.text();
                            break;
                        }
                    }
                }

                const rowData = [
                    batchNo,
                    arNo,
                    row.find('input[name="pool_no[]"]').val() || '',
                    row.find('input[name="requested_volume[]"]').val() || '',
                    row.find('input[name="issued_volume[]"]').val() || '',
                    row.find('input[name="dispensed_by[]"]').val() || ''
                ];
                data.push(rowData);
            });

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Set column widths
            const wscols = [
                {wch: 10}, // Batch No.
                {wch: 10}, // A.R. No.
                {wch: 25}, // Mega Pool No./ Mini Pool No./ Donor ID
                {wch: 15}, // Requested Volume
                {wch: 15}, // Issued Volume
                {wch: 20}  // Dispensed By
            ];
            ws['!cols'] = wscols;

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Plasma Dispensing");

            // Save the file
            XLSX.writeFile(wb, "plasma_dispensing.xlsx");
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Error exporting to Excel: ' + error.message);
        }
    }
</script>
@endpush
