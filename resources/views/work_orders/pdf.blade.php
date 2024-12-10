<!DOCTYPE html>
<html>
<head>
    <title>Work Order</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; font-weight: bold; }
        .info-table, .item-table { width: 100%; border-collapse: collapse; }
        .info-table td, .item-table td, .item-table th { border: 1px solid black; padding: 8px; }
        .item-table th { background-color: #f2f2f2; }
        .footer { margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SURAT PERINTAH KERJA</h2>
        <p>No: {{ $workOrder->id }}/SPK/{{ date('d/m/Y', strtotime($workOrder->created_at)) }}</p>
    </div>

    <p>Tanggal Pembayaran: {{ date('d/m/Y', strtotime($workOrder->paymentReceipt->payment_date)) }}</p>

    <h3>DATA PEMESAN:</h3>
    <table class="info-table">
        <tr>
            <td><strong>No Invoice:</strong></td>
            <td>{{ $workOrder->invoice_id }}</td>
        </tr>
        <tr>
            <td><strong>Nama:</strong></td>
            <td>{{ $workOrder->inquiry->name }}</td>
        </tr>
        <tr>
            <td><strong>Perusahaan:</strong></td>
            <td>{{ $workOrder->inquiry->company }}</td>
        </tr>
        <tr>
            <td><strong>Alamat:</strong></td>
            <td>{{ $workOrder->inquiry->address }}</td>
        </tr>
        <tr>
            <td><strong>Telp:</strong></td>
            <td>{{ $workOrder->inquiry->phone }}</td>
        </tr>
        <tr>
            <td><strong>Pekerjaan:</strong></td>
            <td>{{ $workOrder->inquiry->message }}</td>
        </tr>
    </table>

    <h3>Item:</h3>
    <table class="item-table">
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantitas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($workOrder->inquiry->products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->pivot->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p>Berdasarkan terbitnya surat ini, maka menginstruksikan koordinator lapangan untuk dapat melakukan produksi
        dan pengiriman sesuai dengan data pemesanan yang telah terlamir di atas. Terimakasih.</p>

    <div class="footer">
        <p>Hormat Kami</p>
        <p>Accounting Finance</p>
    </div>
</body>
</html>
