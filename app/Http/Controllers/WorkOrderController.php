<?php

namespace App\Http\Controllers;

use App\Models\WorkOrder;
use Barryvdh\DomPDF\Facade\Pdf;

class WorkOrderController extends Controller
{
    public function exportPdf($id)
    {
        // Fetch the work order with related inquiry and products
        $workOrder = WorkOrder::with('inquiry.products', 'paymentReceipt')->findOrFail($id);

        // Pass data to the Blade view
        $data = [
            'workOrder' => $workOrder,
        ];

        // Generate the PDF
        $pdf = Pdf::loadView('work_orders.pdf', $data);

        // Return the PDF as a downloadable response
        return $pdf->download("work_order_{$workOrder->id}.pdf");
    }
}
