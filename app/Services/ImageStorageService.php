<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImageStorageService
{
    /**
     * Stores the binary image data and returns the URL.
     *
     * @param string $binaryImageData
     * @param string $mimeType
     * @return string URL of the stored image
     */
    public function storeImage(string $binaryImageData, string $mimeType): string
    {
        // Determine the appropriate file extension based on the MIME type
        $extension = $this->getExtensionFromMimeType($mimeType);

        // Generate a unique file name
        $fileName = uniqid('image_', true) . '.' . $extension;

        // Define the storage path
        $filePath = 'uploads/images/' . $fileName;

        // Store the image in the configured disk (e.g., "public" or "s3")
        Storage::disk('public')->put($filePath, $binaryImageData);

        // Return the public URL to the stored image
        //return Storage::disk('public')->url($filePath);

        // Return the full URL to the stored image using the `asset` helper
        return asset('storage/' . $filePath);  // Ensure full URL is generated
    }

    /**
     * Maps MIME types to file extensions.
     *
     * @param string $mimeType
     * @return string
     * @throws \InvalidArgumentException If the MIME type is unsupported
     */
    protected function getExtensionFromMimeType(string $mimeType): string
    {
        $mimeMap = [
            'jpeg' => 'jpg',
            'png' => 'png',
            'gif' => 'gif',
            'bmp' => 'bmp',
            'webp' => 'webp',
        ];

        if (!isset($mimeMap[$mimeType])) {
            throw new \InvalidArgumentException("Unsupported MIME type: $mimeType");
        }

        return $mimeMap[$mimeType];
    }
}
