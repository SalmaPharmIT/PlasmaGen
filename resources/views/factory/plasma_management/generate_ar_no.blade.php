@extends('include.dashboardLayout')

@section('title', 'AR No. Allotment')

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
    .table-success {
        background-color: #d1e7dd !important;
    }
    .table-danger {
        background-color: #f8d7da !important;
    }
</style>
@endpush

@section('content')
<div class="card">
    <div class="card-header text-white" style="background-color: #0c4c90;">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="text-center mb-0">A.R.No. Allotment Sheet</h4>
        </div>
    </div>
    <div class="card-body">
        
        <form id="ARNoAllotmentForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered excel-like">
                    <thead>
                        <tr>
                            <th rowspan="2" style="width: 5%;">SL No.</th>
                            <th rowspan="2" style="width: 10%;">Pickup Date</th>
                            <th rowspan="2" style="width: 10%;">Date of Receipt</th>
                            <th rowspan="2" style="width: 15%;">GRN No.</th>
                            <th rowspan="2" style="width: 15%;">Blood Bank Name</th>
                            <th rowspan="2" style="width: 15%;">Plasma Qty(Ltr)</th>
                            <th rowspan="2" style="width: 15%;">Alloted AR No.</th>
                            {{-- <th rowspan="2" style="width: 10%;">Destruction No.</th> --}}
                            <th rowspan="2" style="width: 10%;">Entered By</th>
                            <th rowspan="2" style="width: 10%;">Remarks</th>
                            <th rowspan="2" style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="plasmaTableBody">
                        @forelse($plasmaEntries as $index => $entry)
                        <tr data-entry-id="{{ $entry['id'] }}" class="{{ !empty($entry['ar_no']) ? 'table-success' : (!empty($entry['destruction_no']) ? 'table-danger' : '') }}">
                            <td><input type="text" class="form-control-sm" name="sl_no[]" value="{{ $index + 1 }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="pickup_date[]" value="{{ $entry['pickup_date'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="receipt_date[]" value="{{ $entry['receipt_date'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="grn_no[]" value="{{ $entry['grn_no'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="blood_bank[]" value="{{ $entry['blood_bank'] }}" data-blood-bank-id="{{ $entry['blood_bank_id'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="plasma_qty[]" value="{{ $entry['plasma_qty'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="ar_no[]" value="{{ $entry['ar_no'] }}" readonly></td>
                            {{-- <td><input type="text" class="form-control-sm" name="destruction_no[]" value="{{ $entry['destruction_no'] }}" {{ !empty($entry['ar_no']) || !empty($entry['destruction_no']) ? 'readonly' : '' }}></td> --}}
                            <td><input type="text" class="form-control-sm" name="entered_by[]" value="{{ $entry['entered_by'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="remarks[]" value="{{ $entry['remarks'] }}" {{ !empty($entry['ar_no']) || !empty($entry['destruction_no']) ? 'readonly' : '' }}></td>
                            <td class="text-center" style="padding: 2px !important;">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" class="btn btn-success btn-sm accept-btn" style="padding: 0.1rem 0.5rem; font-size: 0.7rem;" {{ !empty($entry['ar_no']) || !empty($entry['destruction_no']) ? 'disabled' : '' }}>
                                        <i class="bi bi-check-circle"></i> Accept
                                    </button>
                                    {{-- <button type="button" class="btn btn-danger btn-sm reject-btn" style="padding: 0.1rem 0.5rem; font-size: 0.7rem;" {{ !empty($entry['ar_no']) || !empty($entry['destruction_no']) ? 'disabled' : '' }}>
                                        <i class="bi bi-x-circle"></i> Reject
                                    </button> --}}
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No plasma entries found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary btn-sm float-end">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Object to store temporary AR numbers and track last generated numbers per blood bank
        const tempData = {
            arNumbers: {},
            lastGeneratedArNo: {},  // Track last generated AR number per blood bank
            destructionNumbers: {},
            sequences: {} // Track sequences per blood bank
        };

        // Function to get blood bank code from row
        function getBloodBankId(row) {
            return row.find('input[name="blood_bank[]"]').data('blood-bank-id');
        }

        // Function to generate AR number on client side
        function generateArNumber(bloodBankId) {
            // Format blood bank ID to 4 digits
            const bloodBankCode = String(bloodBankId).padStart(4, '0');
            
            // Get current year's last 2 digits
            const year = new Date().getFullYear().toString().slice(-2);
            
            // Initialize or increment sequence for this blood bank
            if (!tempData.sequences[bloodBankId]) {
                tempData.sequences[bloodBankId] = 0;
            }
            tempData.sequences[bloodBankId]++;
            
            // Format sequence number
            const sequence = String(tempData.sequences[bloodBankId]).padStart(4, '0');
            
            // Generate AR number
            return `AR/RM10001/${bloodBankCode}/${year}/${sequence}`;
        }

        // Handle Accept button click
        $(document).on('click', '.accept-btn', function() {
            const row = $(this).closest('tr');
            const entryId = row.data('entry-id');
            const remarks = row.find('input[name="remarks[]"]').val();
            const bloodBank = row.find('input[name="blood_bank[]"]').val();
            const grnNo = row.find('input[name="grn_no[]"]').val();
            const bloodBankId = getBloodBankId(row);

            // Check if we already generated an AR number for this entry
            if (tempData.arNumbers[entryId]) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Already Generated',
                    text: `AR Number ${tempData.arNumbers[entryId]} has already been generated for this entry.`
                });
                return;
            }

            Swal.fire({
                title: 'Confirm AR Number Generation',
                html: `
                    <p class="mt-3">Are you sure you want to generate AR Number for this entry?</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Generate AR No',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading state
                    Swal.fire({
                        title: 'Generating...',
                        text: 'Please wait while we generate the AR number.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Generate client-side AR number for preview
                    const previewArNo = generateArNumber(bloodBankId);

                    // Send AJAX request to get server-side AR number
                    $.ajax({
                        url: '{{ route("plasma.update-ar-no") }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            entry_id: entryId,
                            remarks: remarks,
                            status: 'accepted',
                            generate_only: 1,
                            preview_ar_no: previewArNo, // Send preview AR number to server
                            blood_bank_id: bloodBankId, // Send blood bank ID
                            current_sequence: tempData.sequences[bloodBankId] // Send current sequence
                        },
                        success: function(response) {
                            console.log('Accept response:', response);
                            
                            if (response.status === 'success' && response.ar_no) {
                                // Update local sequence if server sequence is higher
                                if (response.sequence && response.sequence > tempData.sequences[bloodBankId]) {
                                    tempData.sequences[bloodBankId] = response.sequence;
                                }

                                row.find('input').prop('readonly', true);
                                row.addClass('table-success');
                                row.find('.accept-btn').prop('disabled', true);
                                
                                // Store the server-generated AR number
                                tempData.arNumbers[entryId] = response.ar_no;
                                tempData.lastGeneratedArNo[bloodBankId] = response.ar_no;
                                
                                // Update the AR number in the table
                                row.find('input[name="ar_no[]"]').val(response.ar_no);
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: `<span style="font-size: 1.0em">AR Number <b>${response.ar_no}</b> has been generated successfully</span>`,
                                    showConfirmButton: true
                                });
                            } else {
                                // If server generation fails, use the preview number
                                row.find('input').prop('readonly', true);
                                row.addClass('table-success');
                                row.find('.accept-btn').prop('disabled', true);
                                
                                // Store the preview AR number
                                tempData.arNumbers[entryId] = previewArNo;
                                tempData.lastGeneratedArNo[bloodBankId] = previewArNo;
                                
                                // Update the AR number in the table
                                row.find('input[name="ar_no[]"]').val(previewArNo);
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: `<span style="font-size: 1.0em">AR Number <b>${previewArNo}</b> has been generated successfully</span>`,
                                    showConfirmButton: true
                                });
                            }
                        },
                        error: function(xhr) {
                            console.error('Accept error:', xhr);
                            // If server request fails, use the preview number
                            row.find('input').prop('readonly', true);
                            row.addClass('table-success');
                            row.find('.accept-btn').prop('disabled', true);
                            
                            // Store the preview AR number
                            tempData.arNumbers[entryId] = previewArNo;
                            tempData.lastGeneratedArNo[bloodBankId] = previewArNo;
                            
                            // Update the AR number in the table
                            row.find('input[name="ar_no[]"]').val(previewArNo);
                            
                            Swal.fire({
                                icon: 'success',
                                title: `<span style="font-size: 1.0em">AR Number <b>${previewArNo}</b> has been generated successfully</span>`,
                                showConfirmButton: true
                            });
                        }
                    });
                }
            });
        });

        // Handle form submission
        $('#ARNoAllotmentForm').on('submit', function(e) {
            e.preventDefault();
            
            // Check if there are any AR numbers to save
            if (Object.keys(tempData.arNumbers).length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Changes',
                    text: 'Please generate at least one AR number before submitting.'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Saving...',
                text: 'Please wait while we save the AR numbers.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Collect all entries that need to be saved
            const entriesToSave = [];
            
            // Process accepted entries
            Object.keys(tempData.arNumbers).forEach(entryId => {
                const row = $(`tr[data-entry-id="${entryId}"]`);
                entriesToSave.push({
                    entry_id: entryId,
                    ar_no: tempData.arNumbers[entryId],
                    remarks: row.find('input[name="remarks[]"]').val(),
                    status: 'accepted'
                });
            });

            console.log('Submitting entries:', entriesToSave);
            
            // Send all entries to be saved
            $.ajax({
                url: '{{ route("plasma.update-ar-no") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    entries: entriesToSave,
                    bulk_save: true
                },
                success: function(response) {
                    console.log('Submit response:', response);
                    
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'All AR numbers have been saved successfully'
                        }).then(() => {
                            // Reload the page to show updated data
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to save AR numbers'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Submit error:', xhr);
                    let errorMessage = 'Failed to save AR numbers';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage
                    });
                }
            });
        });
    });
</script>
@endpush 