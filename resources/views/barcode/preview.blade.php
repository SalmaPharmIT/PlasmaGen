<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Preview</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #ddd;
        }
        .label-section {
            margin-bottom: 30px;
        }
        .label-title {
            background-color: #0c4c90;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        .mini-pool-title {
            background-color: #f35c24;
            color: white;
            padding: 10px;
            margin-bottom: 15px;
        }
        .label-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }
        .label {
            border: 1px solid #ddd;
            padding: 15px;
            width: calc(50% - 40px);
            box-sizing: border-box;
        }
        .compact-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .compact-label {
            border: 1px solid #ddd;
            padding: 10px;
            width: calc(33% - 20px);
            box-sizing: border-box;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            padding: 8px;
            border: 1px solid #ddd;
        }
        .label-header {
            background-color: #f8f9fa;
            width: 35%;
        }
        .barcode-container {
            text-align: center;
            padding: 10px 0;
        }
        .buttons {
            text-align: center;
            margin-top: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #0c4c90;
            color: white;
            border: none;
            cursor: pointer;
            margin: 0 10px;
        }
        .btn:hover {
            background-color: #0a3d7a;
        }

        /* Tab styles */
        .tabs {
            display: flex;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
        }
        .tab {
            padding: 10px 20px;
            cursor: pointer;
            border: 1px solid transparent;
            border-bottom: none;
            margin-right: 5px;
        }
        .tab.active {
            background-color: #fff;
            border-color: #ddd;
            border-bottom-color: #fff;
            margin-bottom: -1px;
            font-weight: bold;
        }
        .tab-content {
            display: none;
        }
        .tab-content.active {
            display: block;
        }

        /* PGL Visualization styles */
        .pgl-visualization {
            position: relative;
            width: 100%;
            overflow: auto;
            border: 1px solid #ddd;
            padding: 20px;
            background-color: #fff;
            margin-bottom: 20px;
        }
        .pgl-label {
            position: relative;
            width: 4in;
            height: 2in;
            border: 1px solid #000;
            margin-bottom: 20px;
            background-color: #fff;
            page-break-inside: avoid;
        }
        .pgl-element {
            position: absolute;
        }
        .pgl-text {
            font-family: monospace;
        }
        .pgl-barcode {
            background-color: #f8f8f8;
            border: 1px dashed #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Raw PGL Commands styles */
        .raw-pgl {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: 15px;
            font-family: monospace;
            white-space: pre;
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }

        @media print {
            .buttons, .header button, .tabs, .raw-pgl {
                display: none;
            }
            body, .preview-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
            .tab-content {
                display: block !important;
            }
            .tab-content:not(#tab-actual) {
                display: none !important;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
    <div class="preview-container">
        <div class="header">
            <h1>Label Preview</h1>
            <p>This is a preview of how your labels will look when printed</p>
            <div class="alert alert-info" style="background-color: #d1ecf1; color: #0c5460; padding: 15px; border: 1px solid #bee5eb; border-radius: 4px; margin-bottom: 15px;">
                <strong>Important:</strong> This is only a visualization. To print the actual labels on the Printronix printer, please close this preview and click the "Print Barcodes" button on the main screen.
            </div>
            <button class="btn" onclick="window.print()" style="background-color: #6c757d;">Print This Preview (for reference only)</button>
            <button class="btn" onclick="window.close()">Close Preview</button>
        </div>

        <!-- Tabs -->
        <div class="tabs">
            <div class="tab active" data-tab="browser">Browser Visualization</div>
            <div class="tab" data-tab="actual">Actual Printer Output</div>
            <div class="tab" data-tab="raw">Raw PGL Commands</div>
        </div>

        <!-- Browser Visualization Tab -->
        <div id="tab-browser" class="tab-content active">
            <!-- Mega Pool Labels -->
            <div class="label-section">
                <h2 class="label-title">Plasma Bag Mega Pool Label</h2>
                <div class="label-grid">
                    <!-- First Mega Pool Label -->
                    <div class="label">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 30%; text-align: left;">
                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                        </div>
                                        <div style="flex-grow: 1; text-align: center;">Plasma Bag Mega Pool Label</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">A.R. No.</td>
                                <td>{{ $arNumber }}</td>
                            </tr>
                            <tr>
                                <td class="label-header">Mega Pool No.</td>
                                <td>
                                    <div class="barcode-container">
                                        <svg id="megapool1"></svg>
                                    </div>
                                    <div style="text-align: center;">{{ $megaPool }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">Sign/ Date</td>
                                <td style="height: 30px;"></td>
                            </tr>
                            <tr>
                                <td class="label-header">Ref. Doc. No.</td>
                                <td>{{ $refNumber }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Second Mega Pool Label (duplicate) -->
                    <div class="label">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 30%; text-align: left;">
                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                        </div>
                                        <div style="flex-grow: 1; text-align: center;">Plasma Bag Mega Pool Label</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">A.R. No.</td>
                                <td>{{ $arNumber }}</td>
                            </tr>
                            <tr>
                                <td class="label-header">Mega Pool No.</td>
                                <td>
                                    <div class="barcode-container">
                                        <svg id="megapool2"></svg>
                                    </div>
                                    <div style="text-align: center;">{{ $megaPool }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">Sign/ Date</td>
                                <td style="height: 30px;"></td>
                            </tr>
                            <tr>
                                <td class="label-header">Ref. Doc. No.</td>
                                <td>{{ $refNumber }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mini Pool Labels -->
            <div class="label-section">
                <h2 class="mini-pool-title">Plasma Bag Mini Pool Label</h2>
                <div class="label-grid">
                    @foreach($miniPools as $index => $miniPool)
                    <div class="label">
                        <table>
                            <tr>
                                <td colspan="2">
                                    <div style="display: flex; align-items: center;">
                                        <div style="width: 30%; text-align: left;">
                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                        </div>
                                        <div style="flex-grow: 1; text-align: center;">Plasma Bag Mini Pool Label</div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">A.R. No.</td>
                                <td>{{ $arNumber }}</td>
                            </tr>
                            <tr>
                                <td class="label-header">Mini Pool No.</td>
                                <td>
                                    <div class="barcode-container">
                                        <svg id="minipool_{{ $index }}"></svg>
                                    </div>
                                    <div style="text-align: center;">{{ $miniPool }}</div>
                                </td>
                            </tr>
                            <tr>
                                <td class="label-header">Sign/ Date</td>
                                <td style="height: 30px;"></td>
                            </tr>
                            <tr>
                                <td class="label-header">Ref. Doc. No.</td>
                                <td>{{ $refNumber }}</td>
                            </tr>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Compact Labels -->
            <div class="label-section">
                <h2 class="label-title">Compact Barcode Labels</h2>
                <div class="compact-grid">
                    <div class="compact-label">
                        <div>MEGA POOL No.</div>
                        <div class="barcode-container">
                            <svg id="megapool_compact"></svg>
                        </div>
                        <div>{{ $megaPool }}</div>
                    </div>

                    @foreach($miniPools as $index => $miniPool)
                    <div class="compact-label">
                        <div>MINI POOL No.</div>
                        <div class="barcode-container">
                            <svg id="minipool_compact_{{ $index }}"></svg>
                        </div>
                        <div>{{ $miniPool }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Actual Printer Output Tab -->
        <div id="tab-actual" class="tab-content">
            <h2>Actual Printer Output (PGL)</h2>
            <p>This visualization represents how the labels will appear when printed on the Printronix printer:</p>

            <div class="pgl-visualization">
                @if(isset($pglVisualization))
                    <!-- Mega Pool Labels -->
                    @if(!empty($pglVisualization['mega_pool_labels']))
                        <h3>Mega Pool Labels</h3>
                        <div class="pgl-label-container">
                            @foreach($pglVisualization['mega_pool_labels'] as $labelIndex => $label)
                                <div class="pgl-label" style="border: 2px solid #0c4c90; margin-bottom: 20px; padding: 10px; position: relative; height: 300px; page-break-inside: avoid;">
                                    <div style="background-color: #0c4c90; color: white; padding: 5px 10px; margin-bottom: 10px;">
                                        Plasma Bag Mega Pool Label
                                    </div>
                                    @foreach($label['elements'] as $element)
                                        @if($element['type'] == 'text')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; font-family: monospace; font-size: 12px;">
                                                {{ $element['text'] }}
                                            </div>
                                        @elseif($element['type'] == 'barcode')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; height: {{ $element['height'] / 11.811 }}mm; min-width: 100px; text-align: center;">
                                                <svg id="pgl_barcode_mega_{{ $labelIndex }}_{{ $loop->index }}" class="pgl-barcode"></svg>
                                                <div style="font-size: 10px; margin-top: 5px;">{{ $element['data'] }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Mini Pool Labels -->
                    @if(!empty($pglVisualization['mini_pool_labels']))
                        <h3>Mini Pool Labels</h3>
                        <div class="pgl-label-container">
                            @foreach($pglVisualization['mini_pool_labels'] as $labelIndex => $label)
                                <div class="pgl-label" style="border: 2px solid #f35c24; margin-bottom: 20px; padding: 10px; position: relative; height: 300px; page-break-inside: avoid;">
                                    <div style="background-color: #f35c24; color: white; padding: 5px 10px; margin-bottom: 10px;">
                                        Plasma Bag Mini Pool Label
                                    </div>
                                    @foreach($label['elements'] as $element)
                                        @if($element['type'] == 'text')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; font-family: monospace; font-size: 12px;">
                                                {{ $element['text'] }}
                                            </div>
                                        @elseif($element['type'] == 'barcode')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; height: {{ $element['height'] / 11.811 }}mm; min-width: 100px; text-align: center;">
                                                <svg id="pgl_barcode_mini_{{ $labelIndex }}_{{ $loop->index }}" class="pgl-barcode"></svg>
                                                <div style="font-size: 10px; margin-top: 5px;">{{ $element['data'] }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Compact Labels -->
                    @if(!empty($pglVisualization['compact_labels']))
                        <h3>Compact Barcode Labels</h3>
                        <div class="pgl-label-container" style="display: flex; flex-wrap: wrap; gap: 10px;">
                            @foreach($pglVisualization['compact_labels'] as $labelIndex => $label)
                                <div class="pgl-label" style="border: 1px solid #ddd; padding: 10px; position: relative; width: 150px; height: 150px; page-break-inside: avoid;">
                                    @foreach($label['elements'] as $element)
                                        @if($element['type'] == 'text')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; font-family: monospace; font-size: 10px;">
                                                {{ $element['text'] }}
                                            </div>
                                        @elseif($element['type'] == 'barcode')
                                            <div style="position: absolute; left: {{ $element['x'] / 11.811 }}mm; top: {{ $element['y'] / 11.811 }}mm; height: {{ $element['height'] / 11.811 }}mm; width: 80px; text-align: center;">
                                                <svg id="pgl_barcode_compact_{{ $labelIndex }}_{{ $loop->index }}" class="pgl-barcode"></svg>
                                                <div style="font-size: 8px; margin-top: 3px;">{{ $element['data'] }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <p>No PGL visualization data available.</p>
                @endif
            </div>
        </div>

        <!-- Raw PGL Commands Tab -->
        <div id="tab-raw" class="tab-content">
            <h2>Raw PGL Commands</h2>
            <p>These are the actual commands that will be sent to the Printronix printer:</p>

            <div class="raw-pgl">
                @if(isset($pglCommands))
                    {{ $pglCommands }}
                @else
                    No PGL commands available.
                @endif
            </div>
        </div>

        <div class="buttons">
            <button class="btn" onclick="window.print()" style="background-color: #6c757d;">Print This Preview (for reference only)</button>
            <button class="btn" onclick="window.close()">Close Preview & Return to Printing</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Remove active class from all tabs and content
                    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById('tab-' + this.dataset.tab).classList.add('active');
                });
            });

            // Generate barcodes for browser visualization
            generateBrowserBarcodes();

            // Generate barcodes for PGL visualization
            generatePGLBarcodes();
        });

        function generateBrowserBarcodes() {
            // Generate Mega Pool barcodes
            JsBarcode("#megapool1", "{{ $megaPool }}", {
                format: "code128",
                width: 1,
                height: 40,
                displayValue: false,
                margin: 2
            });

            JsBarcode("#megapool2", "{{ $megaPool }}", {
                format: "code128",
                width: 1,
                height: 40,
                displayValue: false,
                margin: 2
            });

            JsBarcode("#megapool_compact", "{{ $megaPool }}", {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2
            });

            // Generate Mini Pool barcodes
            @foreach($miniPools as $index => $miniPool)
                JsBarcode("#minipool_{{ $index }}", "{{ $miniPool }}", {
                    format: "code128",
                    width: 1,
                    height: 40,
                    displayValue: false,
                    margin: 2
                });

                JsBarcode("#minipool_compact_{{ $index }}", "{{ $miniPool }}", {
                    format: "code128",
                    width: 1,
                    height: 30,
                    displayValue: false,
                    margin: 2
                });
            @endforeach
        }

        function generatePGLBarcodes() {
            @if(isset($pglVisualization))
                // Generate barcodes for Mega Pool labels
                @if(!empty($pglVisualization['mega_pool_labels']))
                    @foreach($pglVisualization['mega_pool_labels'] as $labelIndex => $label)
                        @foreach($label['elements'] as $elementIndex => $element)
                            @if($element['type'] == 'barcode')
                                JsBarcode("#pgl_barcode_mega_{{ $labelIndex }}_{{ $elementIndex }}", "{{ $element['data'] }}", {
                                    format: "code128",
                                    width: 1,
                                    height: 30,
                                    displayValue: false,
                                    margin: 2
                                });
                            @endif
                        @endforeach
                    @endforeach
                @endif

                // Generate barcodes for Mini Pool labels
                @if(!empty($pglVisualization['mini_pool_labels']))
                    @foreach($pglVisualization['mini_pool_labels'] as $labelIndex => $label)
                        @foreach($label['elements'] as $elementIndex => $element)
                            @if($element['type'] == 'barcode')
                                JsBarcode("#pgl_barcode_mini_{{ $labelIndex }}_{{ $elementIndex }}", "{{ $element['data'] }}", {
                                    format: "code128",
                                    width: 1,
                                    height: 30,
                                    displayValue: false,
                                    margin: 2
                                });
                            @endif
                        @endforeach
                    @endforeach
                @endif

                // Generate barcodes for Compact labels
                @if(!empty($pglVisualization['compact_labels']))
                    @foreach($pglVisualization['compact_labels'] as $labelIndex => $label)
                        @foreach($label['elements'] as $elementIndex => $element)
                            @if($element['type'] == 'barcode')
                                JsBarcode("#pgl_barcode_compact_{{ $labelIndex }}_{{ $elementIndex }}", "{{ $element['data'] }}", {
                                    format: "code128",
                                    width: 1,
                                    height: 20,
                                    displayValue: false,
                                    margin: 1
                                });
                            @endif
                        @endforeach
                    @endforeach
                @endif
            @endif
        }
    </script>
</body>
</html>
