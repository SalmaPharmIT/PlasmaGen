@extends('include.dashboardLayout')

@section('title', 'RES File Parser')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">BioRad RES File Parser</h5>
                    
                    <!-- Upload Form -->
                    <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
                        @csrf
                        <div class="col-auto">
                            <input type="file" name="resfiles[]" multiple accept="application/pdf,.pdf" class="form-control" />
                            <small class="text-muted">Allowed file types: PDF (.pdf)</small>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">Upload &amp; Parse</button>
                        </div>
                    </form>

                    @php
                    function quoteWin(string $s): string {
                        return '"' . str_replace('"','""',$s) . '"';
                    }

                    function parse_pdf(string $pdfPath): array {
                        $pythonExec = env('PYTHON_PATH', 'python'); // Use env variable or default to 'python'
                        $script = public_path('parse_res.py');
                        $cmd = quoteWin($pythonExec)
                                . ' ' . quoteWin($script)
                                . ' ' . quoteWin($pdfPath)
                                . ' 2>&1';
                        $raw = shell_exec($cmd);
                        $json = json_decode($raw, true);
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            return [
                                'error' => json_last_error_msg(),
                                'cmd'   => $cmd,
                                'raw'   => $raw
                            ];
                        }
                        return ['data' => $json];
                    }

                    function render_table(array $rows): void {
                        if (empty($rows)) {
                            echo '<p class="text-muted"><em>(no data)</em></p>';
                            return;
                        }
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped table-bordered">';
                        echo '<thead class="table-light"><tr>';
                        echo '<th>Sl.No</th>';
                        foreach (array_keys($rows[0]) as $col) {
                            echo '<th>' . htmlspecialchars($col) . '</th>';
                        }
                        echo '</tr></thead><tbody>';
                        $i = 1;
                        foreach ($rows as $r) {
                            $isReactive = !empty($r['Result']) && stripos($r['Result'], 'reactive') !== false;
                            echo $isReactive ? '<tr class="table-danger">' : '<tr>';
                            echo '<td>' . $i++ . '</td>';
                            foreach ($r as $c) {
                                echo '<td>' . htmlspecialchars($c) . '</td>';
                            }
                            echo '</tr>';
                        }
                        echo '</tbody></table></div>';
                    }
                    @endphp

                    @if (!empty($_FILES['resfiles']))
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" id="pdfTab" role="tablist">
                            @foreach ($_FILES['resfiles']['name'] as $i => $name)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link {{ $i === 0 ? 'active' : '' }}"
                                            id="tab-{{ $i }}-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#tab-{{ $i }}"
                                            type="button" role="tab"
                                            aria-controls="tab-{{ $i }}"
                                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}">
                                        {{ $name }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content border border-top-0 p-3" id="pdfTabContent">
                            @foreach ($_FILES['resfiles']['tmp_name'] as $i => $tmpPath)
                                @php
                                    $name = $_FILES['resfiles']['name'][$i];
                                    $result = parse_pdf($tmpPath);
                                @endphp
                                <div class="tab-pane fade {{ $i === 0 ? 'show active' : '' }}"
                                    id="tab-{{ $i }}" role="tabpanel"
                                    aria-labelledby="tab-{{ $i }}-tab">

                                    @if (!empty($result['error']))
                                        <div class="alert alert-danger">
                                            <strong>Error:</strong> {{ $result['error'] }}
                                        </div>
                                        <h5>Command run:</h5>
                                        <pre>{{ $result['cmd'] }}</pre>
                                        <h5>Raw Python Output:</h5>
                                        <pre>{{ $result['raw'] }}</pre>
                                    @else
                                        @php
                                            $d = $result['data'];
                                            
                                            // Summary counts for Sample Results
                                            $sr = $d['sample_results'] ?? [];
                                            $reactCnt = $nonReactCnt = $equivCnt = 0;
                                            foreach ($sr as $r) {
                                                $res = strtoupper(trim($r['Result'] ?? ''));
                                                if ($res === 'REACTIVE') $reactCnt++;
                                                elseif ($res === 'NONREACTIVE') $nonReactCnt++;
                                                elseif ($res === 'BORDERLINE') $equivCnt++;
                                            }
                                        @endphp
                                        
                                        <!-- Summary badges -->
                                        <div class="mb-4">
                                            <span class="badge bg-danger me-2">Reactive: {{ $reactCnt }}</span>
                                            <span class="badge bg-success me-2">Non-Reactive: {{ $nonReactCnt }}</span>
                                            <span class="badge bg-warning text-dark">Borderline: {{ $equivCnt }}</span>
                                        </div>

                                        <!-- Qualitative Results -->
                                        <div class="card mb-4">
                                            <div class="card-header bg-secondary text-white">
                                                Qualitative Results
                                            </div>
                                            <div class="card-body">
                                                <pre class="mb-0">{{ implode("\n", $d['qualitative'] ?? []) }}</pre>
                                            </div>
                                        </div>

                                        <!-- Negative Controls -->
                                        <h5>Negative Controls</h5>
                                        @php render_table($d['negative_controls'] ?? []); @endphp

                                        <!-- Positive Controls -->
                                        <h5 class="mt-4">Positive Controls</h5>
                                        @php render_table($d['positive_controls'] ?? []); @endphp

                                        <!-- Sample Results -->
                                        <h5 class="mt-4">Sample Results</h5>
                                        @php render_table($sr); @endphp
                                        
                                        <!-- Import to System Button -->
                                        <div class="mt-4">
                                            <form action="{{ route('report.store') }}" method="POST" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="test_type" value="{{ $d['test_type'] ?? 'HBV' }}">
                                                <input type="hidden" name="parsed_data" value="{{ json_encode($d) }}">
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-database-add"></i> Import to System
                                                </button>
                                            </form>
                                            
                                            <a href="{{ route('report.upload') }}" class="btn btn-secondary ms-2">
                                                <i class="bi bi-arrow-left"></i> Back to ELISA Upload
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 