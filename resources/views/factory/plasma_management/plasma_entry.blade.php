@extends('include.dashboardLayout')

@section('title', 'Plasma Entry')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card {
        margin: 0.5rem;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        padding: 0.75rem 1.25rem;
    }
    .card-header h4 {
        margin: 0;
        color: #f8f9fa;
        font-weight: 500;
    }
    .card-body {
        padding: 1.25rem;
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
    .excel-like input:not(.select2-search__field) {
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
    /* Select2 Custom Styles */
    .select2-container--default .select2-selection--single {
        height: 24px !important;
        min-height: 24px !important;
        font-size: 0.8rem !important;
        padding: 0 !important;
        border: none !important;
        background-color: transparent !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 0.4rem !important;
        padding-right: 1.2rem !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 24px !important;
        right: 2px !important;
    }
    .select2-dropdown {
        font-size: 0.8rem !important;
        border: 1px solid #dee2e6;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-container--open .select2-dropdown--below {
        margin-top: 1px;
    }
    /* Fix for Select2 in table cell */
    .excel-like td .select2-container {
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        width: 100% !important;
    }
    .excel-like td .select2-container .select2-selection {
        height: 100% !important;
        display: flex;
        align-items: center;
    }
    /* Ensure dropdown appears above sticky header */
    .select2-container--open .select2-dropdown {
        z-index: 9999;
    }
    /* Custom scrollbar for table */
    .table-responsive::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .actions-row {
        margin-top: 1rem;
        padding: 0.5rem 0;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header text-white" style="background-color: #0c4c90;">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="text-center mb-0">Plasma Entry Sheet</h4>
            </div>
        </div>
        <div class="card-body">
            <form id="plasmaEntryForm">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered excel-like">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 5%;">SL No.</th>
                                <th rowspan="2" style="width: 15%;">Pickup Date</th>
                                <th rowspan="2" style="width: 15%;">Date of Receipt</th>
                                <th rowspan="2" style="width: 20%;">GRN No.</th>
                                <th rowspan="2" style="width: 20%;">Blood Bank Name</th>
                                <th rowspan="2" style="width: 15%;">Plasma Qty(Ltr)</th>
                                <th rowspan="2" style="width: 15%;">Entered By</th>
                                <th rowspan="2" style="width: 10%;">Remarks</th>
                            </tr>
                        </thead>
                        <tbody id="plasmaTableBody">
                            @for ($i = 0; $i < 5; $i++)
                            <tr>
                                <td><input type="text" class="form-control-sm" name="sl_no[]" readonly value="{{ $i + 1 }}"></td>
                                <td><input type="date" class="form-control-sm" name="pickup_date[]"></td>
                                <td><input type="date" class="form-control-sm" name="receipt_date[]"></td>
                                <td><input type="text" class="form-control-sm" name="grn_no[]"></td>
                                <td>
                                    <select class="form-control-sm select2-bloodbank" name="blood_bank[]" data-placeholder="Select Blood Centre">
                                        <option></option>
                                        @foreach($bloodCenters as $center)
                                            <option value="{{ $center['id'] }}">{{ $center['text'] }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="text" class="form-control-sm" name="plasma_qty[]"></td>
                                <td>
                                    <input type="text" class="form-control-sm" name="entered_by[]" readonly value="{{ $userName }}">
                                    <input type="hidden" name="entered_by_id[]" value="{{ Auth::id() }}">
                                </td>
                                <td><input type="text" class="form-control-sm" name="remarks[]"></td>
                            </tr>
                            @endfor
                            <tr id="totalRow" class="table-secondary">
                                <td colspan="8" class="text-end pe-2">
                                    <button type="button" class="btn btn-success btn-sm" id="addRow">
                                        <i class="bi bi-plus-circle"></i> Add Row
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="actions-row">
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2 for existing rows
        initializeSelect2();

        // Function to initialize Select2
        function initializeSelect2() {
            $('.select2-bloodbank').each(function() {
                if (!$(this).data('select2')) {
                    $(this).select2({
                        theme: 'default',
                        width: '100%',
                        placeholder: $(this).data('placeholder'),
                        allowClear: true,
                        dropdownParent: $('body'),
                        ajax: {
                            url: '{{ route("api.plasma.bloodbanks") }}',
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1
                                };
                            },
                            processResults: function(data, params) {
                                return {
                                    results: data.results || []
                                };
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                console.error('AJAX error:', textStatus, errorThrown);
                            },
                            cache: true
                        },
                        minimumInputLength: 0
                    });
                }
            });
        }

        // Function to add new row
        $('#addRow').click(function() {
            var lastRow = $('#plasmaTableBody tr:not(#totalRow)').last();
            var lastNumber = parseInt(lastRow.find('input[name="sl_no[]"]').val()) || 0;
            var nextNumber = lastNumber + 1;
            
            var newRow = `
                <tr>
                    <td><input type="text" class="form-control-sm" name="sl_no[]" readonly value="${nextNumber}"></td>
                    <td><input type="date" class="form-control-sm" name="pickup_date[]"></td>
                    <td><input type="date" class="form-control-sm" name="receipt_date[]"></td>
                    <td><input type="text" class="form-control-sm" name="grn_no[]"></td>
                    <td>
                        <select class="form-control-sm select2-bloodbank" name="blood_bank[]" data-placeholder="Select Blood Centre">
                            <option></option>
                        </select>
                    </td>
                    <td><input type="text" class="form-control-sm" name="plasma_qty[]"></td>
                    <td>
                        <input type="text" class="form-control-sm" name="entered_by[]" readonly value="{{ $userName }}">
                        <input type="hidden" name="entered_by_id[]" value="{{ Auth::id() }}">
                    </td>
                    <td><input type="text" class="form-control-sm" name="remarks[]"></td>
                </tr>
            `;
            
            $(newRow).insertBefore('#totalRow');
            initializeSelect2();
        });

        // Handle form submission
        $('#plasmaEntryForm').on('submit', function(e) {
            e.preventDefault();
            
            // Validate if there are any rows with data
            var hasData = false;
            $('input[name="receipt_date[]"]').each(function() {
                var rowIndex = $(this).closest('tr').index();
                if ($(this).val() || 
                    $('input[name="grn_no[]"]').eq(rowIndex).val() || 
                    $('select[name="blood_bank[]"]').eq(rowIndex).val() || 
                    $('input[name="plasma_qty[]"]').eq(rowIndex).val() || 
                    $('input[name="remarks[]"]').eq(rowIndex).val()) {
                    hasData = true;
                    return false;
                }
            });

            if (!hasData) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please add at least one entry.'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Saving...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form via AJAX
            $.ajax({
                url: '/plasma/store',
                method: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Clear form and reset to initial state
                            $('#plasmaTableBody tr:not(#totalRow)').not(':first').remove();
                            $('#plasmaTableBody tr:first input:not([readonly])').val('');
                            $('#plasmaTableBody tr:first select').val(null).trigger('change');
                        }
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while saving the entries.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
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