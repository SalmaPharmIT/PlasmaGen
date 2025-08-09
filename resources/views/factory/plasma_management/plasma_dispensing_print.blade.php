<!DOCTYPE html>
<html>
<head>
    <title>Plasma Dispensing Sheet - Print</title>
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
        <h4>Plasma Despense List</h4>
    </div>

    <div class="info-section">
        <p><strong>A.R. Number:</strong> <span id="arNumber">-</span></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Mega Pool No./ Mini Pool No./ Donor ID</th>
                <th>Requested Volume</th>
                <th>Issued Volume</th>
                <th>Dispensed By</th>
            </tr>
        </thead>
        <tbody id="plasmaTableBody">
            <!-- Data will be populated here -->
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

    <script>
        // Listen for data from parent window
        window.addEventListener('message', function(event) {
            if (event.origin !== window.location.origin) return;

            const data = event.data;

            // Update header information
            document.getElementById('arNumber').textContent = data.arNo || '-';

            // Clear existing table rows
            const tbody = document.getElementById('plasmaTableBody');
            tbody.innerHTML = '';

            // Add data rows
            data.items.forEach(function(item) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.pool_no || '-'}</td>
                    <td>${item.requested_volume || '-'}</td>
                    <td>${item.issued_volume || '-'}</td>
                    <td>${item.dispensed_by || '-'}</td>
                `;
                tbody.appendChild(row);
            });

            // Auto print after data is loaded
            window.print();
        });
    </script>
</body>
</html>
