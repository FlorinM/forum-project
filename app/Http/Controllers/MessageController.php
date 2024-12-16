<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\SendMessageRequest;
use App\Services\SanitizationService;
use App\Services\ImageExtractorService;

class MessageController extends Controller
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

    /**
     * Send a message from the authenticated user to another user.
     *
     * @param  App\Http\Requests\SendMessageRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(SendMessageRequest $request)
    {
        if (config('quill_use_image_handler')) {
            // Extract images from the string and replace with urls
            $message = $this->imageExtractorService->extractAndReplaceImages($request->input('message'));
        } else {
            $message = $request->input('message');
        }

        // Sanitize the content using the service
        $message = $this->sanitizationService->sanitize($message);

        // Create the message
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->input('receiver_id'),
            'message' => $message,
        ]);

        // Optionally notify the receiver (e.g., real-time or email notifications)

        // Return the response
        return back();
    }
}
