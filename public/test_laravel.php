<?php
// test_laravel.php - Test file for Laravel integration

define('PROJECT_ROOT', __DIR__);
$pythonExec = 'C:\\Users\\salma\\AppData\\Local\\Programs\\Python\\Python313\\python.exe';  // Updated Python path

function quoteWin(string $s): string {
    return '"' . str_replace('"','""',$s) . '"';
}

// Test with a dummy file path
$dummyPath = __DIR__ . '/test.pdf';
file_put_contents($dummyPath, 'Test PDF content');

$script = PROJECT_ROOT . '\\parse_res.py';
$cmd = quoteWin($pythonExec)
      . ' ' . quoteWin($script)
      . ' ' . quoteWin($dummyPath)
      . ' 2>&1';

echo "<h1>Laravel Integration Test</h1>";
echo "<h2>Command being executed:</h2>";
echo "<pre>" . htmlspecialchars($cmd) . "</pre>";

$raw = shell_exec($cmd);
echo "<h2>Raw output:</h2>";
echo "<pre>" . htmlspecialchars($raw) . "</pre>";

$json = json_decode($raw, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    echo "<h2>JSON Error:</h2>";
    echo "<pre>" . json_last_error_msg() . "</pre>";
} else {
    echo "<h2>Parsed JSON:</h2>";
    echo "<pre>" . htmlspecialchars(json_encode($json, JSON_PRETTY_PRINT)) . "</pre>";
    
    // Process the data as Laravel would
    $d = $json;
    
    // Summary counts for Sample Results
    $sr = $d['sample_results'] ?? [];
    $reactCnt = $nonReactCnt = $equivCnt = 0;
    foreach ($sr as $r) {
        $res = strtoupper(trim($r['Result'] ?? ''));
        if ($res === 'REACTIVE') $reactCnt++;
        elseif ($res === 'NEG') $nonReactCnt++;
        elseif ($res === '???') $equivCnt++;
    }
    
    echo "<h2>Summary:</h2>";
    echo "<div style='margin-bottom: 20px;'>";
    echo "<span style='background-color: #dc3545; color: white; padding: 5px 10px; margin-right: 10px; border-radius: 4px;'>Reactive: $reactCnt</span>";
    echo "<span style='background-color: #198754; color: white; padding: 5px 10px; margin-right: 10px; border-radius: 4px;'>Non-Reactive: $nonReactCnt</span>";
    echo "<span style='background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 4px;'>Equivocal (???): $equivCnt</span>";
    echo "</div>";
    
    // Render the table as Laravel would
    echo "<h2>Sample Results:</h2>";
    render_table($sr);
}

// Clean up
unlink($dummyPath);

/**
 * Render a Bootstrap table with Sl.No prepended.
 */
function render_table(array $rows): void {
    if (empty($rows)) {
        echo '<p style="color: #6c757d;"><em>(no data)</em></p>';
        return;
    }
    echo '<div style="overflow-x: auto;">';
    echo '<table style="width: 100%; border-collapse: collapse; border: 1px solid #dee2e6;">';
    echo '<thead style="background-color: #f8f9fa;"><tr>';
    echo '<th style="border: 1px solid #dee2e6; padding: 8px;">Sl.No</th>';
    foreach (array_keys($rows[0]) as $col) {
        echo '<th style="border: 1px solid #dee2e6; padding: 8px;">' . htmlspecialchars($col) . '</th>';
    }
    echo '</tr></thead><tbody>';
    $i = 1;
    foreach ($rows as $r) {
        $isReactive = !empty($r['Result']) && stripos($r['Result'], 'reactive') !== false;
        echo $isReactive ? '<tr style="background-color: #f8d7da;">' : '<tr>';
        echo '<td style="border: 1px solid #dee2e6; padding: 8px;">' . $i++ . '</td>';
        foreach ($r as $c) {
            echo '<td style="border: 1px solid #dee2e6; padding: 8px;">' . htmlspecialchars($c) . '</td>';
        }
        echo '</tr>';
    }
    echo '</tbody></table></div>';
} 