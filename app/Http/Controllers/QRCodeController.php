<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Endroid\QrCode\Writer\WebPWriter;
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
                'logoType' => 'nullable|in:none,default,upload',
                'defaultLogo' => 'nullable|string',
                'logoSize' => 'nullable|integer|min:10|max:30',
                'frame_style' => 'nullable|in:none,square,circle,rounded',
                'frame_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'inner_eye_style' => 'nullable|in:square,circle,rounded,leaf',
                'outer_eye_style' => 'nullable|in:square,circle,rounded,leaf',
                'data_style' => 'nullable|in:square,circle,rounded,dot',
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
        $logoPath = null;

        if ($request->logoType === 'upload' && $request->hasFile('logo')) {
            // Custom uploaded logo
            $logoPath = $request->file('logo')->store('temp', 'public');
            $logoSize = ($request->logoSize ?? 20) * ($request->size ?? 300) / 100;
            $logo = Logo::create(storage_path('app/public/' . $logoPath))
                ->setResizeToWidth($logoSize)
                ->setPunchoutBackground(true);
        } elseif ($request->logoType === 'default' && $request->defaultLogo) {
            // Default logo based on QR type - convert SVG to PNG
            $logoSvg = $this->generateDefaultLogoSVG($request->defaultLogo, $request->foreground_color ?? '#000000');
            $logoSize = ($request->logoSize ?? 20) * ($request->size ?? 300) / 100;

            // Convert SVG to PNG using Imagick or GD
            $logoPath = 'temp/default_logo_' . uniqid() . '.png';
            $this->convertSvgToPng($logoSvg, storage_path('app/public/' . $logoPath), $logoSize);

            $logo = Logo::create(storage_path('app/public/' . $logoPath))
                ->setResizeToWidth($logoSize)
                ->setResizeToHeight($logoSize)
                ->setPunchoutBackground(true);
        }

        $result = $writer->write($qrCode, $logo);

        // Clean up temporary logo file
        if ($logo && $logoPath) {
            unlink(storage_path('app/public/' . $logoPath));
        }

            // Return QR with styling information
            $response = response($result->getString())
                ->header('Content-Type', $result->getMimeType())
                ->header('Content-Disposition', 'inline; filename="qrcode.png"');

            // Add custom headers for styling
            if ($request->frame_style && $request->frame_style !== 'none') {
                $response->header('X-Frame-Style', $request->frame_style);
                $response->header('X-Frame-Color', $request->frame_color ?? '#138c79');
            }

            return $response;

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generateDefaultLogoSVG($type, $color)
    {
        $icons = [
            'text' => '<path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'url' => '<path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'sms' => '<path d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'whatsapp' => '<path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488" fill="' . $color . '"/>',
            'phone' => '<path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'email' => '<path d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'location' => '<path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/><path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'wifi' => '<path d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>',
            'vcard' => '<path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" stroke="' . $color . '" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" fill="none"/>'
        ];

        $icon = $icons[$type] ?? $icons['text'];

        return '<?xml version="1.0" encoding="UTF-8"?>
<svg width="100" height="100" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
    <rect width="24" height="24" fill="white" rx="4"/>
    ' . $icon . '
</svg>';
    }

    private function convertSvgToPng($svgContent, $outputPath, $size)
    {
        try {
            // Try using Imagick first
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->setBackgroundColor(new \ImagickPixel('transparent'));
                $imagick->readImageBlob($svgContent);
                $imagick->setImageFormat('png');
                $imagick->resizeImage($size, $size, \Imagick::FILTER_LANCZOS, 1);
                $imagick->writeImage($outputPath);
                $imagick->clear();
                $imagick->destroy();
                return;
            }
        } catch (\Exception $e) {
            // Fall back to creating a simple PNG with GD
        }

        // Fallback: Create a simple PNG with GD based on QR type
        $image = imagecreatetruecolor($size, $size);
        imagesavealpha($image, true);
        $transparent = imagecolorallocatealpha($image, 0, 0, 0, 127);
        imagefill($image, 0, 0, $transparent);

        // Parse foreground color
        $foregroundColor = $this->hexToRgb('#138c79'); // Default color
        $color = imagecolorallocate($image, $foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']);

        $center = $size / 2;
        $radius = $size * 0.35;

        // Create different shapes based on QR type
        switch ($request->defaultLogo ?? 'text') {
            case 'url':
            case 'email':
                // Rectangle for URL/Email
                $rectSize = $radius * 1.4;
                imagefilledrectangle($image, $center - $rectSize/2, $center - $rectSize/2,
                                   $center + $rectSize/2, $center + $rectSize/2, $color);
                break;
            case 'phone':
            case 'sms':
            case 'whatsapp':
                // Rounded rectangle for communication
                $this->imagefilledroundedrectangle($image, $center - $radius, $center - $radius*1.2,
                                                 $center + $radius, $center + $radius*1.2, $radius*0.3, $color);
                break;
            default:
                // Circle for others
                imagefilledellipse($image, $center, $center, $radius * 2, $radius * 2, $color);
                break;
        }

        imagepng($image, $outputPath);
        imagedestroy($image);
    }

    private function imagefilledroundedrectangle($image, $x1, $y1, $x2, $y2, $radius, $color) {
        // Simple rounded rectangle using arcs
        imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
        imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);
        imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
        imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
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

    public function download(Request $request)
    {
        try {
            $request->validate([
                'format' => 'required|in:png,jpg,svg,webp',
                'type' => 'required|in:text,url,sms,whatsapp,phone,email,location,wifi,vcard',
                'content' => 'required_unless:type,sms,whatsapp,email,location,wifi,vcard|string|max:2000',
                'size' => 'nullable|integer|min:100|max:1000',
                'margin' => 'nullable|integer|min:0|max:50',
                'error_correction' => 'nullable|in:L,M,Q,H',
                'foreground_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'background_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
                'logo' => 'nullable|image|max:2048',
                'logoType' => 'nullable|in:none,default,upload',
                'defaultLogo' => 'nullable|string',
                'logoSize' => 'nullable|integer|min:10|max:30',
                // Additional validations for specific types
                'phone' => 'required_if:type,sms,whatsapp,phone|nullable|string|max:20',
                'email' => 'required_if:type,email|nullable|email|max:255',
                'latitude' => 'required_if:type,location|nullable|numeric|between:-90,90',
                'longitude' => 'required_if:type,location|nullable|numeric|between:-180,180',
                'ssid' => 'required_if:type,wifi|nullable|string|max:32',
                'name' => 'required_if:type,vcard|nullable|string|max:255',
            ]);

            $content = $this->formatContent($request->type, $request->all());

            if (empty($content)) {
                return response()->json(['error' => 'Content cannot be empty'], 400);
            }

            $qrCode = QrCode::create($content)
                ->setSize($request->size ?? 300)
                ->setMargin($request->margin ?? 10);

            // Set error correction level
            $errorCorrectionLevels = [
                'L' => ErrorCorrectionLevel::Low,
                'M' => ErrorCorrectionLevel::Medium,
                'Q' => ErrorCorrectionLevel::Quartile,
                'H' => ErrorCorrectionLevel::High,
            ];
            $qrCode->setErrorCorrectionLevel($errorCorrectionLevels[$request->error_correction ?? 'M']);

            // Set colors
            if ($request->foreground_color) {
                $foregroundColor = $this->hexToRgb($request->foreground_color);
                $qrCode->setForegroundColor(new Color($foregroundColor['r'], $foregroundColor['g'], $foregroundColor['b']));
            }

            if ($request->background_color) {
                $backgroundColor = $this->hexToRgb($request->background_color);
                $qrCode->setBackgroundColor(new Color($backgroundColor['r'], $backgroundColor['g'], $backgroundColor['b']));
            }

            // Choose writer based on format
            $writer = match($request->format) {
                'svg' => new SvgWriter(),
                'webp' => new WebPWriter(),
                default => new PngWriter(),
            };

            // Handle logo
            $logo = null;
            $logoPath = null;

            if ($request->logoType === 'upload' && $request->hasFile('logo')) {
                // Custom uploaded logo
                $logoPath = $request->file('logo')->store('temp', 'public');
                $logoSize = ($request->logoSize ?? 20) * ($request->size ?? 300) / 100;
                $logo = Logo::create(storage_path('app/public/' . $logoPath))
                    ->setResizeToWidth($logoSize)
                    ->setResizeToHeight($logoSize)
                    ->setPunchoutBackground(true);
            } elseif ($request->logoType === 'default' && $request->defaultLogo) {
                // Default logo based on QR type - convert SVG to PNG
                $logoSvg = $this->generateDefaultLogoSVG($request->defaultLogo, $request->foreground_color ?? '#000000');
                $logoSize = ($request->logoSize ?? 20) * ($request->size ?? 300) / 100;

                // Convert SVG to PNG
                $logoPath = 'temp/default_logo_' . uniqid() . '.png';
                $this->convertSvgToPng($logoSvg, storage_path('app/public/' . $logoPath), $logoSize);

                $logo = Logo::create(storage_path('app/public/' . $logoPath))
                    ->setResizeToWidth($logoSize)
                    ->setResizeToHeight($logoSize)
                    ->setPunchoutBackground(true);
            }

            $result = $writer->write($qrCode, $logo);

            // Clean up temporary logo file
            if ($logo && $logoPath) {
                unlink(storage_path('app/public/' . $logoPath));
            }

            // Set appropriate headers based on format
            $mimeTypes = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp'
            ];

            $filename = 'qrcode.' . $request->format;

            // For JPG format, convert PNG to JPG with white background
            if ($request->format === 'jpg') {
                $image = imagecreatefromstring($result->getString());
                $width = imagesx($image);
                $height = imagesy($image);

                $jpgImage = imagecreatetruecolor($width, $height);
                $white = imagecolorallocate($jpgImage, 255, 255, 255);
                imagefill($jpgImage, 0, 0, $white);
                imagecopy($jpgImage, $image, 0, 0, 0, 0, $width, $height);

                ob_start();
                imagejpeg($jpgImage, null, 90);
                $jpgData = ob_get_contents();
                ob_end_clean();

                imagedestroy($image);
                imagedestroy($jpgImage);

                return response($jpgData)
                    ->header('Content-Type', $mimeTypes[$request->format])
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }

            return response($result->getString())
                ->header('Content-Type', $mimeTypes[$request->format])
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to download QR code: ' . $e->getMessage()
            ], 500);
        }
    }
}
