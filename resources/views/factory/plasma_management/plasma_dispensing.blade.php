@extends('include.dashboardLayout')

@section('title', 'Plasma Dispensing')

@push('styles')
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
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="text-center">Plasma Dispensing Sheet</h4>
            </div>
        </div>

        <form id="plasmaDispenseForm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Batch No./ Document Request No.:</label>
                        <input type="text" class="form-control form-control-sm" name="batch_no" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Request By (Department):</label>
                        <input type="text" class="form-control form-control-sm" name="request_by" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="form-label">Date:</label>
                        <input type="date" class="form-control form-control-sm" name="date" required>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered excel-like">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 10%;">A.R. No.</th>
                            <th rowspan="2" style="width: 20%;">Mega Pool No./ Mini Pool No./ Donor ID</th>
                            <th colspan="2" class="text-center" style="width: 20%;">Volume in Liters</th>
                            <th rowspan="2" style="width: 15%;">Dispensed By Sign/ Date<br>(Warehouse)</th>
                            <th rowspan="2" style="width: 15%;">Verified By Sign/ Date<br>(QA)</th>
                            <th rowspan="2" style="width: 20%;">Checked & Received By<br>Sign/ Date<br>(User)</th>
                        </tr>
                        <tr>
                            <th style="width: 10%;">Requested</th>
                            <th style="width: 10%;">Issued</th>
                        </tr>
                    </thead>
                    <tbody id="plasmaTableBody">
                        @for ($i = 0; $i < 8; $i++)
                        <tr>
                            <td><input type="text" class="form-control-sm" name="ar_no[]"></td>
                            <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                            <td><input type="number" step="0.01" class="form-control-sm requested" name="volume_requested[]"></td>
                            <td><input type="number" step="0.01" class="form-control-sm issued" name="volume_issued[]"></td>
                            <td><input type="text" class="form-control-sm" name="dispensed_by[]"></td>
                            <td><input type="text" class="form-control-sm" name="verified_by[]"></td>
                            <td><input type="text" class="form-control-sm" name="checked_by[]"></td>
                        </tr>
                        @endfor
                        <tr id="totalRow" class="table-secondary">
                            <td colspan="2" class="text-end pe-2"><strong>Total Volume</strong></td>
                            <td id="total_requested" class="text-center">0.00</td>
                            <td id="total_issued" class="text-center">0.00</td>
                            <td colspan="2"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-success btn-sm" id="addRow">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm float-end">Submit</button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h6 class="mb-2">Note:</h6>
                        <ul class="mb-0">
                            <li>This format is for an example purpose. The same shall be modified as per the requirement.</li>
                            <li>Attach copy of plasma mini pool and mega pool handling record.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Function to add new row
        $('#addRow').click(function() {
            var newRow = `
                <tr>
                    <td><input type="text" class="form-control-sm" name="ar_no[]"></td>
                    <td><input type="text" class="form-control-sm" name="pool_no[]"></td>
                    <td><input type="number" step="0.01" class="form-control-sm requested" name="volume_requested[]"></td>
                    <td><input type="number" step="0.01" class="form-control-sm issued" name="volume_issued[]"></td>
                    <td><input type="text" class="form-control-sm" name="dispensed_by[]"></td>
                    <td><input type="text" class="form-control-sm" name="verified_by[]"></td>
                    <td><input type="text" class="form-control-sm" name="checked_by[]"></td>
                </tr>
            `;
            $(newRow).insertBefore('#totalRow');
            calculateTotals();
        });

        // Calculate totals when input changes
        $(document).on('input', '.requested, .issued', function() {
            calculateTotals();
        });

        function calculateTotals() {
            let totalRequested = 0;
            let totalIssued = 0;

            $('.requested').each(function() {
                totalRequested += parseFloat($(this).val() || 0);
            });

            $('.issued').each(function() {
                totalIssued += parseFloat($(this).val() || 0);
            });

            $('#total_requested').text(totalRequested.toFixed(2));
            $('#total_issued').text(totalIssued.toFixed(2));
        }

        // Handle form submission
        $('#plasmaDispenseForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate if there are any rows
            if ($('#plasmaTableBody tr').length <= 2) { // Updated to account for total and add row rows
                alert('Please add at least one entry.');
                return;
            }

            // Add your form submission logic here
            alert('Form submitted! Add your backend logic.');
        });
    });
</script>
@endpush 