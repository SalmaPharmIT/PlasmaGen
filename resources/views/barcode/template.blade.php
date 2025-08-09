<!DOCTYPE html>
<html>
<head>
    <title>Barcode Template</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body { 
                margin: 0; 
                padding: 0; 
            }
            .label-container {
                page-break-inside: avoid;
                margin: 0 0 5mm 0;
                padding: 0;
            }
            .card-header {
                background-color: #0c4c90 !important;
                color: white !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .card-header h6 {
                color: white !important;
                font-size: 14pt !important;
                margin: 0 !important;
                padding: 5mm 0 !important;
                text-align: center !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .flex-grow-1.text-center {
                color: black !important;
                font-size: 10pt !important;
                font-weight: bold !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .compact-grid {
                width: 100mm !important;
                margin: 10mm 0 !important;
                border-collapse: collapse !important;
                page-break-inside: avoid !important;
            }
            .compact-grid td {
                width: 33mm !important;
                height: 15mm !important;
                border: 1px solid #000 !important;
                padding: 1mm !important;
                text-align: center !important;
                vertical-align: middle !important;
                background-color: white !important;
                page-break-inside: avoid !important;
            }
            .compact-grid .label-text {
                font-size: 7pt !important;
                font-weight: bold !important;
                margin-bottom: 1mm !important;
            }
            .compact-grid .barcode-container {
                height: 8mm !important;
                margin: 1mm 0 !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            .compact-grid .barcode-text {
                font-size: 7pt !important;
                margin-top: 1mm !important;
            }
            @page {
                margin: 0;
                size: auto;
            }
        }
        body {
            margin: 0;
            padding: 0;
        }
        .barcode-container {
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: #fff;
        }
        .table {
            margin-bottom: 0;
        }
        .table td {
            padding: 8px 15px;
            vertical-align: middle;
            font-size: 14px;
        }
        .table tr:first-child td {
            border-top: none;
        }
        .shadow-sm {
            box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
        }
        .card {
            border: 1px solid #000 !important;
            box-shadow: none !important;
            margin: 0 0 10px 0 !important;
            padding: 0 !important;
            page-break-inside: avoid;
            background: #fff !important;
        }
        .card-body {
            padding: 5px !important;
            background: #fff !important;
        }
        .table {
            border: 1px solid #000 !important;
            margin: 0 !important;
        }
        .table td {
            border: 1px solid #000 !important;
            padding: 3px 5px !important;
            background: #fff !important;
            font-size: 11px !important;
        }
        .table-bordered {
            border: 1px solid #000 !important;
        }
        .shadow-sm {
            box-shadow: none !important;
        }
        .row.g-4 {
            gap: 10px !important;
            margin: 0 !important;
            padding: 0 !important;
        }
        .col-md-6, .col-md-2, .col-sm-4 {
            padding: 5px !important;
        }
        .my-2 {
            margin: 5px 0 !important;
        }
        .px-4 {
            padding: 0 5px !important;
        }
        .mb-3, .mb-4 {
            margin-bottom: 10px !important;
        }
        .py-2 {
            padding: 3px 0 !important;
        }
        .py-3 {
            padding: 5px 0 !important;
        }
        .px-3 {
            padding: 0 5px !important;
        }
        svg {
            max-width: 100% !important;
            height: auto !important;
        }
        td {
            color: #000 !important;
        }
        img {
            max-height: 25px !important;
            width: auto !important;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Mega Pool Labels -->
            <div class="col-12 mb-4">
                <div class="card-header text-white" style="background-color: #0c4c90; padding: 15px;">
                    <h6 class="mb-0">Plasma Bag Mega Pool Label</h6>
                </div>
                <div class="row g-4 my-2 px-4">
                    <!-- First Mega Pool Label -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td colspan="2" class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30%;" class="text-start">
                                                    <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                </div>
                                                <div class="flex-grow-1 text-center">Plasma Bag Mega Pool Label</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                        <td class="px-3">{{ $ar_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Mega Pool No.</td>
                                        <td>
                                            <div class="text-center py-2">
                                                <svg id="megapool1"></svg>
                                            </div>
                                            <div class="text-center">
                                                <span>{{ $mega_pool }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                        <td class="py-3"></td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                        <td class="px-3">{{ $ref_number }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Second Mega Pool Label -->
                    <div class="col-md-6">
                        <div class="card shadow-sm">
                            <div class="card-body p-0">
                                <table class="table table-bordered mb-0">
                                    <tr>
                                        <td colspan="2" class="py-2">
                                            <div class="d-flex align-items-center">
                                                <div style="width: 30%;" class="text-start">
                                                    <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                </div>
                                                <div class="flex-grow-1 text-center">Plasma Bag Mega Pool Label</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                        <td class="px-3">{{ $ar_number }}</td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Mega Pool No.</td>
                                        <td>
                                            <div class="text-center py-2">
                                                <svg id="megapool2"></svg>
                                            </div>
                                            <div class="text-center">
                                                <span>{{ $mega_pool }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                        <td class="py-3"></td>
                                    </tr>
                                    <tr>
                                        <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                        <td class="px-3">{{ $ref_number }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mini Pool Labels -->
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header text-white" style="background-color: #f35c24; padding: 15px;">
                        <h6 class="mb-0">Plasma Bag Mini Pool Label</h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="row g-4 my-2 px-4">
                            @foreach($mini_pools as $minipool)
                            <div class="col-md-6">
                                <div class="card shadow-sm">
                                    <div class="card-body p-0">
                                        <table class="table table-bordered mb-0">
                                            <tr>
                                                <td colspan="2" class="py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div style="width: 30%;" class="text-start">
                                                            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Plasmagen" style="height: 30px; object-fit: contain;">
                                                        </div>
                                                        <div class="flex-grow-1 text-center">Plasma Bag Mini Pool Label</div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 35%; background-color: #f8f9fa;">A.R. No.</td>
                                                <td class="px-3">{{ $ar_number }}</td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #f8f9fa;">Mini Pool No.</td>
                                                <td>
                                                    <div class="text-center py-2">
                                                        <svg id="minipool_detailed_{{ $minipool }}"></svg>
                                                    </div>
                                                    <div class="text-center">
                                                        <span>{{ $minipool }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #f8f9fa;">Sign/ Date</td>
                                                <td class="py-3"></td>
                                            </tr>
                                            <tr>
                                                <td style="background-color: #f8f9fa;">Ref. Doc. No.</td>
                                                <td class="px-3">{{ $ref_number }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Compact Barcodes -->
            <div class="col-12 mt-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <table class="compact-grid">
                            <!-- First Row -->
                            <tr>
                                @foreach(array_slice($mini_pools, 0, 3) as $minipool)
                                <td>
                                    <div class="label-text">MINI POOL No.</div>
                                    <div class="barcode-container">
                                        <svg id="minipool_compact_1_{{ $minipool }}"></svg>
                                    </div>
                                    <div class="barcode-text">{{ $minipool }}</div>
                                </td>
                                @endforeach
                            </tr>
                            <!-- Second Row -->
                            <tr>
                                @foreach(array_slice($mini_pools, 3, 3) as $minipool)
                                <td>
                                    <div class="label-text">MINI POOL No.</div>
                                    <div class="barcode-container">
                                        <svg id="minipool_compact_2_{{ $minipool }}"></svg>
                                    </div>
                                    <div class="barcode-text">{{ $minipool }}</div>
                                </td>
                                @endforeach
                            </tr>
                            <!-- Third Row -->
                            <tr>
                                <td>
                                    <div class="label-text">MEGA POOL No.</div>
                                    <div class="barcode-container">
                                        <svg id="megapool_compact"></svg>
                                    </div>
                                    <div class="barcode-text">{{ $mega_pool }}</div>
                                </td>
                                @foreach(array_slice($mini_pools, 6, 2) as $minipool)
                                <td>
                                    <div class="label-text">MINI POOL No.</div>
                                    <div class="barcode-container">
                                        <svg id="minipool_compact_3_{{ $minipool }}"></svg>
                                    </div>
                                    <div class="barcode-text">{{ $minipool }}</div>
                                </td>
                                @endforeach
                                <td></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Generate Mega Pool barcodes
            JsBarcode("#megapool1", "{{ $mega_pool }}", {
                format: "code128",
                width: 1,
                height: 40,
                displayValue: true,
                fontSize: 10,
                margin: 2,
                textMargin: 2
            });

            JsBarcode("#megapool2", "{{ $mega_pool }}", {
                format: "code128",
                width: 1,
                height: 40,
                displayValue: true,
                fontSize: 10,
                margin: 2,
                textMargin: 2
            });

            // Generate Mini Pool barcodes
            @foreach($mini_pools as $minipool)
            JsBarcode("#minipool_detailed_{{ $minipool }}", "{{ $minipool }}", {
                format: "code128",
                width: 2,
                height: 50,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
            @endforeach

            // Generate compact barcodes
            // First row
            @foreach(array_slice($mini_pools, 0, 3) as $minipool)
            JsBarcode("#minipool_compact_1_{{ $minipool }}", "{{ $minipool }}", {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
            @endforeach

            // Second row
            @foreach(array_slice($mini_pools, 3, 3) as $minipool)
            JsBarcode("#minipool_compact_2_{{ $minipool }}", "{{ $minipool }}", {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
            @endforeach

            // Third row
            JsBarcode("#megapool_compact", "{{ $mega_pool }}", {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });

            @foreach(array_slice($mini_pools, 6, 2) as $minipool)
            JsBarcode("#minipool_compact_3_{{ $minipool }}", "{{ $minipool }}", {
                format: "code128",
                width: 1,
                height: 30,
                displayValue: false,
                margin: 2,
                textMargin: 0
            });
            @endforeach

            // Print automatically when page loads
            window.print();
        });
    </script>
</body>
</html> 