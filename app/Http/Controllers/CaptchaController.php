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
        // Generate a random 6-character string
        $code = strtoupper(Str::random(6));
        Session::put('captcha', $code);

        // Create the CAPTCHA image
        $image = imagecreate(120, 40);
        $backgroundColor = imagecolorallocate($image, 220, 220, 220); // Silver background
        $textColor = imagecolorallocate($image, 0, 0, 0); // Black text

        imagestring($image, 5, 10, 10, $code, $textColor);

        // Output the image as PNG
        ob_start();
        imagepng($image);
        $contents = ob_get_clean();

        imagedestroy($image);

        return response($contents)->header('Content-Type', 'image/png');
    }
}
