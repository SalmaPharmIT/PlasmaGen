@extends('include.dashboardLayout')

@section('title', 'Plasma Rejection')

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
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-12">
                <h4 class="text-center">Plasma Rejection Sheet</h4>
            </div>
        </div>

        <form id="plasmaRejectionForm">
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

        // Handle blood bank selection
        $('#blood_bank').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            console.log('Selected blood bank:', selectedText);
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
                url: '{{ route("plasma.rejection.get-bag-status") }}',
                method: 'POST',
                data: {
                    blood_bank_id: bloodBankId,
                    pickup_date: pickupDate
                },
                success: function(response) {
                    console.log('Response received:', response);
                    if (response.status === 'success') {
                        // Clear existing table rows except header
                        $('#plasmaTableBody tr').remove();
                        
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
                    console.error('Error fetching bag status details:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                }
            });
        }

        // Add event listeners for blood bank and date changes
        $('#blood_bank, input[name="date"]').on('change', fetchBagStatusDetails);

        // Handle form submission
        $('#plasmaRejectionForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                blood_bank_id: $('#blood_bank').val(),
                pickup_date: $('input[name="date"]').val(),
                items: []
            };

            $('#plasmaTableBody tr').each(function() {
                const row = $(this);
                formData.items.push({
                    ar_no: row.find('input[name="ar_no[]"]').val(),
                    pool_no: row.find('input[name="pool_no[]"]').val(),
                    donation_date: row.find('input[name="donation_date[]"]').val(),
                    blood_group: row.find('input[name="blood_group[]"]').val(),
                    volume: row.find('input[name="volume[]"]').val(),
                    rejection_reason: row.find('input[name="rejection_reason[]"]').val(),
                    rejected_by: row.find('input[name="rejected_by[]"]').val()
                });
            });

            $.ajax({
                url: '{{ route("plasma.rejection.store") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Plasma rejection records saved successfully');
                        window.location.reload();
                    } else {
                        alert('Error saving plasma rejection records');
                    }
                },
                error: function(xhr) {
                    console.error('Error saving plasma rejection records:', xhr.responseText);
                    alert('Error saving plasma rejection records');
                }
            });
        });
    });
</script>
@endpush 