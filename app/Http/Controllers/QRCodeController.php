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
        try {
            $request->validate([
                'type' => 'required|in:text,url,sms,whatsapp,phone,email,location,wifi,vcard',
                'content' => 'required_unless:type,sms,whatsapp,email,location,wifi,vcard|string|max:2000',
                'size' => 'nullable|integer|min:100|max:1000',
                'margin' => 'nullable|integer|min:0|max:50',
                'error_correction' => 'nullable|in:L,M,Q,H',
                'foreground_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'logo' => 'nullable|image|max:2048',
                // Additional validations for specific types
                'phone' => 'required_if:type,sms,whatsapp,phone|nullable|string|max:20',
                'email' => 'required_if:type,email|nullable|email|max:255',
                'latitude' => 'required_if:type,location|nullable|numeric|between:-90,90',
                'longitude' => 'required_if:type,location|nullable|numeric|between:-180,180',
                'ssid' => 'required_if:type,wifi|nullable|string|max:32',
                'name' => 'required_if:type,vcard|nullable|string|max:255',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        }

        try {
            $content = $this->formatContent($request->type, $request->all());

            if (empty($content)) {
                return response()->json(['error' => 'Content cannot be empty'], 400);
            }

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

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatContent($type, $data)
    {
        switch ($type) {
            case 'url':
                return $data['content'] ?? '';
            case 'sms':
                $phone = $data['phone'] ?? '';
                $message = $data['message'] ?? '';
                return $phone ? "sms:" . $phone . "?body=" . urlencode($message) : '';
            case 'whatsapp':
                $phone = $data['phone'] ?? '';
                $message = $data['message'] ?? '';
                return $phone ? "https://wa.me/" . $phone . "?text=" . urlencode($message) : '';
            case 'phone':
                return $data['content'] ?? '';
            case 'email':
                $email = $data['email'] ?? '';
                $subject = $data['subject'] ?? '';
                $message = $data['message'] ?? '';
                return $email ? "mailto:" . $email . "?subject=" . urlencode($subject) . "&body=" . urlencode($message) : '';
            case 'location':
                $lat = $data['latitude'] ?? '';
                $lng = $data['longitude'] ?? '';
                return ($lat && $lng) ? "geo:" . $lat . "," . $lng : '';
            case 'wifi':
                $ssid = $data['ssid'] ?? '';
                $password = $data['password'] ?? '';
                $security = $data['security'] ?? 'WPA';
                $hidden = isset($data['hidden']) && $data['hidden'] ? 'true' : 'false';
                return $ssid ? "WIFI:T:" . $security . ";S:" . $ssid . ";P:" . $password . ";H:" . $hidden . ";;" : '';
            case 'vcard':
                $name = $data['name'] ?? '';
                $org = $data['organization'] ?? '';
                $phone = $data['phone'] ?? '';
                $email = $data['email'] ?? '';
                $website = $data['website'] ?? '';
                return $name ? "BEGIN:VCARD\nVERSION:3.0\nFN:" . $name . "\nORG:" . $org . "\nTEL:" . $phone . "\nEMAIL:" . $email . "\nURL:" . $website . "\nEND:VCARD" : '';
            default:
                return $data['content'] ?? '';
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
