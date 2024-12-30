<?php

namespace App\Http\Controllers;

use App\Services\SanitizationService;
use App\Services\ImageExtractorService;

class BaseServiceController extends Controller
{
    /**
     * Service for sanitizing input data to ensure security and proper formatting.
     *
     * @var App\Services\SanitizationService
     */
    protected $sanitizationService;

    /**
     * Service for extracting and processing images, enabling centralized management of image-related functionality.
     *
     * @var App\Services\ImageExtractorService
     */
    protected $imageExtractorService;

    /**
     * Initializes the controller with the required services for sanitization
     * and image extraction, enabling secure input handling and centralized
     * image management.
     *
     * @param App\Services\SanitizationService $sanitizationService Service for input sanitization.
     * @param App\Services\ImageExtractorService $imageExtractorService Service for image extraction and processing.
     */
    public function __construct(
        SanitizationService $sanitizationService,
        ImageExtractorService $imageExtractorService
    ) {
        $this->sanitizationService = $sanitizationService;
        $this->imageExtractorService = $imageExtractorService;
    }
}
