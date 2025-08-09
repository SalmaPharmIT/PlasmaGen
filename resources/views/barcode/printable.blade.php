<!DOCTYPE html>
<html>
<head>
    <title>Print Labels</title>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        // Execute barcode generation and print immediately when loaded
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Printable view loaded, generating barcodes...');
            setTimeout(function() {
                generateBarcodes();
                setTimeout(function() {
                    window.print();
                }, 500);
            }, 200);
        });

        // Helper function to sanitize ID for CSS selector
        function sanitizeId(id) {
            return id.toString().replace(/[^a-zA-Z0-9]/g, '_');
        }

        function generateBarcodes() {
            try {
                // Generate Mega Pool barcodes
                generateBarcode("#megapool_0", "{{ $mega_pool }}");
                generateBarcode("#megapool_1", "{{ $mega_pool }}");

                // Generate Mini Pool barcodes
                @foreach($mini_pools as $minipool)
                generateBarcode("#minipool_{{ Str::slug($minipool, '_') }}", "{{ $minipool }}");
                @endforeach

                // Generate compact barcodes
                generateBarcode("#compact_mega", "{{ $mega_pool }}", true);
                @foreach($mini_pools as $minipool)
                generateBarcode("#compact_mini_{{ Str::slug($minipool, '_') }}", "{{ $minipool }}", true);
                @endforeach

                console.log('All barcodes generated successfully');
            } catch (error) {
                console.error('Error generating barcodes:', error);
                // Create fallback barcodes
                createFallbacks();
            }
        }

        function generateBarcode(selector, value, isCompact = false) {
            try {
                JsBarcode(selector, value, {
                    format: "code128",
                    width: isCompact ? 1 : 2,
                    height: isCompact ? 20 : 40,
                    displayValue: false,
                    margin: isCompact ? 2 : 5,
                    background: "#ffffff"
                });
            } catch (error) {
                console.error(`Error generating barcode for ${selector}:`, error);
                // Create fallback
                const element = document.querySelector(selector);
                if (element) {
                    element.outerHTML = `<div style="border:1px solid #000; text-align:center; padding:5px;">${value}</div>`;
                }
            }
        }

        function createFallbacks() {
            // Create text fallbacks for all barcodes if JsBarcode fails completely
            document.querySelectorAll('svg').forEach(svg => {
                const value = svg.id.includes('megapool') ? "{{ $mega_pool }}"
                    : svg.id.includes('compact_mini_') ? svg.id.replace('compact_mini_', '')
                    : svg.id.replace('minipool_', '');

                svg.outerHTML = `<div style="border:1px solid #000; text-align:center; padding:5px;">${value}</div>`;
            });
        }
    </script>
    <style>
        @page {
            size: 100mm 50mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
        }

        .label-container {
            width: 100mm;
            height: 50mm;
            page-break-after: always;
            page-break-inside: avoid;
            margin: 0;
            padding: 5mm;
            box-sizing: border-box;
            border: 1px solid #000;
            position: relative;
            overflow: hidden;
        }

        .label-header {
            display: flex;
            align-items: center;
            margin-bottom: 1mm;
            height: 8mm;
        }

        .logo {
            width: 25mm;
            height: 8mm;
        }

        .logo img {
            height: 100%;
            width: auto;
            object-fit: contain;
        }

        .header-text {
            flex-grow: 1;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
        }

        .label-content {
            width: 100%;
            border-collapse: collapse;
            height: calc(100% - 8mm);
            table-layout: fixed;
        }

        .label-content td {
            border: 1px solid #000;
            padding: 1mm;
            font-size: 9pt;
            vertical-align: middle;
        }

        .label-title {
            width: 25mm;
            background: #f8f9fa;
            font-weight: bold;
        }

        .barcode-container {
            text-align: center;
            height: 15mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .barcode-container svg {
            max-width: 90%;
            height: 12mm !important;
        }

        .barcode-text {
            margin-top: 1mm;
            font-size: 8pt;
        }

        .compact-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1mm;
            width: 100mm;
            height: 50mm;
            page-break-after: always;
            page-break-inside: avoid;
            margin: 0;
            padding: 2mm;
            box-sizing: border-box;
            border: 1px solid #000;
            background: white;
        }

        .compact-cell {
            border: 1px solid #000;
            padding: 1mm;
            text-align: center;
            height: 15mm;
            overflow: hidden;
        }

        .compact-title {
            font-size: 6pt;
            font-weight: bold;
            margin-bottom: 0.5mm;
        }

        .compact-barcode {
            height: 8mm;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .compact-barcode svg {
            max-width: 90%;
            height: 8mm !important;
        }

        .compact-text {
            font-size: 6pt;
            margin-top: 0.5mm;
        }

        .print-buttons {
            position: fixed;
            top: 10px;
            right: 10px;
            background: white;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 9999;
        }

        .print-buttons button {
            padding: 5px 10px;
            margin: 0 5px;
            cursor: pointer;
        }

        @media print {
            .print-buttons {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div class="print-buttons">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <!-- Mega Pool Labels (2 copies) -->
    @for($i = 0; $i < 2; $i++)
    <div class="label-container">
        <div class="label-header">
            <div class="logo">
                <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen">
            </div>
            <div class="header-text">Plasma Bag Mega Pool Label</div>
        </div>
        <table class="label-content">
            <tr>
                <td class="label-title">A.R. No.</td>
                <td>{{ $ar_number }}</td>
            </tr>
            <tr>
                <td class="label-title">Mega Pool No.</td>
                <td>
                    <div class="barcode-container">
                        <svg id="megapool_{{ $i }}"></svg>
                        <div class="barcode-text">{{ $mega_pool }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-title">Sign/Date</td>
                <td></td>
            </tr>
            <tr>
                <td class="label-title">Ref. Doc. No.</td>
                <td>{{ $ref_number }}</td>
            </tr>
        </table>
    </div>
    @endfor

    <!-- Mini Pool Labels -->
    @foreach($mini_pools as $minipool)
    <div class="label-container">
        <div class="label-header">
            <div class="logo">
                <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen">
            </div>
            <div class="header-text">Plasma Bag Mini Pool Label</div>
        </div>
        <table class="label-content">
            <tr>
                <td class="label-title">A.R. No.</td>
                <td>{{ $ar_number }}</td>
            </tr>
            <tr>
                <td class="label-title">Mini Pool No.</td>
                <td>
                    <div class="barcode-container">
                        <svg id="minipool_{{ Str::slug($minipool, '_') }}"></svg>
                        <div class="barcode-text">{{ $minipool }}</div>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="label-title">Sign/Date</td>
                <td></td>
            </tr>
            <tr>
                <td class="label-title">Ref. Doc. No.</td>
                <td>{{ $ref_number }}</td>
            </tr>
        </table>
    </div>
    @endforeach

    <!-- Compact Grid -->
    <div class="compact-grid">
        @foreach(array_slice($mini_pools, 0, 6) as $minipool)
        <div class="compact-cell">
            <div class="compact-title">MINI POOL No.</div>
            <div class="compact-barcode">
                <svg id="compact_mini_{{ Str::slug($minipool, '_') }}"></svg>
            </div>
            <div class="compact-text">{{ $minipool }}</div>
        </div>
        @endforeach

        <div class="compact-cell">
            <div class="compact-title">MEGA POOL No.</div>
            <div class="compact-barcode">
                <svg id="compact_mega"></svg>
            </div>
            <div class="compact-text">{{ $mega_pool }}</div>
        </div>

        @foreach(array_slice($mini_pools, 6, 2) as $minipool)
        <div class="compact-cell">
            <div class="compact-title">MINI POOL No.</div>
            <div class="compact-barcode">
                <svg id="compact_mini_{{ Str::slug($minipool, '_') }}"></svg>
            </div>
            <div class="compact-text">{{ $minipool }}</div>
        </div>
        @endforeach

        <div class="compact-cell"></div>
    </div>
</body>
</html>