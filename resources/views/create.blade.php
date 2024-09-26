<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Create Invoice</h2>

        <form id="invoiceForm" method="POST" action="dd" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="date">Date</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>

            <div class="form-group">
                <label for="file_upload">File Upload</label>
                <input type="file" class="form-control" id="file_upload" name="file_upload" accept="image/*,.pdf">
            </div>

            <table class="table table-bordered" id="invoiceTable">
                <thead>
                    <tr>
                        <th>Qty</th>
                        <th>Amount</th>
                        <th>Total</th>
                        <th>Tax</th>
                        <th>Net</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><input type="number" class="form-control" name="rows[0][qty]" value="1" required></td>
                        <td><input type="number" class="form-control" name="rows[0][amount]" value="100" required></td>
                        <td><input type="text" class="form-control total" name="rows[0][total]" readonly></td>
                        <td><input type="number" class="form-control" name="rows[0][taxAmount]" value="3" required></td>
                        <td><input type="text" class="form-control net" name="rows[0][net]" readonly></td>
                        <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                    </tr>
                </tbody>
            </table>

            <button type="button" class="btn btn-primary" id="addRow">Add Row</button>
            <button type="submit" class="btn btn-success">Submit</button>
        </form>

        <div id="error-message" class="alert alert-danger mt-3" style="display: none;">Error submitting invoice.</div>
    </div>

    <!-- Add JavaScript to handle dynamic row addition, removal, and amount calculation -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let rowCount = 1;

            const addRowButton = document.getElementById('addRow');
            const invoiceTableBody = document.querySelector('#invoiceTable tbody');

            function calculateAmounts(row) {
                const qtyInput = row.querySelector('input[name$="[qty]"]');
                const amountInput = row.querySelector('input[name$="[amount]"]');
                const taxInput = row.querySelector('input[name$="[taxAmount]"]');
                const totalInput = row.querySelector('.total');
                const netInput = row.querySelector('.net');

                const qty = parseFloat(qtyInput.value) || 0;
                const amount = parseFloat(amountInput.value) || 0;
                const tax = parseFloat(taxInput.value) || 0;

                const total = qty * amount;
                const net = total + tax;

                totalInput.value = total.toFixed(2);
                netInput.value = net.toFixed(2);
            }

            // Event delegation to handle row calculations
            invoiceTableBody.addEventListener('input', function (e) {
                const row = e.target.closest('tr');
                calculateAmounts(row);
            });

            // Add new row
            addRowButton.addEventListener('click', function () {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="number" class="form-control" name="rows[${rowCount}][qty]" value="1" required></td>
                    <td><input type="number" class="form-control" name="rows[${rowCount}][amount]" value="100" required></td>
                    <td><input type="text" class="form-control total" name="rows[${rowCount}][total]" readonly></td>
                    <td><input type="number" class="form-control" name="rows[${rowCount}][taxAmount]" value="3" required></td>
                    <td><input type="text" class="form-control net" name="rows[${rowCount}][net]" readonly></td>
                    <td><button type="button" class="btn btn-danger remove-row">Remove</button></td>
                `;
                invoiceTableBody.appendChild(newRow);
                rowCount++;
            });

            // Remove row
            invoiceTableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row')) {
                    const row = e.target.closest('tr');
                    if (invoiceTableBody.children.length > 1) {
                        row.remove();
                    } else {
                        alert('You need at least one row.');
                    }
                }
            });

            // Initial calculation for the first row
            const firstRow = document.querySelector('#invoiceTable tbody tr');
            calculateAmounts(firstRow);
        });
    </script>
</body>
</html>
