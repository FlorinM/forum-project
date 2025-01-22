<?php

namespace App\Http\Controllers;

use App\Services\SanitizationService;
use App\Services\ImageExtractorService;
use App\Services\UserService;

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
     * Service for managing user-related functionality like bans and approvals.
     *
     * @var App\Services\UserService
     */
    protected $userService;

    /**
     * Initializes the controller with the required services for sanitization
     * and image extraction, enabling secure input handling and centralized
     * image management.
     *
     * @param App\Services\SanitizationService $sanitizationService Service for input sanitization.
     * @param App\Services\ImageExtractorService $imageExtractorService Service for image extraction and processing.
     * @param App\Services\UserService $userService Service for user-related functionality.
     */
    public function __construct(
        SanitizationService $sanitizationService,
        ImageExtractorService $imageExtractorService,
        UserService $userService
    ) {
        $this->sanitizationService = $sanitizationService;
        $this->imageExtractorService = $imageExtractorService;
        $this->userService = $userService;
    }
}
