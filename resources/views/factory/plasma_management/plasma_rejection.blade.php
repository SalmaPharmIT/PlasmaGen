@extends('include.dashboardLayout')

@section('title', 'Plasma Rejection')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    }
    .btn-sm {
        font-size: 0.8rem;
        padding: 0.2rem 0.5rem;
    }
    .select2-container--default .select2-selection--single {
        height: 22px !important;
        min-height: 22px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 22px !important;
        padding-left: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 22px !important;
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
    .form-row {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        flex: 1;
        margin-bottom: 0;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
        font-weight: 500;
        color: #495057;
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
</style>
@endpush

@push('head')
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
@endpush

@section('content')
<div class="card">
    <div class="card-header text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-center mb-0">Plasma Rejection Summary</h4>
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
        <form id="plasmaRejectionForm">
            <div class="form-row">
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
            </div>

            <div class="table-responsive">
                <table class="table table-bordered excel-like">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 10%;">A.R. No.</th>
                            <th rowspan="2" style="width: 20%;">Mega Pool No./ Mini Pool No./ Donor ID</th>
                            <th rowspan="2" style="width: 15%;">Donation Date</th>
                            <th rowspan="2" style="width: 15%;">Blood Group</th>
                            <th rowspan="2" style="width: 15%;">Volume</th>
                            <th rowspan="2" style="width: 15%;">Rejection Reason</th>
                            <th rowspan="2" style="width: 10%;">Rejected By</th>
                        </tr>
                    </thead>
                    <tbody id="plasmaTableBody">
                        @for ($i = 0; $i < 8; $i++)
                        <tr>
                            <td><input type="text" class="form-control-sm" name="ar_no[]"></td>
                            <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                            <td><input type="text" class="form-control-sm" name="donation_date[]"></td>
                            <td><input type="text" class="form-control-sm" name="blood_group[]"></td>
                            <td><input type="text" class="form-control-sm" name="volume[]"></td>
                            <td><input type="text" class="form-control-sm" name="rejection_reason[]"></td>
                            <td><input type="text" class="form-control-sm" name="rejected_by[]"></td>
                        </tr>
                        @endfor
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
<script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
<script>
    $(document).ready(function() {
        // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 for blood bank with proper styling
        $('.select2-bloodbank').select2({
            placeholder: "Select Blood Centre",
            allowClear: true,
            width: '100%',
            dropdownParent: $('#blood_bank').parent(),
            theme: 'bootstrap-5'
        }).on('select2:open', function() {
            // Add custom class to the dropdown
            $('.select2-dropdown').addClass('select2-dropdown-sm');
        });

        // Initialize Select2 for AR Number dropdown
        $('#ar_number').select2({
            placeholder: "Select A.R. Number",
            allowClear: true,
            width: '100%',
            ajax: {
                url: '{{ route("plasma.get-ar-numbers") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data.results || []
                    };
                },
                cache: true
            }
        });

        // Handle AR number selection
        $('#ar_number').on('change', function() {
            const arNumber = $(this).val();
            if (arNumber) {
                fetchPlasmaRejectionDetails(arNumber);
            } else {
                // Clear the table if no AR number is selected
                $('#plasmaTableBody tr').remove();
                addEmptyRows(8);
            }
        });

        // Function to fetch plasma rejection details by AR number
        function fetchPlasmaRejectionDetails(arNumber) {
            $.ajax({
                url: '{{ route("plasma.rejection.get-bag-status") }}',
                method: 'POST',
                data: {
                    ar_number: arNumber
                },
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.status === 'success') {
                        // Clear existing table rows
                        $('#plasmaTableBody tr').remove();
                        
                        if (response.data.length === 0) {
                            console.log('No data found');
                            addEmptyRows(8);
                            return;
                        }

                        // Add new rows
                        response.data.forEach(function(item) {
                            console.log('Processing item:', item);
                            const row = `
                                <tr>
                                    <td><input type="text" class="form-control-sm" name="ar_no[]" value="${item.ar_no || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="pool_no[]" value="${item.mini_pool_id || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="donation_date[]" value="${item.donation_date || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="blood_group[]" value="${item.blood_group || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="volume[]" value="${item.bag_volume || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="rejection_reason[]"></td>
                                    <td><input type="text" class="form-control-sm" name="rejected_by[]" value="{{ Auth::user()->name }}" readonly></td>
                                </tr>
                            `;
                            $('#plasmaTableBody').append(row);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching rejection details:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    addEmptyRows(8);
                }
            });
        }

        // Function to add empty rows to the table
        function addEmptyRows(count) {
            for (let i = 0; i < count; i++) {
                const row = `
                    <tr>
                        <td><input type="text" class="form-control-sm" name="ar_no[]"></td>
                        <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                        <td><input type="text" class="form-control-sm" name="donation_date[]"></td>
                        <td><input type="text" class="form-control-sm" name="blood_group[]"></td>
                        <td><input type="text" class="form-control-sm" name="volume[]"></td>
                        <td><input type="text" class="form-control-sm" name="rejection_reason[]"></td>
                        <td><input type="text" class="form-control-sm" name="rejected_by[]"></td>
                    </tr>
                `;
                $('#plasmaTableBody').append(row);
            }
        }

        // Handle blood bank selection
        $('#blood_bank').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            console.log('Selected blood bank:', selectedText);
            $('#display_blood_centre').text(selectedText || '-');
        });

        // Handle form submission
        $('#plasmaRejectionForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                ar_number: $('#ar_number').val(),
                items: []
            };

            // Only include rows with rejection reason filled
            $('#plasmaTableBody tr').each(function() {
                const row = $(this);
                const rejectionReason = row.find('input[name="rejection_reason[]"]').val();
                
                if (rejectionReason) {
                    formData.items.push({
                        ar_no: row.find('input[name="ar_no[]"]').val(),
                        pool_no: row.find('input[name="pool_no[]"]').val(),
                        donation_date: row.find('input[name="donation_date[]"]').val(),
                        blood_group: row.find('input[name="blood_group[]"]').val(),
                        volume: row.find('input[name="volume[]"]').val(),
                        rejection_reason: rejectionReason,
                        rejected_by: row.find('input[name="rejected_by[]"]').val()
                    });
                }
            });

            if (formData.items.length === 0) {
                alert('Please provide at least one rejection reason');
                return;
            }

            $.ajax({
                url: '{{ route("plasma.rejection.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Plasma rejection records saved successfully');
                        window.location.reload();
                    } else {
                        alert('Error: ' + (response.message || 'Failed to save plasma rejection records'));
                    }
                },
                error: function(xhr) {
                    console.error('Error saving plasma rejection records:', xhr.responseText);
                    alert('Error saving plasma rejection records: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });
    });

    function printForm() {
        const bloodBank = $('#blood_bank option:selected').text();
        const date = $('input[name="date"]').val();
        const items = [];
        
        $('#plasmaTableBody tr').each(function() {
            const row = $(this);
            items.push({
                ar_no: row.find('input[name="ar_no[]"]').val(),
                pool_no: row.find('input[name="pool_no[]"]').val(),
                donation_date: row.find('input[name="donation_date[]"]').val(),
                blood_group: row.find('input[name="blood_group[]"]').val(),
                volume: row.find('input[name="volume[]"]').val(),
                rejection_reason: row.find('input[name="rejection_reason[]"]').val(),
                rejected_by: row.find('input[name="rejected_by[]"]').val()
            });
        });

        // Open print template in new window
        const printWindow = window.open('', '_blank');
        
        // Make AJAX call to get print template
        $.ajax({
            url: '{{ route("plasma.rejection.print") }}',
            method: 'POST',
            data: {
                bloodCentre: bloodBank,
                date: date,
                items: items
            },
            success: function(response) {
                printWindow.document.write(response);
                printWindow.document.close();
                printWindow.focus();
                // Wait for content to load then print
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            },
            error: function(xhr) {
                console.error('Error loading print template:', xhr);
                alert('Error loading print template');
            }
        });
    }

    function exportToExcel() {
        try {
            // Create data array
            const data = [];
            
            // Add headers
            data.push([
                'A.R. No.',
                'Mega Pool No./ Mini Pool No./ Donor ID',
                'Donation Date',
                'Blood Group',
                'Volume',
                'Rejection Reason',
                'Rejected By'
            ]);

            // Get all rows and their input values
            $('#plasmaTableBody tr').each(function() {
                const row = $(this);
                const rowData = [
                    row.find('input[name="ar_no[]"]').val() || '',
                    row.find('input[name="pool_no[]"]').val() || '',
                    row.find('input[name="donation_date[]"]').val() || '',
                    row.find('input[name="blood_group[]"]').val() || '',
                    row.find('input[name="volume[]"]').val() || '',
                    row.find('input[name="rejection_reason[]"]').val() || '',
                    row.find('input[name="rejected_by[]"]').val() || ''
                ];
                data.push(rowData);
            });

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);
            
            // Set column widths
            const wscols = [
                {wch: 10}, // A.R. No.
                {wch: 25}, // Mega Pool No./ Mini Pool No./ Donor ID
                {wch: 15}, // Donation Date
                {wch: 12}, // Blood Group
                {wch: 10}, // Volume
                {wch: 20}, // Rejection Reason
                {wch: 15}  // Rejected By
            ];
            ws['!cols'] = wscols;

            // Create workbook
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Plasma Rejection");

            // Generate Excel file
            const fileName = "Plasma_Rejection_" + new Date().toISOString().split('T')[0] + ".xlsx";
            XLSX.writeFile(wb, fileName);
        } catch (error) {
            console.error('Error exporting to Excel:', error);
            alert('Error exporting to Excel. Please try again.');
        }
    }
</script>
@endpush 