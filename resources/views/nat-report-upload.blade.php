@extends('include.dashboardLayout')

@section('title', 'Upload NAT Reports')

@section('content')
<div class="pagetitle">
    <h1>NAT Report Upload</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">NAT Report Upload</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Upload NAT Report Files</h5>

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

            <form action="{{ route('nat-report.generate') }}" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                @csrf
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file1" class="form-label">Upload Donor Results File</label>
                            <input type="file" class="form-control" id="file1" name="file1" accept=".xlsx,.xls" required>
                            <div class="invalid-feedback">Please select the Donor Results File.</div>
                            <small class="form-text text-muted">Supported formats: .xlsx, .xls</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="file2" class="form-label">Upload Invalid Results File</label>
                            <input type="file" class="form-control" id="file2" name="file2" accept=".xlsx,.xls" required>
                            <div class="invalid-feedback">Please select Upload Invalid Results File.</div>
                            <small class="form-text text-muted">Supported formats: .xlsx, .xls</small>
                        </div>
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Generate Report
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

@if(isset($metadataBlocks) && isset($groupedSamples))
<section class="section">
    <div class="card">
        <div class="card-body">
            @php
                $donorResultsCount = 0;
                $invalidResultsCount = 0;
            @endphp

            {{-- <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">NAT Test Results Summary</h5>
                <div class="export-buttons">
                    <button class="btn btn-outline-primary me-2">
                        <i class="bi bi-download me-1"></i> Export All
                    </button>
                </div>
            </div> --}}

            <ul class="nav nav-tabs nav-tabs-bordered" id="borderedTab" role="tablist">
                @foreach($metadataBlocks as $index => $meta)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $index === 0 ? 'active' : '' }}" 
                                id="tab-{{ $index }}" 
                                data-bs-toggle="tab" 
                                data-bs-target="#content-{{ $index }}" 
                                type="button" 
                                role="tab" 
                                aria-controls="content-{{ $index }}" 
                                aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                            @if(strpos($meta['source'], 'Donor Results Report') !== false)
                                @php $donorResultsCount++; @endphp
                                <i class="bi bi-file-earmark-text me-1"></i> Donor Results Report {{ $donorResultsCount }}
                            @elseif(strpos($meta['source'], 'Invalid Test Results') !== false)
                                @php $invalidResultsCount++; @endphp
                                <i class="bi bi-file-earmark-excel me-1"></i> Invalid Test Results {{ $invalidResultsCount }}
                            @else
                                {{ $meta['source'] }}
                            @endif
                        </button>
                    </li>
                @endforeach
                <li class="nav-item" role="presentation">
                    <button class="nav-link" 
                            id="tab-results" 
                            data-bs-toggle="tab" 
                            data-bs-target="#content-results" 
                            type="button" 
                            role="tab" 
                            aria-controls="content-results" 
                            aria-selected="false">
                        <i class="bi bi-table me-1"></i> Results
                    </button>
                </li>
            </ul>

            <div class="tab-content pt-3" id="borderedTabContent">
                @foreach($metadataBlocks as $index => $meta)
                    <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" 
                         id="content-{{ $index }}" 
                         role="tabpanel" 
                         aria-labelledby="tab-{{ $index }}">
                        
                        <div class="card border-primary mb-4">
                            <div class="card-header text-white fw-bold" style="background-color: #0c4c90;">
                                <i class="bi bi-info-circle me-1"></i> Test Metadata
                            </div>
                            <div class="card-body small">
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <tbody>
                                            <tr>
                                                <th class="text-nowrap" style="width: 150px;"><i class="bi bi-cpu me-1"></i> Analyzer</th>
                                                <td>{{ $meta['Analyzer'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-clipboard-check me-1"></i> Test</th>
                                                <td>{{ $meta['Test'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-shield-x me-1"></i> Negative Control</th>
                                                <td>{{ $meta['Negative control (batch) ID'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-person me-1"></i> Test Operator</th>
                                                <td>{{ $meta['Test operator'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-check-circle me-1"></i> Validated By</th>
                                                <td>{{ $meta['Validated by'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-calendar-check me-1"></i> Validated On</th>
                                                <td>{{ $meta['Validated on'] }}</td>
                                            </tr>
                                            <tr>
                                                <th class="text-nowrap"><i class="bi bi-flag me-1"></i> Flags</th>
                                                <td>{{ $meta['Flags'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover table-sm" id="table-{{ $index }}">
                                <thead>
                                    <tr style="background-color: #f35c24; color: white;">
                                        <th>Tube ID</th>
                                        <th>HIV</th>
                                        <th>HBV</th>
                                        <th>HCV</th>
                                        <th>Status</th>
                                        <th>Timestamp</th>
                                        <th>Analyzer</th>
                                        <th>Operator</th>
                                        <th>Flags</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($groupedSamples as $s)
                                        @php
                                            $currentSource = $meta['source'];
                                            $sampleSource = $s['source'];
                                            $isMatch = false;
                                            
                                            if (strpos($currentSource, 'Donor Results Report') !== false && 
                                                strpos($sampleSource, 'Donor Results Report') !== false) {
                                                $isMatch = true;
                                            } elseif (strpos($currentSource, 'Invalid Test Results') !== false && 
                                                     strpos($sampleSource, 'Invalid Test Results') !== false) {
                                                $isMatch = true;
                                            }
                                        @endphp
                                        
                                        @if($isMatch)
                                            <tr>
                                                <td>{{ $s['tube_id'] }}</td>
                                                <td>{{ $s['HIV'] }}</td>
                                                <td>{{ $s['HBV'] }}</td>
                                                <td>{{ $s['HCV'] }}</td>
                                                <td>
                                                    @if($s['result'] === 'Reactive')
                                                        <span class="badge bg-warning text-dark">⚠️ Reactive</span>
                                                    @elseif($s['result'] === 'Invalid')
                                                        <span class="badge bg-danger">❌ Invalid</span>
                                                    @else
                                                        <span class="badge bg-success">✅ Non-Reactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $s['timestamp'] }}</td>
                                                <td>{{ $s['analyzer'] }}</td>
                                                <td>{{ $s['operator'] }}</td>
                                                <td>{{ $s['flags'] }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

                <!-- Results Tab -->
                <div class="tab-pane fade" id="content-results" role="tabpanel" aria-labelledby="tab-results">
                  
                    <div class="card border-primary mb-4">
                        <div class="card-header text-white fw-bold d-flex justify-content-between align-items-center" style="background-color: #0c4c90;">
                            <div>
                                <i class="bi bi-table me-1"></i> Test Metadata Summary
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-light me-2" id="exportCSV">
                                    <i class="bi bi-file-earmark-excel me-1"></i> CSV
                                </button>
                                <button class="btn btn-sm btn-outline-light" id="exportPDF">
                                    <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm" id="natTable">
                                    <thead>
                                        <tr style="background-color: #f35c24; color: white;">
                                            <th>Tube ID</th>
                                            <th>HIV</th>
                                            <th>HBV</th>
                                            <th>HCV</th>
                                            <th>Status</th>
                                            <th>Timestamp</th>
                                            <th>Analyzer</th>
                                            <th>Operator</th>
                                            <th>Flags</th>
                                            <th>Source</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($groupedSamples as $s)
                                            <tr>
                                                <td>{{ $s['tube_id'] }}</td>
                                                <td>{{ $s['HIV'] }}</td>
                                                <td>{{ $s['HBV'] }}</td>
                                                <td>{{ $s['HCV'] }}</td>
                                                <td>
                                                    @if($s['result'] === 'Reactive')
                                                        <span class="badge bg-warning text-dark">⚠️ Reactive</span>
                                                    @elseif($s['result'] === 'Invalid')
                                                        <span class="badge bg-danger">❌ Invalid</span>
                                                    @else
                                                        <span class="badge bg-success">✅ Non-Reactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $s['timestamp'] }}</td>
                                                <td>{{ $s['analyzer'] }}</td>
                                                <td>{{ $s['operator'] }}</td>
                                                <td>{{ $s['flags'] }}</td>
                                                <td><small>{{ $s['source'] }}</small></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2({
            theme: 'bootstrap-5'
        });

        // Initialize DataTables for each tab
        @foreach($metadataBlocks as $index => $meta)
            $('#table-{{ $index }}').DataTable({
                dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rtip',
                buttons: [
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                        className: 'btn btn-outline-primary me-2'
                    },
                    {
                        extend: 'pdf',
                        text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                        className: 'btn btn-outline-primary me-2'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer me-1"></i> Print',
                        className: 'btn btn-outline-primary'
                    }
                ],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                responsive: true,
                language: {
                    search: '<i class="bi bi-search me-1"></i>',
                    searchPlaceholder: 'Search...',
                    lengthMenu: '<i class="bi bi-list me-1"></i> Show _MENU_ entries',
                    info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                    paginate: {
                        first: '<i class="bi bi-chevron-double-left"></i>',
                        previous: '<i class="bi bi-chevron-left"></i>',
                        next: '<i class="bi bi-chevron-right"></i>',
                        last: '<i class="bi bi-chevron-double-right"></i>'
                    }
                }
            });
        @endforeach

        // Initialize the complete results table
        const natTable = $('#natTable').DataTable({
            dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex align-items-center"l><"d-flex align-items-center"f>>rtip',
            buttons: [
                {
                    extend: 'csv',
                    text: '<i class="bi bi-file-earmark-excel me-1"></i> CSV',
                    className: 'btn btn-outline-primary me-2',
                    filename: 'NAT Test Reports'
                },
                {
                    extend: 'pdf',
                    text: '<i class="bi bi-file-earmark-pdf me-1"></i> PDF',
                    className: 'btn btn-outline-primary me-2',
                    filename: 'NAT Test Reports',
                    orientation: 'landscape',
                    pageSize: 'A4',
                    customize: function(doc) {
                        // Add two-column header
                        doc.content.splice(0, 0, {
                            columns: [
                                {
                                    image: 'data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/img/pgblogo.png'))) }}',
                                    width: 100,
                                    alignment: 'left'
                                },
                                {
                                    text: 'NAT Test Reports',
                                    style: 'header',
                                    alignment: 'right'
                                }
                            ],
                            margin: [40, 20, 40, 20]
                        });

                        // Add date
                        doc.content.splice(1, 0, {
                            text: 'Generated on: ' + new Date().toLocaleDateString(),
                            style: 'subheader',
                            alignment: 'right',
                            margin: [40, 0, 40, 20]
                        });

                        // Style definitions
                        doc.styles = {
                            header: {
                                fontSize: 18,
                                bold: true,
                                color: '#0c4c90'
                            },
                            subheader: {
                                fontSize: 12,
                                bold: true,
                                color: '#666666'
                            },
                            tableHeader: {
                                bold: true,
                                fontSize: 11,
                                color: '#ffffff',
                                fillColor: '#0c4c90',
                                alignment: 'center'
                            },
                            tableBody: {
                                fontSize: 10,
                                color: '#333333'
                            }
                        };

                        // Find the table in the content
                        const tableIndex = doc.content.findIndex(item => item.table);
                        if (tableIndex !== -1) {
                            // Table styling
                            doc.content[tableIndex].table.widths = Array(doc.content[tableIndex].table.body[0].length).fill('*');
                            doc.content[tableIndex].table.body[0].forEach(function(cell, i) {
                                cell.style = 'tableHeader';
                            });

                            // Apply body style to all cells
                            for (let i = 1; i < doc.content[tableIndex].table.body.length; i++) {
                                doc.content[tableIndex].table.body[i].forEach(function(cell) {
                                    cell.style = 'tableBody';
                                });
                            }

                            // Add table borders
                            doc.content[tableIndex].table.body.forEach(function(row, i) {
                                row.forEach(function(cell) {
                                    cell.border = [true, true, true, true];
                                });
                            });
                        }

                        // Page margins
                        doc.pageMargins = [40, 60, 40, 60];
                        
                        // Default style
                        doc.defaultStyle = {
                            fontSize: 10,
                            color: '#333333'
                        };

                        // Footer
                        doc.footer = function(currentPage, pageCount) {
                            return {
                                text: 'Page ' + currentPage.toString() + ' of ' + pageCount,
                                alignment: 'center',
                                margin: [0, 20, 0, 0],
                                fontSize: 10,
                                color: '#666666'
                            };
                        };
                    }
                },
                {
                    extend: 'print',
                    text: '<i class="bi bi-printer me-1"></i> Print',
                    className: 'btn btn-outline-primary'
                }
            ],
            pageLength: 25,
            lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            order: [[0, 'asc']],
            responsive: true,
            language: {
                search: '<i class="bi bi-search me-1"></i>',
                searchPlaceholder: 'Search...',
                lengthMenu: '<i class="bi bi-list me-1"></i> Show _MENU_ entries',
                info: 'Showing _START_ to _END_ of _TOTAL_ entries',
                paginate: {
                    first: '<i class="bi bi-chevron-double-left"></i>',
                    previous: '<i class="bi bi-chevron-left"></i>',
                    next: '<i class="bi bi-chevron-right"></i>',
                    last: '<i class="bi bi-chevron-double-right"></i>'
                }
            }
        });

        // Add click handlers for the export buttons
        $('#exportCSV').on('click', function() {
            natTable.button('.buttons-csv').trigger();
        });

        $('#exportPDF').on('click', function() {
            natTable.button('.buttons-pdf').trigger();
        });

        // Highlight duplicates functionality
        $('#highlight-duplicates').on('click', function () {
            $('td.duplicate').removeClass('duplicate');

            const seen = {};
            const duplicates = [];

            natTable.column(0, {search: 'applied'}).nodes().each(function (cell, i) {
                const val = $(cell).text().trim();
                if (seen[val]) {
                    duplicates.push(val);
                } else {
                    seen[val] = true;
                }
            });

            natTable.column(0).nodes().each(function (cell, i) {
                const val = $(cell).text().trim();
                if (duplicates.includes(val)) {
                    $(cell).addClass('duplicate');
                }
            });
        });
    });
</script>
@endpush

@push('styles')
<link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .badge {
        font-size: 0.9em;
        padding: 0.5em 0.8em;
    }
    .duplicate {
        background-color: #ffb3b3 !important;
        font-weight: bold;
    }
    .card {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .table-responsive {
        margin-top: 20px;
    }
    .nav-tabs .nav-link {
        color: #495057;
        padding: 0.75rem 1.25rem;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: bold;
        border-bottom: 3px solid #0d6efd;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .dataTables_wrapper .dataTables_length,
    .dataTables_wrapper .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_wrapper .dataTables_info {
        padding-top: 1rem;
    }
    .btn-outline-primary {
        border-color: #0c4c90;
        color: #0c4c90;
    }
    .btn-outline-primary:hover {
        background-color: #0c4c90;
        color: white;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-text {
        font-size: 0.875rem;
    }
    .card-header {
        background-color: #0c4c90 !important;
    }
    .table-sm th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .table-sm td {
        vertical-align: middle;
    }
    .table-sm tr {
        border-bottom: 1px solid #dee2e6;
    }
    .table-sm tr:last-child {
        border-bottom: none;
    }
    .table-bordered {
        border-color: #dee2e6;
    }
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(13, 110, 253, 0.02);
    }
    .table thead th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }
    .table tbody td {
        font-size: 0.9rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        padding: 0.5rem 0.75rem;
        margin: 0 0.25rem;
        border-radius: 0.25rem;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #0c4c90 !important;
        color: white !important;
        border: none !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: #0c4c90 !important;
        color: white !important;
        border: none !important;
    }
    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
    .dataTables_wrapper .dataTables_length select {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
    }
</style>
@endpush
@endif
@endsection 