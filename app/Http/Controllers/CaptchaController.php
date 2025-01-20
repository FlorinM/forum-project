<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Http\Response;

class CaptchaController extends Controller
{
    /**
     * CAPTCHA code session key
     */
    private const CAPTCHA_SESSION_KEY = 'captcha';

    /**
     * CAPTCHA expiration time in minutes
     */
    private const CAPTCHA_EXPIRATION_MINUTES = 5;

    /**
     * Image width
     */
    private int $width = 250;

    /**
     * Image height
     */
    private int $height = 100;

    /**
     * Font path
     */
    private string $fontPath;

    public function __construct()
    {
        $this->fontPath = public_path('/storage/TTF/tlc_mink_by_jpreckless2444_ddk56uv.otf.ttf');
    }

    /**
     * Generate a CAPTCHA image.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        // Generate and store CAPTCHA code
        $code = $this->generateCaptchaCode();
        $this->storeCaptchaCode($code);

        // Create CAPTCHA image
        $image = $this->createImage();

        // Add noise to the image
        $this->addNoise($image);

        // Add CAPTCHA text with distortion
        $this->addCaptchaText($image, $code);

        // Output the image as PNG
        $response = $this->outputImageAsPng($image);

        // Clean up
        imagedestroy($image);

        return $response;
    }

    /**
     * Generate a random CAPTCHA code.
     *
     * @return string
     */
    private function generateCaptchaCode(): string
    {
        return strtoupper(Str::random(6));
    }

    /**
     * Store CAPTCHA code in the session with expiration time.
     *
     * @param string $code
     * @return void
     */
    private function storeCaptchaCode(string $code): void
    {
        Session::put(self::CAPTCHA_SESSION_KEY, [
            'code' => $code,
            'expires_at' => now()->addMinutes(self::CAPTCHA_EXPIRATION_MINUTES),
        ]);
    }

    /**
     * Create an empty CAPTCHA image.
     *
     * @return resource
     */
    private function createImage()
    {
        $image = imagecreatetruecolor($this->width, $this->height);

        // Transparent background (if possible)
        $transparentColor = imagecolorallocatealpha($image, 255, 255, 255, 127);
        imagefill($image, 0, 0, $transparentColor); // Fill with transparent background
        imagesavealpha($image, true);

        return $image;
    }

    /**
     * Add background noise to the CAPTCHA image.
     *
     * @param resource $image
     * @return void
     */
    private function addNoise($image): void
    {
        // Light gray dots for background noise
        $dotColor = imagecolorallocate($image, 150, 150, 150);

        // Gray lines for additional noise
        $lineColor = imagecolorallocate($image, 64, 64, 64);

        // Add random dots (Reduce the number to 500 for visibility)
        for ($i = 0; $i < 500; $i++) {
            $x = random_int(0, $this->width - 1);
            $y = random_int(0, $this->height - 1);
            imagesetpixel($image, $x, $y, $dotColor); // Add random pixels (dots)
        }

        // Add random lines for more noise
        for ($i = 0; $i < 8; $i++) {
            $x1 = random_int(0, $this->width - 1);
            $y1 = random_int(0, $this->height - 1);
            $x2 = random_int(0, $this->width - 1);
            $y2 = random_int(0, $this->height - 1);

            imageline(
                $image,
                $x1, $y1,
                $x2, $y2,
                $lineColor
            );
        }
    }

    /**
     * Add CAPTCHA text with distortion to the image.
     *
     * @param resource $image
     * @param string $code
     * @return void
     */
    private function addCaptchaText($image, string $code): void
    {
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text
        $x = random_int(10, 20);
        $y = random_int(50, 80);

        if (file_exists($this->fontPath)) {
            // Add each character with distortion
            for ($i = 0; $i < strlen($code); $i++) {
                imagettftext(
                    $image,
                    random_int(24, 32),                      // Random font size
                             random_int(-30, 30),            // Random angle
                             $x,                             // X-coordinate
                             $y,                             // Y-coordinate
                             $textColor,                     // Text color
                             $this->fontPath,                // Font file
                             $code[$i]                       // Character
                );
                $x += random_int(30, 40); // Space between characters
            }
        } else {
            // Fallback to plain text if font file is missing
            imagestring($image, 5, 50, 40, $code, $textColor);
        }
    }

    /**
     * Output the CAPTCHA image as PNG.
     *
     * @param resource $image
     * @return \Illuminate\Http\Response
     */
    private function outputImageAsPng($image): Response
    {
        ob_start();
        imagepng($image);
        $contents = ob_get_clean();

        return response($contents)->header('Content-Type', 'image/png');
    }
}
