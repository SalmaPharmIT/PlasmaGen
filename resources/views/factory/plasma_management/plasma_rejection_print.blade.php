<!DOCTYPE html>
<html>
<head>
    <title>Plasma Rejection Sheet - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h4 {
            margin: 0;
            padding: 0;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .signature-section {
            margin-top: 50px;
        }
        .signature-box {
            width: 45%;
            float: left;
            margin-right: 5%;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            margin-top: 40px;
            margin-bottom: 5px;
        }
        .clear {
            clear: both;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h4>Plasma Rejection List</h4>
    </div>

    <div class="info-section">
        <p><strong>Blood Centre:</strong> {{ $bloodCentre ?? '-' }}</p>
        <p><strong>Date:</strong> {{ $date ?? '-' }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>A.R. No.</th>
                <th>Mega Pool No./ Mini Pool No./ Donor ID</th>
                <th>Donation Date</th>
                <th>Blood Group</th>
                <th>Volume</th>
                <th>Rejection Reason</th>
                <th>Rejected By</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($items) && count($items) > 0)
                @foreach($items as $item)
                <tr>
                    <td>{{ $item['ar_no'] ?? '-' }}</td>
                    <td>{{ $item['pool_no'] ?? '-' }}</td>
                    <td>{{ $item['donation_date'] ?? '-' }}</td>
                    <td>{{ $item['blood_group'] ?? '-' }}</td>
                    <td>{{ $item['volume'] ?? '-' }}</td>
                    <td>{{ $item['rejection_reason'] ?? '-' }}</td>
                    <td>{{ $item['rejected_by'] ?? '-' }}</td>
                </tr>
                @endforeach
            @else
                @for($i = 0; $i < 8; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <div class="signature-section">
        <div class="signature-box">
            <div>Done By:</div>
            <div class="signature-line"></div>
            <div>Signature:</div>
            <div class="signature-line"></div>
        </div>
        <div class="signature-box">
            <div>Reviewed By:</div>
            <div class="signature-line"></div>
            <div>Signature:</div>
            <div class="signature-line"></div>
        </div>
        <div class="clear"></div>
    </div>

    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()">Print</button>
    </div>
</body>
</html> 