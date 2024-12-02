<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Http\Controllers\PostController;
use App\Services\SanitizationService;
use ReflectionMethod;
use HTMLPurifier;

class PostControllerTest extends TestCase
{
    /**
     * @var PostController $controller The instance of PostController to be tested.
     */
    protected $controller;

    /**
     * @var array $blockquoteData Array holding test data for various blockquote scenarios.
     */
    protected $blockquoteData = [
        // Test case 1: Nested blockquotes
        [
            'input' => '<blockquote><blockquote>Nested Blockquote</blockquote></blockquote>',
            'expected' => '<blockquote></blockquote>',
        ],
        // Test case 2: No nested blockquotes (single blockquote)
        [
            'input' => '<blockquote>Single Blockquote</blockquote>',
            'expected' => '<blockquote>Single Blockquote</blockquote>',
        ],
        // Test case 3: Multiple blockquotes without nesting
        [
            'input' => '<blockquote>First Blockquote</blockquote><blockquote>Second Blockquote</blockquote>',
            'expected' => '<blockquote>First Blockquote</blockquote><blockquote>Second Blockquote</blockquote>',
        ],
        // Test case 4: Empty content
        [
            'input' => '',
            'expected' => '',
        ],
        // Test case 5: No blockquotes
        [
            'input' => '<p>No blockquotes here</p>',
            'expected' => '<p>No blockquotes here</p>',
        ],
        // Test case 6: Mixed input
        [
            'input' => '<div><blockquote>Text1</blockquote></div><blockquote><div><blockquote>Text2</blockquote></div></blockquote>',
            'expected' => '<div><blockquote>Text1</blockquote></div><blockquote><div></div></blockquote>',
        ],
    ];

    /**
     * Set up the test environment.
     *
     * This method prepares the test environment by creating a mock of the HTMLPurifier class,
     * which returns the input HTML content unchanged. It also creates instances of the
     * SanitizationService and PostController with the mocked HTMLPurifier.
     */
    public function setUp(): void
    {
        parent::setUp();

        // Mock the HTMLPurifier class
        $purifierMock = $this->createMock(HTMLPurifier::class);

        // Mock the purify method to return the input unchanged (just for testing the blockquote removal)
        $purifierMock->method('purify')
        ->willReturnCallback(function($htmlContent) {
            return $htmlContent; // Simply return the content without modifying it (no real sanitization for testing)
        });

        // Create an instance of the SanitizationService with the mocked HTMLPurifier
        $sanitizationService = new SanitizationService($purifierMock);

        // Create an instance of the PostController with the mocked SanitizationService
        $this->controller = new PostController($sanitizationService);
    }

    /**
     * Test removeNestedBlockquotes method.
     */
    public function test_remove_nested_blockquotes()
    {
        // Loop through each test case
        foreach ($this->blockquoteData as $data) {
            $input = $data['input'];
            $expected = $data['expected'];

            // Use reflection to access the protected method
            $reflection = new ReflectionMethod(PostController::class, 'removeNestedBlockquotes');
            $reflection->setAccessible(true);

            // Call the protected method with the input HTML string
            $result = $reflection->invoke($this->controller, $input);

            // Assert that the result matches the expected output
            $this->assertEquals($expected, $result);
        }
    }
}

