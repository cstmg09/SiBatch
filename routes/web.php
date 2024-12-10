<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\InquiriesController;
use App\Http\Controllers\InvoiceController;

use App\Http\Controllers\WorkOrderController;



Route::get('/', [ProductController::class, 'index'])->name('home');
Route::post('/inquiries', [InquiriesController::class, 'store'])->name('inquiries.store');
Route::get('/invoice/{id}/pdf', [InvoiceController::class, 'exportPdf'])->name('invoice.pdf');
Route::get('/work-orders/{id}/pdf', [WorkOrderController::class, 'exportPdf'])->name('work-orders.pdf');
