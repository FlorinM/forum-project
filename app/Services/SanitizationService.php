<?php

namespace App\Services;

use HTMLPurifier;
use HTMLPurifier_Config;

class SanitizationService
{
    /**
     * @var HTMLPurifier
     */
    protected $purifier;

    /**
     * Constructor for the SanitizationService class.
     *
     * Initializes the HTMLPurifier instance with configurations tailored for Quill editor compatibility.
     * This ensures that only elements and attributes supported by Quill's toolbar are allowed,
     * while maintaining robust sanitization to prevent XSS and other malicious inputs.
     *
     * Configuration Highlights:
     * - Enables HTML5 support with `HTMLPurifier_HTML5Config::createDefault()`.
     * - Whitelists specific HTML elements and attributes used in Quill (e.g., headings, lists, bold, links, etc.).
     * - Allows inline styles for text formatting, such as color, background-color, and text alignment.
     * - Permits YouTube embeds via `<iframe>` with the `HTML.SafeIframe` directive and a safe URI pattern.
     *
     * This ensures that user input is sanitized while preserving the content formatting
     * and features provided by the Quill rich-text editor.
     */
    public function __construct()
    {
        // Create the default configuration for HTML5 with the necessary directives
        $config = \HTMLPurifier_HTML5Config::createDefault();

        // Allow only elements and attributes used by Quill
        $config->set('HTML.Allowed', 'p, h1, h2, h3, ul, ol, li, b, i, u, a[href|target], span[style], pre, code, blockquote, img[src|alt|title|width|height], iframe[src|width|height], strong, em, br, div[style]');

        // Allow inline styles required by Quill
        $config->set('CSS.AllowedProperties', 'color, background-color, text-align, font-weight, font-style, text-decoration');

        // Allow YouTube embeds
        $config->set('HTML.SafeIframe', true);
        $config->set('URI.SafeIframeRegexp', '%^https://www\.youtube\.com/embed/%');

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
