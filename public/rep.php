<?php
// rep.php

define('PROJECT_ROOT', __DIR__);
$pythonExec = 'C:\\Users\\salma\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';  // Updated Python path

function quoteWin(string $s): string {
    return '"' . str_replace('"','""',$s) . '"';
}

function parse_pdf(string $pdfPath): array {
    global $pythonExec;
    $script = PROJECT_ROOT . '\\parse_res.py';
    $cmd    = quoteWin($pythonExec)
            . ' ' . quoteWin($script)
            . ' ' . quoteWin($pdfPath)
            . ' 2>&1';
    $raw    = shell_exec($cmd);
    $json   = json_decode($raw, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'error' => json_last_error_msg(),
            'cmd'   => $cmd,
            'raw'   => $raw
        ];
    }
    return ['data' => $json];
}

/**
 * Render a Bootstrap table with Sl.No prepended.
 */
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
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>RES Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-4">
  <h1 class="mb-4">PharmIT_ BioRad - PDF Decryptor Dashboard</h1>

  <!-- Upload Form -->
  <form method="post" enctype="multipart/form-data" class="row g-3 mb-4">
    <div class="col-auto">
      <input type="file" name="resfiles[]" multiple accept="application/pdf,.pdf" class="form-control" />
      <small class="form-text text-muted">Allowed file types: PDF (.pdf)</small>
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-primary">Upload &amp; Parse</button>
    </div>
  </form>

  <?php if (!empty($_FILES['resfiles'])): ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="pdfTab" role="tablist">
      <?php foreach ($_FILES['resfiles']['name'] as $i => $name): ?>
        <li class="nav-item" role="presentation">
          <button class="nav-link <?php echo $i===0?'active':''?>"
                  id="tab-<?php echo $i?>-tab"
                  data-bs-toggle="tab"
                  data-bs-target="#tab-<?php echo $i?>"
                  type="button" role="tab"
                  aria-controls="tab-<?php echo $i?>"
                  aria-selected="<?php echo $i===0?'true':'false'?>">
            <?php echo htmlspecialchars($name)?>
          </button>
        </li>
      <?php endforeach; ?>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content border border-top-0 p-3" id="pdfTabContent">
      <?php foreach ($_FILES['resfiles']['tmp_name'] as $i => $tmpPath):
          $name   = $_FILES['resfiles']['name'][$i];
          $result = parse_pdf($tmpPath);
      ?>
        <div class="tab-pane fade <?php echo $i===0?'show active':''?>"
             id="tab-<?php echo $i?>" role="tabpanel"
             aria-labelledby="tab-<?php echo $i?>-tab">

          <?php if (!empty($result['error'])): ?>
            <div class="alert alert-danger">
              <strong>Error:</strong> <?php echo htmlspecialchars($result['error'])?>
            </div>
            <h5>Command run:</h5>
            <pre><?php echo htmlspecialchars($result['cmd'])?></pre>
            <h5>Raw Python Output:</h5>
            <pre><?php echo htmlspecialchars($result['raw'])?></pre>
          <?php else:
            $d = $result['data'];

            // Summary counts for Sample Results
            $sr = $d['sample_results'] ?? [];
            $reactCnt = $nonReactCnt = $equivCnt = 0;
            foreach ($sr as $r) {
              $res = strtoupper(trim($r['Result'] ?? ''));
              if ($res === 'REACTIVE') $reactCnt++;
              elseif ($res === 'NEG')      $nonReactCnt++;
              elseif ($res === '???')      $equivCnt++;
            }
          ?>
            <!-- Summary badges -->
            <div class="mb-4">
              <span class="badge bg-danger me-2">Reactive: <?php echo $reactCnt?></span>
              <span class="badge bg-success me-2">Non-Reactive: <?php echo $nonReactCnt?></span>
              <span class="badge bg-warning text-dark">Equivocal (???): <?php echo $equivCnt?></span>
            </div>

            <!-- Qualitative Results -->
            <div class="card mb-4">
              <div class="card-header bg-secondary text-white">
                Qualitative Results
              </div>
              <div class="card-body">
                <pre class="mb-0"><?php echo htmlspecialchars(implode("\n", $d['qualitative'] ?? []))?></pre>
              </div>
            </div>

            <!-- Negative Controls -->
            <h5>Negative Controls</h5>
            <?php render_table($d['negative_controls'] ?? [])?>

            <!-- Positive Controls -->
            <h5 class="mt-4">Positive Controls</h5>
            <?php render_table($d['positive_controls'] ?? [])?>

            <!-- Sample Results -->
            <h5 class="mt-4">Sample Results</h5>
            <?php render_table($sr)?>
          <?php endif; ?>

        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Bootstrap JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 