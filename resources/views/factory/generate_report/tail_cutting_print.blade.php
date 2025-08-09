<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tail Cutting Report</title>
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
            margin-bottom: 20px;
        }
        .header img {
            max-width: 180px;
            height: auto;
        }
        .header h2 {
            margin: 10px 0;
            font-size: 18px;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 150px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: center;
            font-size: 11px;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 200px;
            border-top: 1px solid #000;
            padding-top: 5px;
            text-align: center;
        }
        .error-message {
            color: red;
            text-align: center;
            margin: 50px 0;
            font-size: 14px;
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
            <h2>Tail Cutting Report</h2>
        </div>

        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Blood Centre:</span>
                <span>{{ $bloodBank }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Date:</span>
                <span>{{ $date }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total Records:</span>
                <span>{{ $totalRecords }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Mega Pool No.</th>
                    <th>Mini Pool No.</th>
                    <th>Donor ID</th>
                    <th>Donation Date</th>
                    <th>Blood Group</th>
                    <th>Bag Volume (ML)</th>
                    <th>Tail Cutting</th>
                    <th>Prepared By</th>
                </tr>
            </thead>
            <tbody>
                @forelse($details as $index => $detail)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $detail['mega_pool_no'] }}</td>
                        <td>{{ $detail['mini_pool_number'] }}</td>
                        <td>{{ $detail['donor_id'] }}</td>
                        <td>{{ $detail['donation_date'] }}</td>
                        <td>{{ $detail['blood_group'] }}</td>
                        <td>{{ $detail['bag_volume_ml'] }}</td>
                        <td>{{ $detail['tail_cutting'] }}</td>
                        <td>{{ $detail['prepared_by'] }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center;">No data found</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="footer">
            <div class="signature-box">
                Entered By/ Done By<br>
                (WH/PPT) (Sign/ Date)
            </div>
            <div class="signature-box">
                Reviewed By (PPT/WH)<br>
                (Sign/ Date)
            </div>
            <div class="signature-box">
                Verified By (QA)<br>
                (Sign/ Date)
            </div>
        </div>
    @endif
</body>
</html>
