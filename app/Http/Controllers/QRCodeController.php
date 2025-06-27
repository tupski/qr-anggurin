<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\ErrorCorrectionLevel;

class QRCodeController extends Controller
{
    public function index()
    {
        return view('qr.generator');
    }

    public function generate(Request $request)
    {
        $request->validate([
            'type' => 'required|in:text,url,sms,whatsapp,phone,email,location,wifi,vcard',
            'content' => 'required|string',
            'size' => 'nullable|integer|min:100|max:1000',
            'margin' => 'nullable|integer|min:0|max:50',
            'error_correction' => 'nullable|in:L,M,Q,H',
            'foreground_color' => 'nullable|string',
            'background_color' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
        ]);

        $content = $this->formatContent($request->type, $request->all());

        $qrCode = QrCode::create($content)
            ->setSize($request->size ?? 300)
            ->setMargin($request->margin ?? 10);

        // Set error correction level
        switch ($request->error_correction ?? 'M') {
            case 'L':
                $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Low);
                break;
            case 'Q':
                $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Quartile);
                break;
            case 'H':
                $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::High);
                break;
            default:
                $qrCode->setErrorCorrectionLevel(ErrorCorrectionLevel::Medium);
        }

        // Set colors
        if ($request->foreground_color) {
            $foregroundColor = $this->hexToRgb($request->foreground_color);
            $qrCode->setForegroundColor(new Color($foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']));
        }

        if ($request->background_color) {
            $backgroundColor = $this->hexToRgb($request->background_color);
            $qrCode->setBackgroundColor(new Color($backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']));
        }

        $writer = new PngWriter();

        // Handle logo
        $logo = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('temp', 'public');
            $logo = Logo::create(storage_path('app/public/' . $logoPath))
                ->setResizeToWidth($request->logo_size ?? 50);
        }

        $result = $writer->write($qrCode, $logo);

        // Clean up temporary logo file
        if ($logo && $logoPath) {
            unlink(storage_path('app/public/' . $logoPath));
        }

        return response($result->getString())
            ->header('Content-Type', $result->getMimeType())
            ->header('Content-Disposition', 'inline; filename="qrcode.png"');
    }

    private function formatContent($type, $data)
    {
        switch ($type) {
            case 'url':
                return $data['content'];
            case 'sms':
                return "sms:" . $data['phone'] . "?body=" . urlencode($data['message'] ?? '');
            case 'whatsapp':
                return "https://wa.me/" . $data['phone'] . "?text=" . urlencode($data['message'] ?? '');
            case 'phone':
                return "tel:" . $data['content'];
            case 'email':
                return "mailto:" . $data['email'] . "?subject=" . urlencode($data['subject'] ?? '') . "&body=" . urlencode($data['message'] ?? '');
            case 'location':
                return "geo:" . $data['latitude'] . "," . $data['longitude'];
            case 'wifi':
                return "WIFI:T:" . $data['security'] . ";S:" . $data['ssid'] . ";P:" . $data['password'] . ";H:" . ($data['hidden'] ? 'true' : 'false') . ";;";
            case 'vcard':
                return "BEGIN:VCARD\nVERSION:3.0\nFN:" . $data['name'] . "\nORG:" . ($data['organization'] ?? '') . "\nTEL:" . ($data['phone'] ?? '') . "\nEMAIL:" . ($data['email'] ?? '') . "\nURL:" . ($data['website'] ?? '') . "\nEND:VCARD";
            default:
                return $data['content'];
        }
    }

    private function hexToRgb($hex)
    {
        $hex = ltrim($hex, '#');
        return [
            'r' => hexdec(substr($hex, 0, 2)),
            'g' => hexdec(substr($hex, 2, 2)),
            'b' => hexdec(substr($hex, 4, 2))
        ];
    }
}
