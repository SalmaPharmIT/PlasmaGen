<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mega Pool Mini Pool Report - {{ $header['mega_pool'] ?? 'N/A' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
        }
        .header img {
            max-width: 150px;
            height: auto;
        }
        .header h2 {
            margin: 10px 0;
            font-size: 16px;
            font-weight: bold;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .info-item {
            border: 1px solid #000;
            padding: 5px;
        }
        .info-label {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
        }
        .signature-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }
        .signature-box {
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
            font-size: 10px;
        }
        .error-message {
            color: red;
            text-align: center;
            margin: 50px 0;
            font-size: 14px;
        }
        /* Group of 12 styling */
        tbody tr:nth-child(12n) {
            border-bottom: 2px solid #000;
        }
        tbody tr:nth-child(12n+1) {
            border-top: 2px solid #000;
        }
    </style>
</head>
<body>
    @if(isset($error))
        <div class="error-message">
            {{ $error }}
        </div>
    @else
        <div class="header">
            <img src="{{ asset('assets/img/pgblogo.png') }}" alt="Company Logo">
            <h2>Plasma Mini Pool and Mega Pool Handling Record</h2>
        </div>

        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Blood Centre Name & City:</span>
                <span>{{ $header['blood_centre'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Date:</span>
                <span>{{ $header['date'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Pickup Date:</span>
                <span>{{ $header['pickup_date'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">A.R. No.:</span>
                <span>{{ $header['ar_no'] ?? 'N/A' }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">GRN No.:</span>
                <span>{{ $header['grn_no'] ?? 'N/A' }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No. of<br>Bags</th>
                    <th>No. of Bags in<br>Mini Pool</th>
                    <th>Donor<br>ID</th>
                    <th>Donation<br>Date</th>
                    <th>Blood<br>Group</th>
                    <th>Bag Volume<br>in ML</th>
                    <th>Mini Pool Bag<br>Volume in Liter</th>
                    <th>Mega Pool<br>Number</th>
                    <th>Mini Pool Number /<br>Segment No.</th>
                    <th>Tail<br>Cutting<br>Done</th>
                    <th>Mini Pool Sample<br>Prepared By<br>(Sign/Date)</th>
                    <th>Test Results<br>Mini Pool</th>
                    <th>Test Results<br>Mega Pool</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $currentMiniPool = null;
                    $currentMegaPool = null;
                    $miniPoolCounts = [];
                    $megaPoolCounts = [];

                    // Count rows per mini pool and mega pool
                    foreach ($details as $detail) {
                        $miniPoolCounts[$detail['mini_pool_number']] = isset($miniPoolCounts[$detail['mini_pool_number']])
                            ? $miniPoolCounts[$detail['mini_pool_number']] + 1
                            : 1;
                        $megaPoolCounts[$detail['mega_pool_no']] = isset($megaPoolCounts[$detail['mega_pool_no']])
                            ? $megaPoolCounts[$detail['mega_pool_no']] + 1
                            : 1;
                    }
                @endphp

                @forelse($details as $index => $detail)
                    <tr>
                        <td>{{ $detail['row_number'] }}</td>
                        <td>{{ $detail['bags_in_mini_pool'] }}</td>
                        <td>{{ $detail['donor_id'] }}</td>
                        <td>{{ $detail['donation_date'] }}</td>
                        <td>{{ $detail['blood_group'] }}</td>
                        <td>{{ $detail['bag_volume_ml'] }}</td>

                        @if($currentMiniPool !== $detail['mini_pool_number'])
                            @php $currentMiniPool = $detail['mini_pool_number']; @endphp
                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['mini_pool_bag_volume'] }}</td>

                            @if($currentMegaPool !== $detail['mega_pool_no'])
                                @php $currentMegaPool = $detail['mega_pool_no']; @endphp
                                <td rowspan="{{ $megaPoolCounts[$detail['mega_pool_no']] }}">{{ $detail['mega_pool_no'] }}</td>
                            @endif

                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['mini_pool_number'] }}</td>
                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['tail_cutting'] }}</td>
                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['prepared_by'] }}</td>
                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['mini_pool_test_result'] }}</td>
                            <td rowspan="{{ $miniPoolCounts[$detail['mini_pool_number']] }}">{{ $detail['mega_pool_test_result'] }}</td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="12" style="text-align: center;">No data found</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right;">
                        <strong>Total Volume in Liters:</strong>
                    </td>
                    <td>{{ $total_volume ?? '0' }}</td>
                    <td colspan="5"></td>
                </tr>
            </tfoot>
        </table>

        <div class="footer">
            <div class="signature-row">
                <div class="signature-box">
                    Entered By/ Done By<br>
                    (WH/PPT) (Sign/ Date)
                </div>
                <div class="signature-box">
                    Reviewed By (PPT/WH)<br>
                    (Sign/ Date)
                </div>
                <div class="signature-box">
                    Approved By (QA)<br>
                    (Sign/ Date)
                </div>
            </div>
        </div>
    @endif

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
