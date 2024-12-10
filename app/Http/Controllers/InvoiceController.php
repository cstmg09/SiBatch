<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function exportPdf($id)
    {
        // Fetch the invoice with related inquiry and products
        $invoice = Invoice::with('inquiry.products')->findOrFail($id);

        // Pass data to the Blade view
        $data = [
            'invoice' => $invoice,
        ];

        // Generate the PDF
        $pdf = Pdf::loadView('invoices.pdf', $data);

        // Return the PDF as a downloadable response
        return $pdf->download("invoice_{$invoice->id}.pdf");
    }
}
