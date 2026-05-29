<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt #<?= htmlspecialchars($payment['reference_no']) ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 20mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #2d3748;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
            font-size: 14px;
            line-height: 1.5;
        }
        .receipt-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            border: 1px solid #e2e8f0;
            padding: 30px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #cbd5e0;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0 0 5px 0;
            font-size: 22px;
            color: #1a0dab;
            letter-spacing: 0.5px;
        }
        .header h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #718096;
            font-weight: 600;
        }
        .header p {
            margin: 0;
            font-size: 12px;
            color: #718096;
        }
        .reference-box {
            text-align: center;
            margin-bottom: 30px;
        }
        .reference-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #a0aec0;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
            display: block;
        }
        .reference-val {
            font-size: 18px;
            font-weight: bold;
            color: #2d3748;
            font-family: monospace;
            margin: 0;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            padding: 10px 0;
            border-bottom: 1px solid #edf2f7;
        }
        .details-table td.label {
            color: #718096;
            width: 40%;
        }
        .details-table td.value {
            font-weight: bold;
            color: #2d3748;
            text-align: right;
            width: 60%;
        }
        .amount-box {
            background-color: #f7fafc;
            border: 1px solid #edf2f7;
            padding: 20px;
            text-align: center;
            border-radius: 6px;
            margin-bottom: 30px;
        }
        .amount-label {
            font-size: 11px;
            text-transform: uppercase;
            color: #718096;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: block;
            font-weight: 600;
        }
        .amount-val {
            font-size: 28px;
            font-weight: bold;
            color: #38a169;
        }
        .notes-box {
            background-color: #f8fafc;
            border-left: 4px solid #cbd5e0;
            padding: 12px 15px;
            margin-bottom: 30px;
            border-radius: 0 4px 4px 0;
        }
        .notes-title {
            font-size: 10px;
            font-weight: bold;
            color: #718096;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        .notes-content {
            font-size: 12px;
            color: #4a5568;
            margin: 0;
        }
        .footer {
            text-align: center;
            border-top: 2px dashed #cbd5e0;
            padding-top: 20px;
            color: #718096;
            font-size: 11px;
        }
        .footer p {
            margin: 0 0 5px 0;
            font-weight: 600;
        }
        .footer span {
            color: #a0aec0;
        }
    </style>
</head>
<body>

    <div class="receipt-container">
        
        <!-- Header -->
        <div class="header">
            <h2>GENERAL SANTOS CITY</h2>
            <h3>PUBLIC MARKET ADMINISTRATION</h3>
            <p>Arkalaba Stall Rental Receipt</p>
        </div>

        <!-- Reference Number -->
        <div class="reference-box">
            <span class="reference-label">Reference Number</span>
            <p class="reference-val"><?= htmlspecialchars($payment['reference_no']) ?></p>
        </div>

        <!-- Details -->
        <table class="details-table">
            <tr>
                <td class="label">Vendor Leaseholder:</td>
                <td class="value"><?= htmlspecialchars($payment['vendor_name']) ?></td>
            </tr>
            <tr>
                <td class="label">Assigned Stall:</td>
                <td class="value" style="font-family: monospace;"><?= htmlspecialchars($payment['stall_number']) ?></td>
            </tr>
            <tr>
                <td class="label">Stall Section:</td>
                <td class="value"><?= htmlspecialchars($payment['section']) ?></td>
            </tr>
            <tr>
                <td class="label">Collection Type:</td>
                <td class="value" style="text-transform: uppercase;"><?= htmlspecialchars($payment['payment_type']) ?> Rent</td>
            </tr>
            <tr>
                <td class="label">Period Covered:</td>
                <td class="value"><?= date('F d, Y', strtotime($payment['period_start'])) ?> to <?= date('F d, Y', strtotime($payment['period_end'])) ?></td>
            </tr>
            <tr>
                <td class="label">Collected By:</td>
                <td class="value"><?= htmlspecialchars($payment['collector_name'] ?: 'System') ?></td>
            </tr>
            <tr>
                <td class="label">Date &amp; Time Logged:</td>
                <td class="value"><?= date('M d, Y H:i:s', strtotime($payment['created_at'])) ?></td>
            </tr>
        </table>

        <!-- Amount Box -->
        <div class="amount-box">
            <span class="amount-label">Total Amount Paid</span>
            <span class="amount-val">PHP <?= number_format((float) $payment['amount'], 2) ?></span>
        </div>

        <!-- Notes -->
        <?php if (!empty($payment['notes'])): ?>
            <div class="notes-box">
                <div class="notes-title">Remarks</div>
                <p class="notes-content"><?= htmlspecialchars($payment['notes']) ?></p>
            </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for your prompt daily payment!</p>
            <span>This serves as an official electronic e-ticket log for your public market lease.</span>
        </div>

    </div>

</body>
</html>
