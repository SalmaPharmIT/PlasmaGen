<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destruction List - Print</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-header h2 {
            margin: 0;
            color: #0c4c90;
            font-size: 24px;
        }
        .print-header p {
            margin: 5px 0;
            color: #666;
            font-size: 14px;
        }
        .print-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        .print-table th {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
            font-weight: bold;
            color: #495057;
        }
        .print-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
        }
        .print-table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .print-footer {
            margin-top: 20px;
            text-align: right;
            font-size: 12px;
            color: #666;
        }
        .print-info {
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="print-header">
        <h2>Destruction List</h2>
        <p>Generated on: {{ now()->format('d-m-Y H:i:s') }}</p>
    </div>

    <div class="print-info">
        <p>Total Records: <span id="totalRecords">0</span></p>
    </div>

    <table class="print-table">
        <thead>
            <tr>
                <th>SL No.</th>
                <th>Pickup Date</th>
                <th>Date of Receipt</th>
                <th>GRN No.</th>
                <th>Blood Bank Name</th>
                <th>Plasma Qty(Ltr)</th>
                <th>Destruction No.</th>
                <th>Entered By</th>
                <th>Remarks</th>
            </tr>
        </thead>
        <tbody id="printTableBody">
            <!-- Data will be populated here -->
        </tbody>
    </table>

    <div class="print-footer">
        <p>Page 1 of 1</p>
    </div>

    <script>
        // Function to load and populate print data
        function loadPrintData() {
            fetch('{{ route("plasma.get-destruction-list") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    export: true
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const tbody = document.getElementById('printTableBody');
                    const totalRecords = document.getElementById('totalRecords');

                    totalRecords.textContent = data.total;

                    data.data.forEach((entry, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${entry.pickup_date || '-'}</td>
                            <td>${entry.receipt_date || '-'}</td>
                            <td>${entry.grn_no || '-'}</td>
                            <td>${entry.blood_bank_name || '-'}</td>
                            <td>${entry.plasma_qty || '-'}</td>
                            <td>${entry.destruction_no || '-'}</td>
                            <td>${entry.entered_by || '-'}</td>
                            <td>${entry.remarks || '-'}</td>
                        `;
                        tbody.appendChild(row);
                    });

                    // Trigger print after data is loaded
                    window.print();
                }
            })
            .catch(error => {
                console.error('Error loading print data:', error);
            });
        }

        // Load data when page loads
        window.onload = loadPrintData;
    </script>
</body>
</html>
