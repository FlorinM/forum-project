<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * Generate a CAPTCHA image.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        // Generate random CAPTCHA code
        $code = strtoupper(Str::random(6));
        Session::put('captcha', ['code' => $code, 'expires_at' => now()->addMinutes(5)]);

        // Create an image
        $width = 250;
        $height = 100;
        $image = imagecreate($width, $height);

        // Define colors
        $backgroundColor = imagecolorallocate($image, 255, 255, 255); // White background
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
        $lineColor = imagecolorallocate($image, 64, 64, 64); // Gray lines
        $dotColor = imagecolorallocate($image, 150, 150, 150); // Light gray dots

        // Add background noise (dots)
        for ($i = 0; $i < 1000; $i++) {
            imagesetpixel($image, random_int(0, $width - 1), random_int(0, $height - 1), $dotColor);
        }

        // Add random lines for more noise
        for ($i = 0; $i < 8; $i++) {
            imageline(
                $image,
                random_int(0, $width),
                      random_int(0, $height),
                      random_int(0, $width),
                      random_int(0, $height),
                      $lineColor
            );
        }

        // Define the path to the font file
        $fontPath = public_path('/storage/TTF/tlc_mink_by_jpreckless2444_ddk56uv.otf.ttf');

        // Add CAPTCHA text with distortion
        if (file_exists($fontPath)) {
            $x = random_int(10, 20);
            $y = random_int(50, 80);
            for ($i = 0; $i < strlen($code); $i++) {
                imagettftext(
                    $image,
                    random_int(24, 32),             // Random font size
                             random_int(-30, 30),           // Random angle
                             $x,                            // X-coordinate
                             $y,                            // Y-coordinate
                             $textColor,                    // Text color
                             $fontPath,                     // Font file
                             $code[$i]                      // Character
                );
                $x += random_int(30, 40); // Space between characters
            }
        } else {
            // Fallback to plain text if font file is missing
            imagestring($image, 5, 50, 40, $code, $textColor);
        }

        // Output the image as PNG
        ob_start();
        imagepng($image);
        $contents = ob_get_clean();

        imagedestroy($image);

        return response($contents)->header('Content-Type', 'image/png');
    }
}
