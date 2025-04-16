@extends('include.dashboardLayout')

@section('title', 'Plasma Mini Pool and Mega Pool Handling Record')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header py-2">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <img src="{{ asset('assets/img/pgblogo.png') }}" alt="" style="max-height: 40px;">
                </div>
                <div class="col-md-6 text-end">
                    <h5 class="mb-0">Plasma Mini Pool and Mega Pool Handling Record</h5>
                </div>
            </div>
        </div>

        <div class="card-body p-2">
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
                            @for ($i = 1; $i <= 48; $i++)
                            <tr>
                                <td class="p-0 text-center">{{ $i }}</td>
                                <td class="p-0 text-center">{{ (($i - 1) % 12) + 1 }}</td>
                                <td class="p-0"><input type="text" class="form-control form-control-sm border-0 px-1" name="donor_id[]"></td>
                                <td class="p-0 date-cell"><input type="date" class="form-control form-control-sm border-0 px-1" name="donation_date[]"></td>
                                <td class="p-0">
                                    <select class="form-control form-control-sm border-0 px-1" name="blood_group[]" style="width: 100%; height: 24px; padding: 0 2px;">
                                        <option value="">Select</option>
                                        <option value="A">A</option>
                                        <option value="B">B</option>
                                        <option value="AB">AB</option>
                                        <option value="O">O</option>
                                    </select>
                                </td>
                                <td class="p-0"><input type="number" class="form-control form-control-sm border-0 px-1" name="bag_volume[]"></td>
                                <td class="p-0">
                                    @if(($i - 1) % 12 === 5)
                                        <input type="number" class="form-control form-control-sm border-0 px-1 volume-input" name="mini_pool_bag_volume[]" readonly>
                                    @else
                                        <div class="form-control form-control-sm border-0 px-1 bg-light"></div>
                                    @endif
                                </td>
                                <td class="p-0">
                                    @if(($i - 1) % 12 === 5)
                                        <input type="text" class="form-control form-control-sm border-0 px-1" name="segment_number[]">
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
    
    /* Add separator only after each 12-row group */
    .excel-like tr:nth-child(12n) {
        border-bottom: 2px solid #000;
    }
    .excel-like tr:nth-child(12n+1) {
        border-top: 2px solid #000;
    }
    
    /* Make Mini Pool Bag Volume column look like one continuous block */
    .excel-like td:nth-child(7) {
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-top: none;
        border-bottom: none;
        padding: 0;
    }
    
    .excel-like td:nth-child(7) .form-control,
    .excel-like td:nth-child(7) .bg-light {
        border-radius: 0;
        height: 100%;
        min-height: 24px;
    }
    
    /* Make Mini Pool Number / Segment No. column look like one continuous block */
    .excel-like td:nth-child(8) {
        border-left: 1px solid #dee2e6;
        border-right: 1px solid #dee2e6;
        border-top: none;
        border-bottom: none;
        padding: 0;
        cursor: pointer;
    }
    
    .excel-like td:nth-child(8) .form-control,
    .excel-like td:nth-child(8) .bg-light {
        border-radius: 0;
        height: 100%;
        min-height: 24px;
    }
    
    /* Add separator only after each 12-row group */
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
    }
    
    .dropdown-display {
        cursor: pointer;
        background: transparent;
        width: 100%;
        height: 22px;
        min-height: 22px;
        padding: 0.1rem 0.2rem;
        font-size: 0.8rem;
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
    }
    
    .dropdown-options.show {
        display: block;
    }
    
    .option {
        padding: 0.2rem 0.4rem;
        cursor: pointer;
        font-size: 0.8rem;
    }
    
    .option:hover {
        background-color: #f8f9fa;
    }
    
    .option.selected {
        background-color: #e9ecef;
    }
    
    /* Remove hover effect for Mini Pool Bag Volume column */
    .excel-like td:nth-child(7):hover {
        background-color: transparent !important;
    }
    
    /* Remove hover effect for Mini Pool Number / Segment No. column */
    .excel-like td:nth-child(8):hover {
        background-color: transparent !important;
    }
    
    /* Add border only to the sum input */
    .excel-like td:nth-child(7) .volume-input {
        border: 1px solid #dee2e6;
        background: #fff;
    }
    
    /* Make empty cells transparent */
    .excel-like td:nth-child(7) .bg-light,
    .excel-like td:nth-child(8) .bg-light {
        background: transparent !important;
    }
    
    /* Remove hover effect for sum columns */
    .excel-like td:nth-child(7):hover,
    .excel-like td:nth-child(8):hover {
        background-color: transparent !important;
    }
    
    /* Remove individual cell focus styles */
    .excel-like td:nth-child(7):focus-within,
    .excel-like td:nth-child(8):focus-within {
        background-color: transparent !important;
    }
    
    .excel-like td:nth-child(7):focus-within input,
    .excel-like td:nth-child(8):focus-within input {
        background: transparent !important;
        border: none !important;
    }
    
    /* Add focus style to the entire 12-row block */
    .excel-like tr:nth-child(12n+1) td:nth-child(7):focus-within ~ tr td:nth-child(7),
    .excel-like tr:nth-child(12n+1) td:nth-child(8):focus-within ~ tr td:nth-child(8) {
        background-color: #e9ecef !important;
    }
    
    /* Make input field visible when focused */
    .excel-like td:nth-child(8) input:focus {
        background: #fff !important;
        border: 1px solid #86b7fe !important;
    }
    .excel-like select {
        background: transparent;
        width: 100%;
        border: none;
        padding: 0.1rem 0.2rem;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        cursor: pointer;
        font-size: 0.8rem;
    }
    
    .excel-like select:focus {
        background: #fff;
        outline: none;
        border: 1px solid #86b7fe;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Make table cells clickable and focusable
        const tableCells = document.querySelectorAll('.excel-like td');
        tableCells.forEach(cell => {
            cell.setAttribute('tabindex', '0');
            
            cell.addEventListener('click', function() {
                const input = this.querySelector('input, select');
                if (input) {
                    input.focus();
                }
            });

            cell.addEventListener('focus', function() {
                const input = this.querySelector('input, select');
                if (input) {
                    input.focus();
                }
            });
        });

        // Handle keyboard navigation and blood group selection
        const inputs = document.querySelectorAll('input, select');
        inputs.forEach((input, index) => {
            // Add tabindex for proper keyboard navigation
            input.setAttribute('tabindex', index + 1);

            // Handle Enter key to move to next input
            input.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const nextIndex = index + 1;
                    if (nextIndex < inputs.length) {
                        inputs[nextIndex].focus();
                    }
                }
            });

            // Handle Arrow keys for navigation
            input.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextIndex = index + 1;
                    if (nextIndex < inputs.length) {
                        inputs[nextIndex].focus();
                    }
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevIndex = index - 1;
                    if (prevIndex >= 0) {
                        inputs[prevIndex].focus();
                    }
                }
            });
        });

        // Handle keyboard navigation for date inputs
        document.querySelectorAll('input[type="date"]').forEach((dateInput, index) => {
            dateInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    const currentRow = this.closest('tr');
                    const bloodGroupSelect = currentRow.querySelector('select[name="blood_group[]"]');
                    if (bloodGroupSelect) {
                        setTimeout(() => {
                            bloodGroupSelect.focus();
                            bloodGroupSelect.click(); // Open the dropdown
                        }, 0);
                    }
                }
            });
        });

        // Handle keyboard navigation for blood group select
        document.querySelectorAll('select[name="blood_group[]"]').forEach((select, index) => {
            // Make select element focusable
            select.setAttribute('tabindex', '0');
            
            select.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === 'Tab') {
                    e.preventDefault();
                    const nextInput = this.closest('tr').querySelector('input[name="bag_volume[]"]');
                    if (nextInput) {
                        nextInput.focus();
                    }
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const currentIndex = this.selectedIndex;
                    if (currentIndex > 0) {
                        this.selectedIndex = currentIndex - 1;
                        this.dispatchEvent(new Event('change'));
                    }
                } else if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const currentIndex = this.selectedIndex;
                    if (currentIndex < this.options.length - 1) {
                        this.selectedIndex = currentIndex + 1;
                        this.dispatchEvent(new Event('change'));
                    }
                }
            });

            // Handle focus to show options
            select.addEventListener('focus', function() {
                this.size = 4; // Show all options
            });

            // Handle blur to hide options
            select.addEventListener('blur', function() {
                this.size = 1; // Show only selected option
            });
        });

        // Auto-focus first Donor ID cell on page load
        const firstDonorIdInput = document.querySelector('input[name="donor_id[]"]');
        if (firstDonorIdInput) {
            firstDonorIdInput.focus();
        }

        // Calculate total volume
        function calculateTotalVolume() {
            let total = 0;
            document.querySelectorAll('.volume-input').forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
            });
            document.getElementById('totalVolume').value = total.toFixed(2);
        }

        // Calculate mini pool volumes
        function calculateMiniPoolVolumes() {
            const bagVolumes = document.querySelectorAll('input[name="bag_volume[]"]');
            const miniPoolVolumes = document.querySelectorAll('input[name="mini_pool_bag_volume[]"]');
            
            // Reset all mini pool volumes
            miniPoolVolumes.forEach(input => {
                input.value = '';
            });
            
            // Calculate sum for each group of 12
            for (let i = 0; i < bagVolumes.length; i += 12) {
                let sum = 0;
                for (let j = i; j < Math.min(i + 12, bagVolumes.length); j++) {
                    const volume = parseFloat(bagVolumes[j].value) || 0;
                    sum += volume;
                }
                
                // Convert ML to Liters and set the value in the middle row
                const liters = (sum / 1000).toFixed(2);
                miniPoolVolumes[Math.floor(i/12)].value = liters;
            }
            
            // Update total volume
            calculateTotalVolume();
        }

        // Add event listeners to volume inputs
        document.querySelectorAll('.volume-input').forEach(input => {
            input.addEventListener('input', calculateTotalVolume);
        });

        // Add event listeners to bag volume inputs
        document.querySelectorAll('input[name="bag_volume[]"]').forEach(input => {
            input.addEventListener('input', calculateMiniPoolVolumes);
        });

        // Initial calculation
        calculateMiniPoolVolumes();

        // Make entire 12-row block clickable for Mini Pool Number / Segment No.
        document.querySelectorAll('.excel-like td:nth-child(8)').forEach(cell => {
            cell.addEventListener('click', function() {
                const rowIndex = this.closest('tr').rowIndex;
                const groupStart = Math.floor((rowIndex - 1) / 12) * 12 + 1;
                const middleRow = groupStart + 5;
                const middleCell = document.querySelector(`.excel-like tr:nth-child(${middleRow}) td:nth-child(8) input`);
                if (middleCell) {
                    middleCell.focus();
                }
            });
        });

        // Add focus style to the entire 12-row block
        document.querySelectorAll('.excel-like td:nth-child(8) input').forEach(input => {
            input.addEventListener('focus', function() {
                const rowIndex = this.closest('tr').rowIndex;
                const groupStart = Math.floor((rowIndex - 1) / 12) * 12 + 1;
                const groupEnd = groupStart + 11;
                
                // Highlight all cells in the group
                for (let i = groupStart; i <= groupEnd; i++) {
                    const cell = document.querySelector(`.excel-like tr:nth-child(${i}) td:nth-child(8)`);
                    if (cell) {
                        cell.style.backgroundColor = '#e9ecef';
                    }
                }
            });

            input.addEventListener('blur', function() {
                const rowIndex = this.closest('tr').rowIndex;
                const groupStart = Math.floor((rowIndex - 1) / 12) * 12 + 1;
                const groupEnd = groupStart + 11;
                
                // Remove highlight from all cells in the group
                for (let i = groupStart; i <= groupEnd; i++) {
                    const cell = document.querySelector(`.excel-like tr:nth-child(${i}) td:nth-child(8)`);
                    if (cell) {
                        cell.style.backgroundColor = '';
                    }
                }
            });
        });
    });
</script>
@endpush
@endsection