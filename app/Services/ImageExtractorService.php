<?php

namespace App\Services;

use App\Services\ImageStorageService;

class ImageExtractorService
{
    protected $imageStorageService;

    public function __construct(ImageStorageService $imageStorageService)
    {
        $this->imageStorageService = $imageStorageService;
    }

    /**
     * Processes HTML content to extract base64 images and replace them with stored URLs.
     *
     * @param string $htmlContent
     * @return string Processed HTML content
     */
    public function extractAndReplaceImages(string $htmlContent): string
    {
        $base64Images = $this->findBase64Images($htmlContent);

        if (empty($base64Images['matches'])) {
            return $htmlContent; // Return early if no images found
        }

        return $this->replaceBase64ImagesWithUrls($htmlContent, $base64Images);
    }

    /**
     * Finds base64 images in the HTML content.
     *
     * @param string $htmlContent
     * @return array Contains matches, mime types, and base64 data
     */
    protected function findBase64Images(string $htmlContent): array
    {
        $regex = '/<img[^>]+src="data:image\/([a-zA-Z]*);base64,([^"]+)"/';
        preg_match_all($regex, $htmlContent, $matches);

        return [
            'matches' => $matches[0] ?? [],
            'mimeTypes' => $matches[1] ?? [],
            'base64Data' => $matches[2] ?? [],
        ];
    }

    /**
     * Replaces base64 images in the HTML content with stored URLs.
     *
     * @param string $htmlContent
     * @param array $base64Images
     * @return string Updated HTML content
     */
    protected function replaceBase64ImagesWithUrls(string $htmlContent, array $base64Images): string
    {
        foreach ($base64Images['matches'] as $index => $match) {
            $mimeType = $base64Images['mimeTypes'][$index];
            $base64Data = $base64Images['base64Data'][$index];

            $binaryImageData = $this->decodeBase64Image($base64Data);
            $imageUrl = $this->storeImageAndGetUrl($binaryImageData, $mimeType);

            $htmlContent = $this->replaceImageTag($htmlContent, $match, $imageUrl);
        }

        return $htmlContent;
    }

    /**
     * Decodes a base64 image string into binary data.
     *
     * @param string $base64Data
     * @return string Binary image data
     */
    protected function decodeBase64Image(string $base64Data): string
    {
        return base64_decode($base64Data);
    }

    /**
     * Stores the binary image data and returns the URL.
     *
     * @param string $binaryImageData
     * @param string $mimeType
     * @return string URL of the stored image
     */
    protected function storeImageAndGetUrl(string $binaryImageData, string $mimeType): string
    {
        return $this->imageStorageService->storeImage($binaryImageData, $mimeType);
    }

    /**
     * Replaces the base64 image tag with a new tag containing the image URL.
     *
     * @param string $htmlContent
     * @param string $originalTag
     * @param string $imageUrl
     * @return string Updated HTML content
     */
    protected function replaceImageTag(string $htmlContent, string $originalTag, string $imageUrl): string
    {
        $newTag = '<img src="' . $imageUrl . '">';
        return str_replace($originalTag, $newTag, $htmlContent);
    }
}
