<!DOCTYPE html>
<html>
<head>
    <title>NAT Summary</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/2.3.6/css/buttons.dataTables.min.css" rel="stylesheet"/>
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
    </style>
</head>
<body class="p-4">
    <div class="container-fluid">
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3 mb-4">
            @foreach($metadataBlocks as $meta)
                <div class="col">
                    <div class="card border-primary h-100">
                        <div class="card-header bg-primary text-white fw-bold">
                            {{ $meta['source'] }}
                        </div>
                        <div class="card-body small">
                            <p><strong>Analyzer:</strong> {{ $meta['Analyzer'] }}</p>
                            <p><strong>Test:</strong> {{ $meta['Test'] }}</p>
                            <p><strong>Negative Control (Batch ID):</strong> {{ $meta['Negative control (batch) ID'] }}</p>
                            <p><strong>Test Operator:</strong> {{ $meta['Test operator'] }}</p>
                            <p><strong>Validated By:</strong> {{ $meta['Validated by'] }}</p>
                            <p><strong>Validated On:</strong> {{ $meta['Validated on'] }}</p>
                            <p><strong>Flags:</strong> {{ $meta['Flags'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm" id="natTable">
                <thead class="table-dark">
                    <tr>
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

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            const table = $('#natTable').DataTable({
                dom: 'lBfrtip',
                buttons: ['copy', 'csv', 'excel', 'pdf', 'print'],
                pageLength: 25,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                responsive: true
            });

            $('#highlight-duplicates').on('click', function () {
                $('td.duplicate').removeClass('duplicate');

                const seen = {};
                const duplicates = [];

                table.column(0, {search: 'applied'}).nodes().each(function (cell, i) {
                    const val = $(cell).text().trim();
                    if (seen[val]) {
                        duplicates.push(val);
                    } else {
                        seen[val] = true;
                    }
                });

                table.rows().nodes().each(function (row) {
                    const td = $('td:eq(0)', row);
                    const val = td.text().trim();
                    if (duplicates.includes(val)) {
                        td.addClass('duplicate');
                    }
                });
            });
        });
    </script>
</body>
</html> 