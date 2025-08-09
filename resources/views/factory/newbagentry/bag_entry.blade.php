@extends('include.dashboardLayout')

@section('title', 'New Bag Entry')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
<!-- Font Awesome for icons -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" />
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
    .excel-like input, .excel-like select {
        background: transparent;
        width: 100%;
        border: none;
        padding: 0.1rem 0.2rem;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
    }
    .excel-like input[type="date"] {
        padding: 0.1rem 0.1rem;
        font-size: 0.75rem;
        width: 100%;
        height: 100%;
        cursor: pointer;
        background: transparent;
    }
    .excel-like input[type="date"]::-webkit-calendar-picker-indicator {
        display: block;
        cursor: pointer;
        opacity: 0.8;
        padding: 0;
        margin: 0;
        width: 14px;
        height: 14px;
        position: absolute;
        right: 2px;
        top: 50%;
        transform: translateY(-50%);
    }
    .excel-like td.date-cell {
        position: relative;
        padding: 0;
    }
    .excel-like input:focus, .excel-like select:focus {
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
    /* Focus styles for table cells */
    .excel-like td:focus-within {
        background-color: transparent !important;
    }
    .excel-like td:focus-within input,
    .excel-like td:focus-within select {
        background: transparent !important;
        border: none !important;
    }
    /* Hover styles for table cells */
    .excel-like td:hover {
        background-color: transparent !important;
    }
    /* Remove all separators between rows */
    .excel-like tr {
        border-bottom: none;
        border-top: none;
    }

    /* Update the group separator styles for 12-row groups */
    .excel-like tr:nth-child(12n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) {
        border-top: 2px solid #000;
    }

    /* Update the Mini Pool Bag Volume column styles */
    .excel-like tr:nth-child(12n) td:nth-child(7),
    .excel-like tr:nth-child(12n) td:nth-child(8) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) td:nth-child(7),
    .excel-like tr:nth-child(12n+1) td:nth-child(8) {
        border-top: 2px solid #000;
    }

    /* Add section separator styles */
    .excel-like tr:nth-child(12n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) {
        border-top: 2px solid #000;
    }

    /* Add section headers */
    .section-header {
        background-color: #f8f9fa;
        font-weight: bold;
        text-align: center;
    }

    /* Add section number column */
    .section-number {
        width: 30px;
        text-align: center;
        font-weight: bold;
        background-color: #f8f9fa;
    }

    /* Loader Styles */
    .loader-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loader-content {
        text-align: center;
    }

    .loader-content .spinner-border {
        width: 3rem;
        height: 3rem;
    }

    .loader-content div {
        margin-top: 1rem;
        font-weight: 500;
        color: #0d6efd;
    }

    /* Critical Select2 Fixes */
    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .select2-dropdown {
        z-index: 10000 !important;
        border-color: #80bdff !important;
        box-shadow: 0 0 5px rgba(0,123,255,.25) !important;
    }

    .select2-container .select2-selection--single {
        height: 22px !important;
        border: 1px solid #ced4da !important;
        outline: none !important;
    }

    .select2-container .select2-selection--single:focus {
        border-color: #80bdff !important;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25) !important;
    }

    .select2-container .select2-selection--single .select2-selection__rendered {
        line-height: 22px !important;
        padding-left: 8px !important;
        font-size: 0.8rem;
        color: #212529 !important;
    }

    .select2-container .select2-selection--single .select2-selection__arrow {
        height: 20px !important;
    }

    .select2-search--dropdown .select2-search__field {
        padding: 2px;
        border-color: #ced4da !important;
    }

    .select2-search--dropdown .select2-search__field:focus {
        border-color: #80bdff !important;
        outline: none !important;
    }

    .select2-results__option {
        padding: 3px 6px;
        font-size: 0.8rem;
        cursor: pointer !important;
    }

    .select2-container--open .select2-dropdown {
        display: block !important;
    }

    /* Form group styling */
    .form-group {
        margin-bottom: 0.5rem;
    }

    .form-group label {
        margin-bottom: 0.1rem;
    }

    /* Mini Pool Volume styling */
    .excel-like tr:nth-child(12n) td:nth-child(7),
    .excel-like tr:nth-child(12n) td:nth-child(8) {
        background-color: #fff;
    }

    /* Merged cell styling */
    .excel-like td[rowspan] {
        border: 1px solid #dee2e6;
        background-color: #fff !important;
        position: relative;
    }
    .excel-like td[rowspan] input {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        text-align: center;
        font-weight: bold;
    }

    /* Blood Group Dropdown Styles */
    td select.blood-group-select {
        width: 100%;
        height: 22px;
        padding: 0 0.2rem;
    }

    td select.blood-group-select[size]:not([size="1"]) {
        position: absolute !important;
        height: auto !important;
        min-width: 100px !important;
        max-height: 200px !important;
        z-index: 99999 !important;
        background: white !important;
        border: 1px solid #ced4da !important;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2) !important;
    }

    td select.blood-group-select option {
        padding: 2px 5px;
        background: white;
    }

    td select.blood-group-select option:hover {
        background-color: #e9ecef;
    }

    .excel-like td {
        padding: 0;
        height: 24px;
        vertical-align: middle;
        position: relative;
    }

    /* AR Number Search Styles */
    #arSearchResults {
        position: absolute;
        z-index: 1000;
        background: white;
        width: 100%;
        border: 1px solid #ced4da;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .search-result {
        transition: background-color 0.2s;
    }

    .search-result:hover {
        background-color: #f8f9fa;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Loader -->
    <div id="loader" class="loader-overlay" style="display: none;">
        <div class="loader-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2">Processing...</div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-2">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('newBag.store') }}">
                @csrf

                <!-- Header Information -->
                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="arNumberSelect">A.R. No.</label>
                            <select id="arNumberSelect" class="form-select form-select-sm" name="ar_no" required>
                                <option value="">Select A.R. No.</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="bloodCentreSelect">Blood Centre Name & City</label>
                            <select id="bloodCentreSelect" class="form-select form-select-sm" name="blood_centre_id" required>
                                <option value="">Select Blood Centre</option>
                                @forelse($bloodCenters as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }} - {{ $center->city }}</option>
                                @empty
                                    <option value="" disabled>No blood centers available</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="dateInput">Date</label>
                            <input type="date" id="dateInput" class="form-control form-control-sm" name="date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="pickupDateInput">Pickup Date</label>
                            <input type="date" id="pickupDateInput" class="form-control form-control-sm" name="pickup_date" required>
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="grnNoInput">GRN No.</label>
                            <input type="text" id="grnNoInput" class="form-control form-control-sm" name="grn_no" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1" for="megaPoolSelect">Mega Pool No.</label>
                            <select id="megaPoolSelect" class="form-select form-select-sm" name="mega_pool_no" required>
                                <option value="">Select Mega Pool No.</option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- Table Section -->
                <div class="table-responsive">
                    <table class="table table-bordered table-sm border-dark excel-like">
                        <thead>
                            <tr class="text-center">
                                <th class="align-middle" style="width: 40px;">No. of<br>Bags</th>
                                <th class="align-middle" style="width: 40px;">No. of Bags in<br>Mini Pool</th>
                                <th class="align-middle" style="width: 60px;">Donor<br>ID</th>
                                <th class="align-middle" style="width: 40px;">Donation<br>Date</th>
                                <th class="align-middle" style="width: 60px;">Blood<br>Group</th>
                                <th class="align-middle" style="width: 60px;">Bag Volume<br>in ML</th>
                                <th class="align-middle" style="width: 60px;">Mini Pool Bag<br>Volume in Liter</th>
                                <th class="align-middle" style="width: 100px;">Mini Pool Number /<br>Segment No.</th>
                                <th class="align-middle" style="width: 60px;">Tail<br>Cutting<br>Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for ($i = 1; $i <= 72; $i++)
                            <tr>
                                <td class="p-0 text-center">{{ $i }}</td>
                                <td class="p-0 text-center">{{ (($i - 1) % 12) + 1 }}</td>
                                <td class="p-0"><input type="text" class="form-control form-control-sm border-0 px-1" name="donor_id[]"></td>
                                <td class="p-0 date-cell">
                                    <input type="date" class="form-control form-control-sm border-0 px-1 date-input" name="donation_date[]">
                                </td>
                                <td class="p-0">
                                    <select class="form-select form-select-sm border-0 px-1 blood-group-select" name="blood_group[]" style="position: relative;">
                                        <option value="">Select</option>
                                        <option value="A+">A+</option>
                                        <option value="A-">A-</option>
                                        <option value="B+">B+</option>
                                        <option value="B-">B-</option>
                                        <option value="AB+">AB+</option>
                                        <option value="AB-">AB-</option>
                                        <option value="O+">O+</option>
                                        <option value="O-">O-</option>
                                    </select>
                                </td>
                                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 px-1" name="bag_volume[]"></td>
                                @if(($i - 1) % 12 === 0)
                                <td class="p-0 text-center" rowspan="12" style="vertical-align: middle;">
                                    <input type="number" class="form-control form-control-sm border-0 px-1 volume-input" name="mini_pool_bag_volume[]" readonly style="text-align: center; height: 100%;">
                                </td>
                                <td class="p-0 text-center" rowspan="12" style="vertical-align: middle;">
                                    <input type="text" class="form-control form-control-sm border-0 px-1" name="segment_number[]" value="" readonly style="text-align: center; height: 100%;">
                                </td>
                                @endif
                                <td class="p-0 text-center">
                                    <select class="form-select form-select-sm border-0 px-1" name="tail_cutting[]">
                                        <option value="No">No</option>
                                        <option value="Yes">Yes</option>
                                    </select>
                                </td>
                            </tr>
                            @endfor
                            <!-- Total Row -->
                            <tr>
                                <td colspan="6" class="text-end pe-2">
                                    <span class="fw-bold">Total Volume in Liters:</span>
                                </td>
                                <td class="p-0">
                                    <input type="text" class="form-control form-control-sm border-0 px-1 text-end" id="totalVolume" readonly>
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-2">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-sm">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<!-- Load specific versions of jQuery and Select2 known to work well together -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
<script>
    // Execute when document is fully loaded
    $(document).ready(function() {
        console.log("Document ready, initializing Bag Entry page...");

        // Function to reset rejection form
        function resetRejectionForm() {
            $('#rejectionReason').val('');
            $('#rejectionRemarks').val('');
        }

        // Basic Select2 config - initialization will be handled by the global fix
        var selectElements = ['#bloodCentreSelect', '#megaPoolSelect'];

        // Remove all previous event handlers
        $('.date-input, .blood-group-select').off();

        // Handle date input navigation
        $('.date-input').on('keydown', function(e) {
            if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                const nextSelect = $(this).closest('td').next().find('select');
                nextSelect.focus();
                setTimeout(() => {
                    nextSelect.attr('size', '8');
                    nextSelect.css('position', 'absolute');
                }, 0);
            }
        });

        // Handle blood group dropdown
        $('.blood-group-select').on('focus', function() {
            $(this).attr('size', '8');
            $(this).css('position', 'absolute');
        });

        $('.blood-group-select').on('blur', function() {
            $(this).attr('size', '1');
            $(this).css('position', 'relative');
        });

        $('.blood-group-select').on('keydown', function(e) {
            const key = e.key.toLowerCase();
            const select = $(this)[0];

            // Handle arrow keys for dropdown navigation
            if ($(this).attr('size') !== '1') {
                if (key === 'arrowup') {
                    e.preventDefault();
                    const prevIndex = select.selectedIndex > 0 ? select.selectedIndex - 1 : 0;
                    select.selectedIndex = prevIndex;
                    return false;
                }
                if (key === 'arrowdown') {
                    e.preventDefault();
                    const nextIndex = select.selectedIndex < select.options.length - 1 ?
                        select.selectedIndex + 1 : select.selectedIndex;
                    select.selectedIndex = nextIndex;
                    return false;
                }
                if (key === 'arrowleft' || key === 'arrowright') {
                    e.preventDefault();
                    return false;
                }
            }

            // Quick selection for blood groups
            if (key.match(/[abos]/)) {
                const options = Array.from(select.options);
                const match = options.find(opt => opt.text.toLowerCase().startsWith(key));
                if (match) {
                    select.value = match.value;
                    $(this).trigger('change');
                }
            }

            // Handle enter key
            if (key === 'enter') {
                e.preventDefault();
                if (select.selectedIndex > 0) {  // If an option is selected (not the first "Select" option)
                    const selectedValue = select.options[select.selectedIndex].value;
                    select.value = selectedValue;
                    $(this).attr('size', '1');
                    $(this).css('position', 'relative');
                    $(this).trigger('change');
                    $(this).closest('td').next().find('input').focus();
                }
                return false;
            }
        });

        $('.blood-group-select').on('change', function() {
            if ($(this).val()) {
                $(this).attr('size', '1');
                $(this).css('position', 'relative');
                $(this).closest('td').next().find('input').focus();
            }
        });

        console.log("Initializing Select2 elements", selectElements);

        // For AR Number dropdown with AJAX
        $('#arNumberSelect').select2({
            ajax: {
                url: "{{ route('plasma.get-ar-numbers') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        search: params.term,
                        page: params.page || 1
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results || []
                    };
                },
                cache: true
            }
        });

        console.log("AR Number Select2 initialized");

        // Initialize mega pool select specifically
        $('#megaPoolSelect').select2();
        console.log("Mega Pool Select2 explicitly initialized");

        // Add event listener for Donor ID inputs
        $(document).on('input', 'input[name="donor_id[]"]', function() {
            const donorId = $(this).val().trim();
            const tailCuttingSelect = $(this).closest('tr').find('select[name="tail_cutting[]"]');

            if (donorId !== '') {
                tailCuttingSelect.val('Yes');
            } else {
                tailCuttingSelect.val('No');
            }
        });

        // Handle AR No. change event
        $('#arNumberSelect').on('select2:select', function(e) {
            const data = e.params.data;
            if (!data.id) return;

            // Show loader
            $('#loader').show();

            var urlGetByARNum = "{{ route('plasma.get-by-ar-no', ['ar_no' => '__AR_NUM__']) }}";
            var urlGetByARNumURL = urlGetByARNum.replace('__AR_NUM__', data.id);
            console.log("Selected AR Number:", data.id);
            console.log("urlGetByARNumURL URL:", urlGetByARNumURL);

            // Fetch plasma entry details
            $.ajax({
                url: urlGetByARNumURL,
                method: 'GET',
                success: function(response) {
                    console.log('AR No. Response:', response); // Debug log

                    if (response.status === 'success') {
                        // Set blood bank
                        $('#bloodCentreSelect').val(response.data.blood_bank_id).trigger('change');

                        // Set GRN No.
                        if (response.data.grn_no) {
                            $('input[name="grn_no"]').val(response.data.grn_no);
                        }

                        // Set Pickup Date
                        if (response.data.pickup_date) {
                            $('input[name="pickup_date"]').val(response.data.pickup_date);
                        }

                        // Set Mega Pool Numbers dropdown
                        const megaPoolSelect = $('#megaPoolSelect');
                        megaPoolSelect.empty().append('<option value="">Select Mega Pool No.</option>');

                        if (response.data.mega_pool_numbers && response.data.mega_pool_numbers.length > 0) {
                            response.data.mega_pool_numbers.forEach(megaPoolNo => {
                                megaPoolSelect.append(`<option value="${megaPoolNo}">${megaPoolNo}</option>`);
                            });
                            // Refresh Select2 to show the new options
                            megaPoolSelect.trigger('change');
                        }

                        // Debug logs for each field
                        console.log('Work Station:', response.data.work_station);
                        console.log('Pickup Date:', response.data.pickup_date);
                        console.log('Blood Bank ID:', response.data.blood_bank_id);
                        console.log('GRN No:', response.data.grn_no);
                        console.log('Mega Pool Numbers:', response.data.mega_pool_numbers);
                    } else {
                        // Show error message
                        alert('Failed to fetch entry details: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    // Log more detailed error information
                    if (xhr.responseJSON) {
                        console.error('Response JSON:', xhr.responseJSON);
                    }
                    console.error('Status Text:', xhr.statusText);
                    console.error('Status Code:', xhr.status);

                    // Show more detailed error message if available
                    let errorMsg = 'Failed to fetch entry details';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += ': ' + xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    // Hide loader
                    $('#loader').hide();
                }
            });
        });

        // Handle AR No. clear event
        $('#arNumberSelect').on('select2:clear', function() {
            // Clear all related fields
            $('#bloodCentreSelect').val('').trigger('change');
            $('input[name="grn_no"]').val('');
            $('input[name="pickup_date"]').val('');
            $('#megaPoolSelect').empty().append('<option value="">Select Mega Pool No.</option>');
        });

        // Handle Mega Pool selection
        console.log("Setting up megaPoolSelect event handlers");

        // Try a different event binding approach
        $(document).on('select2:select', '#megaPoolSelect', function() {
            console.log("Mega Pool select event triggered (delegated)");
            const megaPoolNo = $(this).val();
            console.log("Mega Pool No (delegated):", megaPoolNo);
            if (megaPoolNo) {
                updateMiniPoolNumbers();
            }
        });

        // Calculate mini pool volumes
        function calculateMiniPoolVolumes() {
            const bagVolumes = document.querySelectorAll('input[name="bag_volume[]"]');
            const miniPoolVolumes = document.querySelectorAll('input[name="mini_pool_bag_volume[]"]');
            let totalVolume = 0;

            // Process each group of 12 bags
            for (let i = 0; i < bagVolumes.length; i += 12) {
                let groupVolume = 0;
                let hasVolume = false;

                // Sum up volumes for the group
                for (let j = i; j < i + 12 && j < bagVolumes.length; j++) {
                    const volume = parseFloat(bagVolumes[j].value) || 0;
                    if (volume > 0) {
                        groupVolume += volume;
                        hasVolume = true;
                    }
                }

                // Convert to liters and set the mini pool volume
                if (hasVolume) {
                    const liters = (groupVolume / 1000).toFixed(2);
                    const miniPoolIndex = Math.floor(i / 12);
                    if (miniPoolVolumes[miniPoolIndex]) {
                        miniPoolVolumes[miniPoolIndex].value = liters;
                        totalVolume += parseFloat(liters);
                    }
                } else {
                    // Clear the volume if no bags have volume
                    const miniPoolIndex = Math.floor(i / 12);
                    if (miniPoolVolumes[miniPoolIndex]) {
                        miniPoolVolumes[miniPoolIndex].value = '';
                    }
                }
            }

            // Update total volume
            document.getElementById('totalVolume').value = totalVolume.toFixed(2);
        }

        // Add event listeners for volume calculations
        document.querySelectorAll('input[name="bag_volume[]"]').forEach(input => {
            input.addEventListener('input', calculateMiniPoolVolumes);
        });

        // Mini pool number updates
        function updateMiniPoolNumbers() {
            const megaPoolNo = $('#megaPoolSelect').val();
            console.log("updateMiniPoolNumbers called with mega pool:", megaPoolNo);

            if (!megaPoolNo) {
                console.log("No mega pool selected, returning");
                return;
            }

            // Show loader
            $('#loader').show();

            console.log("Making AJAX request to get mini pool numbers");

            // Fetch mini pool numbers
            $.ajax({
                url: "{{ route('plasma.get_mini_pool_numbers') }}",
                method: 'GET',
                data: {
                    mega_pool_no: megaPoolNo
                },
                success: function(response) {
                    console.log("Mini pool numbers response:", response);

                    if (response.success) {
                        // Split the comma-separated mini pool numbers
                        const miniPoolNumbers = response.mini_pool_numbers.split(',');
                        console.log("Mini pool numbers:", miniPoolNumbers);

                        const miniPoolInputs = document.querySelectorAll('input[name="segment_number[]"]');
                        console.log("Found mini pool inputs:", miniPoolInputs.length);

                        // Update mini pool number inputs
                        miniPoolInputs.forEach((input, index) => {
                            if (index < miniPoolNumbers.length) {
                                input.value = miniPoolNumbers[index];
                                console.log(`Set mini pool #${index} to ${miniPoolNumbers[index]}`);
                            } else {
                                input.value = '';
                            }
                        });
                    } else {
                        console.error("Failed to fetch mini pool numbers:", response.message);
                        alert('Failed to fetch mini pool numbers: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    alert('Failed to fetch mini pool numbers');
                },
                complete: function() {
                    // Hide loader
                    $('#loader').hide();
                    console.log("Mini pool numbers update complete");
                }
            });
        }
         // Handle Rejection confirmation
        $('#confirmRejection').on('click', function() {
            const reason = $('#rejectionReason').val();
            const remarks = $('#rejectionRemarks').val();
            const megaPoolNo = $('#megaPoolSelect').val();
            const arNo = $('#arNumberSelect').val();

            if (!reason) {
                alert('Please select a reason for rejection');
                return;
            }

            // Show loader
            $('#loader').show();

            // Send rejection request
            $.ajax({
                url: "{{ route('plasma.reject-mega-pool') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    mega_pool_no: megaPoolNo,
                    ar_no: arNo,
                    reject_reason: reason,
                    remarks: remarks
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Check if SweetAlert2 is available
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: `<span style="font-size: 1.0em">Rejection Number <b>${response.destruction_no}</b> has been generated successfully</span>`,
                                showConfirmButton: true
                            });
                        } else {
                            // Fallback to regular alert
                            alert('Mega Pool rejected successfully. Destruction Number: ' + response.destruction_no);
                        }

                        // Reset rejection form fields directly
                        resetRejectionForm();

                        // Reload page to reflect changes
                        window.location.reload();
                    } else {
                        alert('Failed to reject mega pool: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {xhr, status, error});
                    // Log more detailed error information
                    if (xhr.responseJSON) {
                        console.error('Response JSON:', xhr.responseJSON);
                    }
                    console.error('Status Text:', xhr.statusText);
                    console.error('Status Code:', xhr.status);

                    // Show more detailed error message if available
                    let errorMsg = 'Failed to reject mega pool';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg += ': ' + xhr.responseJSON.message;
                    }
                    alert(errorMsg);
                },
                complete: function() {
                    // Hide loader
                    $('#loader').hide();
                }
            });
        });

        // Run our global Select2 fix in case it hasn't been applied yet
        if (typeof window.reinitSelect2 === 'function') {
            window.reinitSelect2();
        }

        console.log("Bag Entry page initialization complete");
    });
</script>
@endpush
@endsection
