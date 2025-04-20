@extends('include.dashboardLayout')

@section('title', 'Plasma Mini Pool and Mega Pool Handling Record')

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
        <div class="card-header py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    {{-- <img src="{{ asset('assets/img/pgblogo.png') }}" alt="" style="max-height: 40px;"> --}}
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-0">Plasma Mini Pool and Mega Pool Handling Record</h5>
                </div>
            </div>
        </div>

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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="small mb-1">Blood Centre Name & City</label>
                            <input type="text" class="form-control form-control-sm" name="blood_centre" value="Aster Prime Hospital Blood Centre Hyderabad">
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
                            <label class="small mb-1">Mega Pool No./Crate No.</label>
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
                            @for ($i = 1; $i <= 36; $i++)
                            <tr>
                                <td class="p-0 text-center">{{ $i }}</td>
                                <td class="p-0 text-center">{{ (($i - 1) % 6) + 1 }}</td>
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
                                            <div class="option" data-value="A">A</div>
                                            <div class="option" data-value="B">B</div>
                                            <div class="option" data-value="AB">AB</div>
                                            <div class="option" data-value="O">O</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 px-1" name="bag_volume[]"></td>
                                <td class="p-0">
                                    @if(($i - 1) % 6 === 2)
                                        <input type="number" class="form-control form-control-sm border-0 px-1 volume-input" name="mini_pool_bag_volume[]" readonly>
                                    @else
                                        <div class="form-control form-control-sm border-0 px-1 bg-light"></div>
                                    @endif
                                </td>
                                <td class="p-0">
                                    @if(($i - 1) % 6 === 2)
                                        <input type="text" class="form-control form-control-sm border-0 px-1" name="segment_number[]" value="" readonly>
                                    @else
                                        <div class="form-control form-control-sm border-0 px-1 bg-light"></div>
                                    @endif
                                </td>
                                <td class="p-0"><input type="text" class="form-control form-control-sm border-0 px-1" name="tail_cutting[]"></td>
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
    
    /* Update the group separator styles for 6-row groups */
    .excel-like tr:nth-child(6n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(6n+1) {
        border-top: 2px solid #000;
    }
    
    /* Update the Mini Pool Bag Volume column styles */
    .excel-like tr:nth-child(6n) td:nth-child(7),
    .excel-like tr:nth-child(6n) td:nth-child(8) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(6n+1) td:nth-child(7),
    .excel-like tr:nth-child(6n+1) td:nth-child(8) {
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
    .excel-like tr:nth-child(6n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(6n+1) {
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
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle custom dropdowns
        document.querySelectorAll('.custom-dropdown').forEach(dropdown => {
            const display = dropdown.querySelector('.dropdown-display');
            const options = dropdown.querySelector('.dropdown-options');
            const hiddenInput = dropdown.querySelector('input[type="hidden"]');
            const selectedValue = dropdown.querySelector('.selected-value');
            const allOptions = Array.from(options.querySelectorAll('.option'));

            // Toggle dropdown on click/focus
            display.addEventListener('mousedown', (e) => {
                e.preventDefault();
                e.stopPropagation();
                options.classList.toggle('show');
            });

            display.addEventListener('focus', (e) => {
                e.stopPropagation();
                options.classList.add('show');
            });

            // Handle keyboard navigation
            display.addEventListener('keydown', (e) => {
                e.stopPropagation();
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    options.classList.toggle('show');
                } else if (e.key === 'Tab') {
                    options.classList.remove('show');
                } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    let currentIndex = allOptions.findIndex(opt => opt.classList.contains('selected'));
                    
                    if (currentIndex === -1) {
                        currentIndex = 0;
                    }

                    if (e.key === 'ArrowDown' && currentIndex < allOptions.length - 1) {
                        currentIndex++;
                    } else if (e.key === 'ArrowUp' && currentIndex > 0) {
                        currentIndex--;
                    }

                    allOptions.forEach(opt => opt.classList.remove('selected'));
                    allOptions[currentIndex].classList.add('selected');
                    
                    const value = allOptions[currentIndex].getAttribute('data-value');
                    hiddenInput.value = value;
                    selectedValue.textContent = value || 'Select';
                }
            });

            // Handle option selection
            allOptions.forEach(option => {
                option.addEventListener('mousedown', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    const value = option.getAttribute('data-value');
                    hiddenInput.value = value;
                    selectedValue.textContent = value || 'Select';
                    options.classList.remove('show');
                    
                    allOptions.forEach(opt => opt.classList.remove('selected'));
                    option.classList.add('selected');
                    
                    const currentRow = dropdown.closest('tr');
                    const nextInput = currentRow.querySelector('input[name="bag_volume[]"]');
                    if (nextInput) {
                        nextInput.focus();
                    }
                });
            });

            // Close dropdown when clicking outside
            document.addEventListener('mousedown', (e) => {
                if (!dropdown.contains(e.target)) {
                    options.classList.remove('show');
                }
            }, true);
        });

        // Handle keyboard navigation from date to blood group
        document.querySelectorAll('input[type="date"]').forEach(dateInput => {
            dateInput.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    const currentRow = dateInput.closest('tr');
                    const bloodGroupDropdown = currentRow.querySelector('.custom-dropdown .dropdown-display');
                    if (bloodGroupDropdown) {
                        bloodGroupDropdown.focus();
                    }
                }
            });
        });

        // Auto-focus first Donor ID cell on page load
        const firstDonorIdInput = document.querySelector('input[name="donor_id[]"]');
        if (firstDonorIdInput) {
            firstDonorIdInput.focus();
        }

        // Calculate mini pool volumes
        function calculateMiniPoolVolumes() {
            const bagVolumes = document.querySelectorAll('input[name="bag_volume[]"]');
            const miniPoolVolumes = document.querySelectorAll('input[name="mini_pool_bag_volume[]"]');
            let totalVolume = 0;

            // Process each group of 6 bags
            for (let i = 0; i < bagVolumes.length; i += 6) {
                let groupVolume = 0;
                let hasVolume = false;

                // Sum up volumes for the group
                for (let j = i; j < i + 6 && j < bagVolumes.length; j++) {
                    const volume = parseFloat(bagVolumes[j].value) || 0;
                    if (volume > 0) {
                        groupVolume += volume;
                        hasVolume = true;
                    }
                }

                // Convert to liters and set the mini pool volume
                if (hasVolume) {
                    const liters = (groupVolume / 1000).toFixed(2);
                    const miniPoolIndex = Math.floor(i / 6);
                    if (miniPoolVolumes[miniPoolIndex]) {
                        miniPoolVolumes[miniPoolIndex].value = liters;
                        totalVolume += parseFloat(liters);
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
            console.log('Mega Pool No:', megaPoolNo); // Debug log
            
            if (megaPoolNo) {
                // Extract the base number (year + month + workstation + serial)
                const baseNumber = megaPoolNo.replace('MG', '');
                miniPoolInputs.forEach((input, index) => {
                    // Generate mini pool number by appending 01-06 to the base number
                    const miniPoolNo = baseNumber + padNumber((index + 1).toString(), 2, '0');
                    console.log('Generated Mini Pool No:', miniPoolNo); // Debug log
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