<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_HTML5Config;

class SanitizationService
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor for the SanitizationService class.
     *
     * Configuration Highlights:
     * - Enables HTML5 support with `HTMLPurifier_HTML5Config::createDefault()`.
     * - Allows inline styles for text formatting, such as color, background-color, and text alignment.
     * - Permits YouTube embeds via `<iframe>` with the `HTML.SafeIframe` directive and a safe URI pattern.
     */
    public function __construct()
    {
        // Create the default configuration for HTML5 with the necessary directives
        $config = HTMLPurifier_HTML5Config::createDefault();

        // You can adjust additional configurations if needed
        // Example: Allow YouTube embeds in iframe tags
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^//www\.youtube\.com/embed/%');

        // Instantiate the HTMLPurifier object
        $this->purifier = new HTMLPurifier($config);
    }

    /**
     * Sanitizes the provided HTML content.
     *
     * This method cleans the HTML input using the configured HTMLPurifier instance, ensuring
     * it conforms to the allowed elements and attributes while removing harmful scripts or
     * invalid content.
     *
     * @param string $htmlContent The raw HTML content to be sanitized.
     * @return string The sanitized HTML content.
     */
    public function sanitize(string $htmlContent): string
    {
        return $this->purifier->purify($htmlContent);
    }
}
