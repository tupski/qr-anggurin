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
        try {
            $request->validate([
                'method' => 'required|in:upload,url,camera,base64',
                'file' => 'required_if:method,upload|file|max:10240',
                'url' => 'required_if:method,url|url',
                'base64' => 'required_if:method,base64|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', array_flatten($e->errors()))
            ], 422);
        }

        try {
            $imagePath = null;

            if ($request->method === 'upload') {
                $imagePath = $request->file('file')->store('temp', 'public');
                $fullPath = storage_path('app/public/' . $imagePath);
            } elseif ($request->method === 'url') {
                // Download image from URL with better error handling
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 30,
                        'user_agent' => 'QR-Anggurin-Scanner/1.0',
                        'follow_location' => true,
                        'max_redirects' => 5
                    ]
                ]);

                $imageContent = @file_get_contents($request->url, false, $context);
                if ($imageContent === false) {
                    throw new \Exception('Failed to download image from URL');
                }

                // Detect file extension from URL or content
                $urlPath = parse_url($request->url, PHP_URL_PATH);
                $extension = pathinfo($urlPath, PATHINFO_EXTENSION);
                if (empty($extension)) {
                    // Try to detect from content
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($imageContent);
                    $extension = match($mimeType) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/svg+xml' => 'svg',
                        'image/bmp' => 'bmp',
                        default => 'jpg'
                    };
                }

                $imagePath = 'temp/' . uniqid() . '.' . $extension;
                file_put_contents(storage_path('app/public/' . $imagePath), $imageContent);
                $fullPath = storage_path('app/public/' . $imagePath);
            } elseif ($request->method === 'base64') {
                // Handle base64 data URL or raw base64
                $base64Data = trim($request->base64);

                $imageContent = null;
                $extension = 'png'; // Default extension

                // Check if it's a data URL format: data:image/type;base64,actualdata
                if (preg_match('/^data:image\/([a-zA-Z0-9+\/]+);base64,(.+)$/', $base64Data, $matches)) {
                    $imageType = $matches[1];
                    $base64Content = $matches[2];

                    // Map image type to extension
                    $extension = match($imageType) {
                        'jpeg', 'jpg' => 'jpg',
                        'png' => 'png',
                        'gif' => 'gif',
                        'svg+xml' => 'svg',
                        'bmp' => 'bmp',
                        'webp' => 'webp',
                        'tiff' => 'tiff',
                        'ico' => 'ico',
                        default => 'png'
                    };
                } else {
                    // Assume it's raw base64 data
                    $base64Content = $base64Data;
                }

                // Decode base64 content
                $imageContent = base64_decode($base64Content);
                if ($imageContent === false) {
                    throw new \Exception('Failed to decode base64 content');
                }

                // Try to detect image type from content if not from data URL
                if (!isset($imageType)) {
                    $finfo = new \finfo(FILEINFO_MIME_TYPE);
                    $mimeType = $finfo->buffer($imageContent);
                    $extension = match($mimeType) {
                        'image/jpeg' => 'jpg',
                        'image/png' => 'png',
                        'image/gif' => 'gif',
                        'image/svg+xml' => 'svg',
                        'image/bmp' => 'bmp',
                        'image/webp' => 'webp',
                        'image/tiff' => 'tiff',
                        'image/x-icon' => 'ico',
                        default => 'png'
                    };
                }

                $imagePath = 'temp/' . uniqid() . '.' . $extension;
                file_put_contents(storage_path('app/public/' . $imagePath), $imageContent);
                $fullPath = storage_path('app/public/' . $imagePath);
            }

            // Decode QR code from image
            $result = $this->decodeQRCode($fullPath);

            // Clean up temporary files
            if ($imagePath && file_exists(storage_path('app/public/' . $imagePath))) {
                unlink(storage_path('app/public/' . $imagePath));
            }

            // Clean up converted files if different from original
            if ($fullPath !== storage_path('app/public/' . $imagePath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            return response()->json([
                'success' => true,
                'data' => $result,
                'type' => $this->detectQRType($result)
            ]);

        } catch (\Exception $e) {
            \Log::error('QR Scan Error: ' . $e->getMessage(), [
                'method' => $request->method,
                'file' => $request->hasFile('file') ? $request->file('file')->getClientOriginalName() : null,
                'url' => $request->url,
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membaca QR Code: ' . $e->getMessage()
            ], 400);
        }
    }

    private function decodeQRCode($imagePath)
    {
        try {
            // Check if file exists
            if (!file_exists($imagePath)) {
                throw new \Exception('Image file not found');
            }

            // Get file info
            $imageInfo = getimagesize($imagePath);
            if ($imageInfo === false) {
                throw new \Exception('Invalid image file');
            }

            $mimeType = $imageInfo['mime'];

            // Convert SVG to PNG if needed
            if ($mimeType === 'image/svg+xml' || pathinfo($imagePath, PATHINFO_EXTENSION) === 'svg') {
                $imagePath = $this->convertSvgToPng($imagePath);
            }

            // Convert other unsupported formats to PNG
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'])) {
                $imagePath = $this->convertImageToPng($imagePath);
            }

            // Try to decode QR code with different methods
            try {
                // First try with default settings
                $qrcode = new QrReader($imagePath);
                $text = $qrcode->text();

                if (!empty($text)) {
                    return $text;
                }

                // Try with TRY_HARDER hint
                $qrcode = new QrReader($imagePath);
                $text = $qrcode->text(['TRY_HARDER' => true]);

                if (!empty($text)) {
                    return $text;
                }

                throw new \Exception('No QR code found in image');

            } catch (\Exception $e) {
                // If QrReader fails, try alternative approach
                if (extension_loaded('imagick')) {
                    return $this->tryImagickDecode($imagePath);
                }
                throw $e;
            }
        } catch (\Exception $e) {
            throw new \Exception('Failed to decode QR code: ' . $e->getMessage());
        }
    }

    private function convertSvgToPng($svgPath)
    {
        try {
            $pngPath = str_replace('.svg', '.png', $svgPath);

            // Try using Imagick first
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick();
                $imagick->setBackgroundColor(new \ImagickPixel('white'));
                $imagick->readImage($svgPath);
                $imagick->setImageFormat('png');
                $imagick->writeImage($pngPath);
                $imagick->clear();
                $imagick->destroy();
                return $pngPath;
            }

            // Fallback: Create a simple white image
            $image = imagecreatetruecolor(400, 400);
            $white = imagecolorallocate($image, 255, 255, 255);
            imagefill($image, 0, 0, $white);
            imagepng($image, $pngPath);
            imagedestroy($image);

            return $pngPath;
        } catch (\Exception $e) {
            throw new \Exception('Failed to convert SVG: ' . $e->getMessage());
        }
    }

    private function convertImageToPng($imagePath)
    {
        try {
            $pngPath = pathinfo($imagePath, PATHINFO_DIRNAME) . '/' . pathinfo($imagePath, PATHINFO_FILENAME) . '.png';

            // Try using Imagick for better format support
            if (extension_loaded('imagick')) {
                $imagick = new \Imagick($imagePath);
                $imagick->setImageFormat('png');
                $imagick->writeImage($pngPath);
                $imagick->clear();
                $imagick->destroy();
                return $pngPath;
            }

            // Fallback to GD
            $image = imagecreatefromstring(file_get_contents($imagePath));
            if ($image === false) {
                throw new \Exception('Cannot create image from file');
            }

            imagepng($image, $pngPath);
            imagedestroy($image);

            return $pngPath;
        } catch (\Exception $e) {
            throw new \Exception('Failed to convert image: ' . $e->getMessage());
        }
    }

    private function tryImagickDecode($imagePath)
    {
        try {
            // Enhance image for better QR detection
            $imagick = new \Imagick($imagePath);

            // Convert to grayscale
            $imagick->transformImageColorspace(\Imagick::COLORSPACE_GRAY);

            // Enhance contrast
            $imagick->contrastImage(true);
            $imagick->contrastImage(true);

            // Sharpen
            $imagick->sharpenImage(0, 1);

            // Save enhanced image
            $enhancedPath = str_replace('.png', '_enhanced.png', $imagePath);
            $imagick->writeImage($enhancedPath);
            $imagick->clear();
            $imagick->destroy();

            // Try to decode enhanced image
            $qrcode = new QrReader($enhancedPath);
            $text = $qrcode->text(['TRY_HARDER' => true]);

            // Clean up enhanced image
            if (file_exists($enhancedPath)) {
                unlink($enhancedPath);
            }

            if (!empty($text)) {
                return $text;
            }

            throw new \Exception('No QR code found even after image enhancement');

        } catch (\Exception $e) {
            throw new \Exception('Failed to decode with Imagick: ' . $e->getMessage());
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
