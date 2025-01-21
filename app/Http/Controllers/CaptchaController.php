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

    /**
     * CaptchaController constructor.
     *
     * Randomly selects one of two available fonts to increase CAPTCHA variety.
     * This helps make the CAPTCHA harder for bots to decode by introducing
     * variability in the text appearance.
     *
     * The selection is determined using a cryptographically secure random integer.
     * Font paths are dynamically set based on the selection.
     */
    public function __construct()
    {
        // Randomly select one of two fonts for CAPTCHA variety
        $randomBoolean = random_int(0, 1) === 1;

        if ($randomBoolean) {
            // Use the first font (Mink)
            $this->fontPath = public_path('/storage/TTF/tlc_mink_by_jpreckless2444_ddk56uv.otf.ttf');
        } else {
            // Use the second font (Short Baby)
            $this->fontPath = public_path('/storage/TTF/Short Baby.ttf');
        }
    }

    /**
     * Generate a CAPTCHA image.
     *
     * @return \Illuminate\Http\Response
     */
    public function generate()
    {
        // Generate CAPTCHA text
        $captchaText = $this->generateCaptchaCode();

        // Extract numbers from the CAPTCHA text
        $numbers = $this->extractNumbersFromCaptcha($captchaText);

        // Use session ID as the cache key for storing numbers
        $cacheKey = 'captcha_numbers_' . session()->getId();

        // Store numbers in the cache (expires in 5 minutes, you can adjust as needed)
        cache()->put($cacheKey, $numbers, now()->addMinutes(5));

        // Generate and store the CAPTCHA image
        $image = $this->createImage();
        $this->addNoise($image);
        $this->addCaptchaText($image, $captchaText);

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

    /**
     * Extract numbers from the CAPTCHA text.
     *
     * @param string $captchaText
     * @return array
     */
    private function extractNumbersFromCaptcha(string $captchaText): array
    {
        preg_match_all('/\d+/', $captchaText, $matches);
        return $matches[0]; // Return the numbers as an array
    }
}
