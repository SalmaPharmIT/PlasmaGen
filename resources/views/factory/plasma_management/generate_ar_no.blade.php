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
                            <th rowspan="2" style="width: 10%;">Date of Receipt</th>
                            <th rowspan="2" style="width: 15%;">GRN No.</th>
                            <th rowspan="2" style="width: 15%;">Blood Bank Name</th>
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
                            <td><input type="text" class="form-control-sm" name="receipt_date[]" value="{{ $entry['receipt_date'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="grn_no[]" value="{{ $entry['grn_no'] }}" readonly></td>
                            <td><input type="text" class="form-control-sm" name="blood_bank[]" value="{{ $entry['blood_bank'] }}" readonly></td>
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
                            <td colspan="9" class="text-center">No plasma entries found</td>
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
        // Object to store temporary AR numbers and destruction numbers
        const tempData = {
            arNumbers: {},
            destructionNumbers: {}
        };

        // Handle Accept button click
        $(document).on('click', '.accept-btn', function() {
            const row = $(this).closest('tr');
            const entryId = row.data('entry-id');
            const destructionNo = row.find('input[name="destruction_no[]"]').val();
            const remarks = row.find('input[name="remarks[]"]').val();

            console.log('Accepting entry:', {
                entryId,
                destructionNo,
                remarks
            });

            // Send AJAX request to get AR number without saving
            $.ajax({
                url: '{{ route("plasma.update-ar-no") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    entry_id: entryId,
                    destruction_no: destructionNo,
                    remarks: remarks,
                    status: 'accepted',
                    generate_only: 1
                },
                success: function(response) {
                    console.log('Accept response:', response);
                    
                    if (response.status === 'success' && response.ar_no) {
                        row.find('input').prop('readonly', true);
                        row.addClass('table-success');
                        row.find('.accept-btn, .reject-btn').prop('disabled', true);
                        
                        // Store the AR number temporarily
                        tempData.arNumbers[entryId] = response.ar_no;
                        
                        // Update the AR number in the table
                        row.find('input[name="ar_no[]"]').val(response.ar_no);
                        
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Entry accepted successfully'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to generate AR number'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Accept error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to accept entry'
                    });
                }
            });
        });

        // Handle Reject button click
        $(document).on('click', '.reject-btn', function() {
            const row = $(this).closest('tr');
            const entryId = row.data('entry-id');
            const remarks = row.find('input[name="remarks[]"]').val();

            console.log('Rejecting entry:', {
                entryId,
                remarks
            });

            // Send AJAX request to update the entry
            $.ajax({
                url: '{{ route("plasma.update-ar-no") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    entry_id: entryId,
                    remarks: remarks,
                    status: 'rejected'
                },
                success: function(response) {
                    console.log('Reject response:', response);
                    
                    if (response.status === 'success') {
                        if (response.entry_status === 'rejected') {
                            row.addClass('table-danger');
                            row.find('.accept-btn').prop('disabled', true);
                            row.find('.reject-btn').prop('disabled', true);
                            
                            // Set the generated destruction number
                            const destructionNoInput = row.find('input[name="destruction_no[]"]');
                            destructionNoInput.val(response.destruction_no);
                            
                            // Make all fields readonly after rejection
                            row.find('input').prop('readonly', true);
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Entry rejected successfully. Destruction number has been generated.'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to reject entry'
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Reject error:', xhr);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: xhr.responseJSON?.message || 'Failed to reject entry'
                    });
                }
            });
        });

        // Handle form submission
        $('#ARNoAllotmentForm').on('submit', function(e) {
            e.preventDefault();
            
            // Collect all entries that need to be saved
            const entriesToSave = [];
            
            // Process accepted entries
            Object.keys(tempData.arNumbers).forEach(entryId => {
                const row = $(`tr[data-entry-id="${entryId}"]`);
                entriesToSave.push({
                    entry_id: entryId,
                    ar_no: tempData.arNumbers[entryId],
                    destruction_no: row.find('input[name="destruction_no[]"]').val(),
                    remarks: row.find('input[name="remarks[]"]').val(),
                    status: 'accepted'
                });
            });
            
            // Process rejected entries - collect all rows with table-danger class
            $('.table-danger').each(function() {
                const row = $(this);
                const entryId = row.data('entry-id');
                const destructionNo = row.find('input[name="destruction_no[]"]').val();
                
                entriesToSave.push({
                    entry_id: entryId,
                    destruction_no: destructionNo,
                    remarks: row.find('input[name="remarks[]"]').val(),
                    status: 'rejected'
                });
            });

            console.log('Submitting entries:', entriesToSave);
            
            // Send all entries to be saved
            if (entriesToSave.length > 0) {
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
                                text: 'All entries saved successfully'
                            }).then(() => {
                                // Reload the page to show updated data
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to save entries'
                            });
                        }
                    },
                    error: function(xhr) {
                        console.error('Submit error:', xhr);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: xhr.responseJSON?.message || 'Failed to save entries'
                        });
                    }
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'No entries to save'
                });
            }
        });
    });
</script>
@endpush 