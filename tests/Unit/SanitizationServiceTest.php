<?php

namespace Tests\Unit;

use App\Services\SanitizationService;
use HTMLPurifier;
use Mockery;
use PHPUnit\Framework\TestCase;

class SanitizationServiceTest extends TestCase
{
    public function test_sanitize_html_content()
    {
        // Sample HTML content to sanitize
        $html = '<script>alert("xss")</script><p>Hello</p>';
        $expected = '<p>Hello</p>';

        // Mock HTMLPurifier
        $purifierMock = Mockery::mock(HTMLPurifier::class);
        $purifierMock->shouldReceive('purify')
            ->once()
            ->with($html)
            ->andReturn($expected);

        // Inject the mock into SanitizationService
        $service = new SanitizationService($purifierMock);

        // Test the sanitize method
        $this->assertEquals($expected, $service->sanitize($html));
    }
}
