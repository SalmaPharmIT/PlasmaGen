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
                        <label class="small mb-1">Blood Centre Name & City</label>
                        <div>
                            <select class="form-control-sm select2-bloodbank" name="blood_bank" id="blood_bank" data-placeholder="Select Blood Centre">
                                <option></option>
                                @foreach($bloodCenters as $center)
                                    <option value="{{ $center['id'] }}">{{ $center['text'] }}</option>
                                @endforeach
                            </select>
                        </div>
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
                            <th rowspan="2" style="width: 15%;">Donation Date</th>
                            <th rowspan="2" style="width: 15%;">Blood Group</th>
                            <th rowspan="2" style="width: 15%;">Dispensed By Sign/ Date<br>(Warehouse)</th>
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
                            {{-- <td class="text-center">
                                <button type="button" class="btn btn-success btn-sm" id="addRow">
                                    <i class="bi bi-plus-circle"></i> Add
                                </button>
                            </td> --}}
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm float-end">Submit</button>
                </div>
            </div> --}}

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
         // Add CSRF token to all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialize Select2 for blood bank
        $('.select2-bloodbank').select2({
            placeholder: "Select Blood Centre",
            allowClear: true,
            width: '100%'
        });

        // Handle blood bank selection
        $('#blood_bank').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            $('#display_blood_centre').text(selectedText || '-');
        });

        // Function to fetch bag status details
        function fetchBagStatusDetails() {
            const bloodBankId = $('#blood_bank').val();
            const pickupDate = $('input[name="date"]').val();

            console.log('Fetching details with:', {
                bloodBankId,
                pickupDate
            });

            if (!bloodBankId || !pickupDate) {
                console.log('Missing required fields');
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
                    console.log('Response received:', response);
                    if (response.status === 'success') {
                        // Clear existing table rows except header
                        $('#plasmaTableBody tr:not(#totalRow)').remove();
                        
                        if (response.data.length === 0) {
                            console.log('No data found');
                            return;
                        }

                        // Add new rows
                        response.data.forEach(function(item) {
                            console.log('Processing item:', item);
                            const row = `
                                <tr>
                                    <td><input type="text" class="form-control-sm" name="ar_no[]" value="${item.ar_no || ''}" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="pool_no[]" value="${item.mini_pool_id}" readonly></td>
                                    <td><input type="number" step="0.01" class="form-control-sm requested" name="volume_requested[]" value="0.39"></td>
                                    <td><input type="number" step="0.01" class="form-control-sm issued" name="volume_issued[]" value="0.39"></td>
                                    <td><input type="text" class="form-control-sm" name="donation_date[]" value="2025-04-01" readonly></td>
                                    <td><input type="text" class="form-control-sm" name="blood_group[]" value="A+" readonly></td>
                                    <td>
                                        <input type="text" class="form-control-sm" name="dispensed_by[]" value="${item.created_by_name || '{{ Auth::user()->name }}'}" readonly>
                                        <input type="hidden" name="created_by[]" value="${item.created_by || '{{ Auth::id() }}'}">
                                    </td>
                                </tr>
                            `;
                            $('#totalRow').before(row);
                        });

                        // Update totals
                        updateTotals();
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching bag status details:', xhr.responseText);
                }
            });
        }

        // Function to update totals
        function updateTotals() {
            let totalRequested = 0;
            let totalIssued = 0;

            $('.requested').each(function() {
                totalRequested += parseFloat($(this).val()) || 0;
            });

            $('.issued').each(function() {
                totalIssued += parseFloat($(this).val()) || 0;
            });

            $('#total_requested').text(totalRequested.toFixed(2));
            $('#total_issued').text(totalIssued.toFixed(2));
        }

        // Add event listeners for blood bank and date changes
        $('#blood_bank, input[name="date"]').on('change', fetchBagStatusDetails);
    });
</script>
@endpush 