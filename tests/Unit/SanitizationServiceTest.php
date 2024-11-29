<?php

namespace Tests\Unit;

use App\Services\SanitizationService;
use HTMLPurifier;
use Mockery;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SanitizationServiceTest extends TestCase
{
    /**
     * Array of input HTML content and expected sanitized output.
     *
     */
    private array $testCases = [
        // XSS attacks
        ['<script>alert("xss")</script><p>Hello</p>', '<p>Hello</p>'],
        ['<img src="javascript:alert(\'xss\')" />', ''],
        ['<iframe src="http://malicious-site.com"></iframe>', '<iframe></iframe>'],
        ['<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnc3NzJyk8L3NjcmlwdD4=" />', ''],  // Malicious data URI

        // Allowed content
        ['<p>Allowed content</p>', '<p>Allowed content</p>'],
        ['<b>Bold text</b>', '<b>Bold text</b>'],

        // Mixed content
        ['<p><script>alert("xss")</script>Valid text</p>', '<p>Valid text</p>'],
        ['<div onclick="alert(\'xss\')">Click me</div>', '<div>Click me</div>'],

        // Event handlers
        ['<img src="image.jpg" onerror="alert(\'xss\')" />', '<img src="image.jpg" alt="image.jpg">'],
        ['<div onmouseover="alert(\'xss\')">Hover me</div>', '<div>Hover me</div>'],

        // Iframe with specific attributes
        ['<iframe src="http://malicious-site.com" allowfullscreen></iframe>', '<iframe></iframe>'],
        ['<iframe src="http://malicious-site.com" sandbox></iframe>', '<iframe></iframe>'],

        // Script-related tags
        ['<noscript>alert("xss")</noscript>', 'alert("xss")'],
        ['<embed src="http://malicious-site.com" />', ''],
        ['<object data="http://malicious-site.com" type="text/html"></object>', ''],

        // Potentially dangerous HTML attributes
        ['<div style="background-image:url(javascript:alert(\'xss\'))">Test</div>', '<div>Test</div>'],
        ['<a href="javascript:alert(\'xss\')">Click me</a>', '<a>Click me</a>'],

        // Suspicious URL schemes
        ['<a href="javascript:alert(\'xss\')">Click me</a>', '<a>Click me</a>'],
        ['<a href="http://safe-url.com">Visit Safe Site</a>', '<a href="http://safe-url.com">Visit Safe Site</a>'],

        // Nested XSS attacks
        ['<div><script>alert("xss")</script><p>Valid content</p></div>', '<div><p>Valid content</p></div>'],
        ['<p><img src="javascript:alert(\'xss\')" /></p>', '<p></p>'],

        // Unknown or custom tags
        ['<custom-tag>Some content</custom-tag>', 'Some content'],
        ['<unknown-tag><p>Test</p></unknown-tag>', '<p>Test</p>'],
    ];

    /**
     * Test the sanitization of HTML using a mocked HTMLPurifier instance.
     *
     * @return void
     */
    public function test_sanitize_html_content()
    {
        // Mock HTMLPurifier
        $purifierMock = Mockery::mock(HTMLPurifier::class);

        // Define mocked behavior for each test case
        foreach ($this->testCases as [$html, $expected]) {
            $purifierMock->shouldReceive('purify')
            ->once()
            ->with($html)
            ->andReturn($expected);
        }

        // Inject the mock into SanitizationService
        $service = new SanitizationService($purifierMock);

        // Test all cases
        foreach ($this->testCases as [$html, $expected]) {
            $this->assertEquals($expected, $service->sanitize($html));
        }
    }

    /**
     * Test sanitizing HTML content with a real HTMLPurifier instance.
     *
     * This test checks whether the `SanitizationService` correctly sanitizes various
     * HTML inputs by using the actual HTMLPurifier service registered in the Laravel container.
     *
     * @return void
     */
    public function test_sanitize_html_content_with_real_purifier()
    {
        // Retrieve the real HTMLPurifier from the container
        $realPurifier = App::make(HTMLPurifier::class);

        // Inject the real purifier into SanitizationService
        $service = new SanitizationService($realPurifier);

        // Test all cases
        foreach ($this->testCases as [$html, $expected]) {
            $this->assertEquals($expected, $service->sanitize($html));
        }
    }
}
