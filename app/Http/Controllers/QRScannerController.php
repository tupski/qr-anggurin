<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Zxing\QrReader;

class QRScannerController extends Controller
{
    public function index()
    {
        return view('qr.scanner');
    }

    public function scan(Request $request)
    {
        $request->validate([
            'method' => 'required|in:upload,url,camera',
            'file' => 'required_if:method,upload|image|max:10240',
            'url' => 'required_if:method,url|url',
        ]);

        try {
            $imagePath = null;

            if ($request->method === 'upload') {
                $imagePath = $request->file('file')->store('temp', 'public');
                $fullPath = storage_path('app/public/' . $imagePath);
            } elseif ($request->method === 'url') {
                // Download image from URL
                $imageContent = file_get_contents($request->url);
                $imagePath = 'temp/' . uniqid() . '.jpg';
                file_put_contents(storage_path('app/public/' . $imagePath), $imageContent);
                $fullPath = storage_path('app/public/' . $imagePath);
            }

            // Decode QR code from image
            $result = $this->decodeQRCode($fullPath);

            // Clean up temporary file
            if ($imagePath && file_exists(storage_path('app/public/' . $imagePath))) {
                unlink(storage_path('app/public/' . $imagePath));
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'type' => $this->detectQRType($result)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca QR Code: ' . $e->getMessage()
            ], 400);
        }
    }

    private function decodeQRCode($imagePath)
    {
        try {
            $qrcode = new QrReader($imagePath);
            $text = $qrcode->text();

            if (empty($text)) {
                throw new \Exception('No QR code found in image');
            }

            return $text;
        } catch (\Exception $e) {
            throw new \Exception('Failed to decode QR code: ' . $e->getMessage());
        }
    }

    private function detectQRType($content)
    {
        $content = trim($content);

        // Check for specific protocols/formats
        if (strpos($content, 'tel:') === 0) {
            return 'phone';
        } elseif (strpos($content, 'mailto:') === 0) {
            return 'email';
        } elseif (strpos($content, 'sms:') === 0) {
            return 'sms';
        } elseif (strpos($content, 'https://wa.me/') === 0 || strpos($content, 'https://api.whatsapp.com/') === 0) {
            return 'whatsapp';
        } elseif (strpos($content, 'geo:') === 0) {
            return 'location';
        } elseif (strpos($content, 'WIFI:') === 0) {
            return 'wifi';
        } elseif (strpos($content, 'BEGIN:VCARD') === 0) {
            return 'vcard';
        } elseif (filter_var($content, FILTER_VALIDATE_URL)) {
            return 'url';
        } else {
            return 'text';
        }
    }
}
