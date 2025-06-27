<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QRCodeController;
use App\Http\Controllers\QRScannerController;

Route::get('/', function () {
    return view('home');
});

// QR Code Generator Routes
Route::get('/bikin-qr', [QRCodeController::class, 'index'])->name('qr.generator');
Route::post('/generate', [QRCodeController::class, 'generate'])->name('qr.generate');
Route::post('/download', [QRCodeController::class, 'download'])->name('qr.download');

// QR Code Scanner Routes
Route::get('/scan-qr', [QRScannerController::class, 'index'])->name('qr.scanner');
Route::post('/scan', [QRScannerController::class, 'scan'])->name('qr.scan');
