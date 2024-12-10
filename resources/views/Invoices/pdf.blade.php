<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            color: red;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table th {
            background-color: red;
            color: white;
        }
        .subtotal {
            text-align: right;
            font-weight: bold;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>INVOICE</h1>
        <p>Jalan Veteran No. 79, Rengat - Indragiri Hulu</p>
        <p>081275664808</p>
    </div>

    <table style="width: 100%; margin-top: 20px;">
        <tr>
            <td>
                <strong>Bill To:</strong><br>
                Name: {{ $invoice->inquiry->name }}<br>
                Company Name: {{ $invoice->inquiry->company }}<br>
                Address: {{ $invoice->inquiry->address }}<br>
                Phone: {{ $invoice->inquiry->phone }}<br>
                Work: {{ $invoice->inquiry->message }}
            </td>
            <td>
                <strong>Invoice #:</strong> {{ $invoice->id }}<br>
                <strong>Date:</strong> {{ $invoice->created_at->format('d/m/Y') }}<br>
                <strong>Customer ID:</strong> {{ $invoice->customer_id }}<br>
                <strong>Terms:</strong> Cash and Carry
            </td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->pivot->quantity }}</td>
                <td>{{ number_format($product->price, 2, ',', '.') }}</td>
                <td>{{ number_format($product->pivot->quantity * $product->price, 2, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="subtotal">Subtotal</td>
                <td>{{ number_format($invoice->products->sum(fn($p) => $p->pivot->quantity * $p->price), 2, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p><strong>Payment Method Transfer to Account:</strong></p>
        <p>
            <strong>Mandiri</strong><br>
            Acc: 1080021556122<br>
            A/N: DINDA LESTARI (Accounting Finance)
        </p>
    </div>
</body>
</html>
