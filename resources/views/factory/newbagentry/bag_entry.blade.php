@extends('include.dashboardLayout')

@section('title', 'Plasma Mini Pool and Mega Pool Handling Record')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    
    .custom-dropdown {
        position: relative;
        width: 100%;
        height: 24px;
    }
    
    .dropdown-display {
        width: 100%;
        height: 100%;
        padding: 0 2px;
        display: flex;
        align-items: center;
        cursor: pointer;
        background: transparent;
        font-size: 0.8rem;
    }
    
    .dropdown-display:focus {
        outline: none;
        background: #fff;
    }
    
    .dropdown-options {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #86b7fe;
        z-index: 1000;
        max-height: 120px;
        overflow-y: auto;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: none;
    }
    
    .dropdown-options.show {
        display: block;
    }
    
    .option {
        padding: 2px 4px;
        cursor: pointer;
        font-size: 0.8rem;
        transition: none;
    }
    
    .option:hover {
        background-color: #f8f9fa;
    }
    
    .option.selected {
        background-color: #e9ecef;
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

    /* Select2 Custom Styles */
    .select2-container--classic .select2-selection--single {
        height: 22px;
        min-height: 22px;
        padding: 0 0.2rem;
        font-size: 0.8rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }

    .select2-container--classic .select2-selection--single .select2-selection__rendered {
        line-height: 20px;
        padding-left: 0;
        padding-right: 20px;
        color: #212529;
    }

    .select2-container--classic .select2-selection--single .select2-selection__arrow {
        height: 20px;
    }

    .select2-container--classic .select2-results__option {
        padding: 4px 8px;
        font-size: 0.8rem;
    }

    .select2-container--classic .select2-search--dropdown .select2-search__field {
        border: 1px solid #ced4da;
        font-size: 0.8rem;
        padding: 2px 4px;
    }

    .select2-container--classic .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
    }

    /* Blood Centre Dropdown Styles */
    .select2-container {
        max-width: 300px !important;
        display: block !important;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 2px;
    }
    
    .select2-container--default .select2-selection--single {
        height: 22px !important;
        padding: 0 !important;
        border: 1px solid #ced4da !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 22px !important;
        padding-left: 0.2rem !important;
        font-size: 0.8rem !important;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 20px !important;
    }
    
    .select2-dropdown {
        font-size: 0.8rem !important;
    }
    
    /* Group of 12 styling */
    .excel-like tr:nth-child(12n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) {
        border-top: 2px solid #000;
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
    .excel-like td .form-select-sm {
        padding-right: 20px !important; /* Space for the dropdown arrow */
        background-position: right 2px center;
        background-size: 16px 12px;
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
                            <label class="small mb-1">Blood Centre Name & City</label>
                            <div>
                                <select class="form-control form-control-sm select2" name="blood_centre_id" required>
                                    <option value="">Select Blood Centre</option>
                                    @forelse($bloodCenters as $center)
                                        <option value="{{ $center->id }}">{{ $center->name }} - {{ $center->city }}</option>
                                    @empty
                                        <option value="" disabled>No blood centers available</option>
                                    @endforelse
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">Work Station No.</label>
                            <input type="text" class="form-control form-control-sm" name="work_station" value="01">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">Date</label>
                            <input type="date" class="form-control form-control-sm" name="date" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">Pickup Date</label>
                            <input type="date" class="form-control form-control-sm" name="pickup_date" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">A.R. No.</label>
                            <input type="text" class="form-control form-control-sm" name="ar_no" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">GRN No.</label>
                            <input type="text" class="form-control form-control-sm" name="grn_no" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="small mb-1">Mega Pool No.</label>
                            <input type="text" class="form-control form-control-sm" name="mega_pool_no" required>
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
                                <td class="p-0 date-cell"><input type="date" class="form-control form-control-sm border-0 px-1" name="donation_date[]"></td>
                                <td class="p-0">
                                    <div class="custom-dropdown">
                                        <div class="dropdown-display" tabindex="0">
                                            <span class="selected-value">{{ old('blood_group.'.$i, '') }}</span>
                                            <input type="hidden" name="blood_group[]" value="{{ old('blood_group.'.$i, '') }}">
                                        </div>
                                        <div class="dropdown-options">
                                            <div class="option" data-value="">Select</div>
                                            <div class="option" data-value="A+">A+</div>
                                            <div class="option" data-value="A-">A-</div>
                                            <div class="option" data-value="B+">B+</div>
                                            <div class="option" data-value="B-">B-</div>
                                            <div class="option" data-value="AB+">AB+</div>
                                            <div class="option" data-value="AB-">AB-</div>
                                            <div class="option" data-value="O+">O+</div>
                                             <div class="option" data-value="O-">O-</div>
                                        </div>
                                    </div>
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
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
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
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize Select2
        $('.select2').select2({
            placeholder: "Select Blood Centre",
            allowClear: true,
            width: '100%'
        });

        // Blood Group Dropdown Functionality
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const display = dropdown.querySelector('.dropdown-display');
            const options = dropdown.querySelector('.dropdown-options');
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const selectedValue = dropdown.querySelector('.selected-value');

            // Toggle dropdown on click
            display.addEventListener('click', (e) => {
                e.stopPropagation();
                options.classList.toggle('show');
                // Hide other open dropdowns
                document.querySelectorAll('.dropdown-options.show').forEach(openDropdown => {
                    if (openDropdown !== options) {
                        openDropdown.classList.remove('show');
                    }
                });
            });

            // Handle option selection
            options.querySelectorAll('.option').forEach(option => {
                option.addEventListener('click', () => {
                    const value = option.getAttribute('data-value');
                    selectedValue.textContent = value;
                    hiddenInput.value = value;
                    options.classList.remove('show');
                    
                    // Remove selected class from all options
                    options.querySelectorAll('.option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    // Add selected class to clicked option
                    option.classList.add('selected');
                });
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-options.show').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
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

        // Generate mini pool numbers based on mega pool number
        const megaPoolInput = document.querySelector('input[name="mega_pool_no"]');
        const miniPoolInputs = document.querySelectorAll('input[name="segment_number[]"]');

        function updateMiniPoolNumbers() {
            const megaPoolNo = megaPoolInput.value.trim();
            
            if (megaPoolNo) {
                // Extract the base number (year + month + workstation + serial)
                const baseNumber = megaPoolNo.replace('MG', '');
                
                // Generate sequential mini pool numbers
                miniPoolInputs.forEach((input, index) => {
                    // Each mini pool number should be unique within the mega pool
                    const miniPoolNo = baseNumber + padNumber((index + 1).toString(), 2, '0');
                    input.value = miniPoolNo;
                });
            } else {
                // Clear mini pool numbers if mega pool number is empty
                miniPoolInputs.forEach(input => {
                    input.value = '';
                });
            }
        }

        // Helper function to pad numbers with zeros
        function padNumber(n, length, pad) {
            n = n.toString();
            while (n.length < length) {
                n = pad + n;
            }
            return n;
        }

        // Update mini pool numbers when mega pool number changes
        if (megaPoolInput) {
            megaPoolInput.addEventListener('input', updateMiniPoolNumbers);
            megaPoolInput.addEventListener('blur', updateMiniPoolNumbers);
            megaPoolInput.addEventListener('change', updateMiniPoolNumbers);
        }

        // Initial update
        updateMiniPoolNumbers();
    });
</script>
@endpush
@endsection