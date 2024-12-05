<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Requests\StorePostRequest;
use App\Services\SanitizationService;
use App\Services\ImageExtractorService;

class PostController extends Controller
{
    protected $sanitizationService;
    protected $imageExtractorService;

    /**
     * Specifies whether to use Quill's default image handling behavior.
     * Set to true to enable it, false to disable.
     * If enabled, ensure 'image' is included in Quill's configuration.
     *
     * @var bool
     */
    protected $useDefaultQuillImageHandler = true;

    public function __construct(
        SanitizationService $sanitizationService,
        ImageExtractorService $imageExtractorService
    ) {
        $this->sanitizationService = $sanitizationService;
        $this->imageExtractorService = $imageExtractorService;
    }

    /**
     * Display the posts for a given thread in a category.
     *
     * @param Category $category The category to which the thread belongs
     * @param Thread $thread The thread to display posts for
     * @return \Inertia\Response The Inertia response with the category, thread, and posts
     */
    public function show (Category $category, Thread $thread)
    {
        $posts = $thread->posts()->with('user')->get(); // Eager load the user relationship

        return Inertia::render('Posts/Show', [
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts
        ]);
    }

    /**
     * Store a new reply for a thread.
     *
     * @param Request $request The incoming HTTP request
     * @param Category $category The category of the thread
     * @param Thread $thread The thread to which the reply belongs
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePostRequest $request, Category $category, Thread $thread)
    {
        if ($this->useDefaultQuillImageHandler) {
            // Extract images from the string and replace with urls
            $content = $this->imageExtractorService->extractAndReplaceImages($request->input('content'));
        } else {
            $content = $request->input('content');
        }

        // Sanitize the content using the service
        $content = $this->sanitizationService->sanitize($content);

        // Remove nested <blockquote>...</blockquote> if any
        $content = $this->removeNestedBlockquotes($content);

        // Create the new post
        $thread->posts()->create([
            'user_id' => auth()->id(), // Use the currently authenticated user
            'thread_id' => $thread->id,
            'content' => $content,
        ]);

        return back(); // Redirect back to the thread view
    }

    /**
     * Removes nested blockquotes from the input HTML.
     *
     * This method processes the provided HTML input, searches for all <blockquote> elements,
     * and removes any blockquotes that are nested inside other blockquotes. Root blockquotes
     * (those not inside any other blockquote) and any other content outside blockquotes are
     * preserved.
     *
     * @param string $input The HTML string to process.
     *
     * @return string The HTML string with nested blockquotes removed.
     */
    protected function removeNestedBlockquotes($input)
    {
        // If the input is empty, return the empty string immediately
        if (empty($input)) {
            return '';
        }

        // Create a new DOMDocument instance
        $dom = new \DOMDocument();

        // Suppress warnings from invalid HTML, such as unclosed tags
        libxml_use_internal_errors(true);

        // Load the input HTML string, making sure it handles empty or malformed input gracefully
        $dom->loadHTML($input, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Find all blockquote elements
        $blockquotes = $dom->getElementsByTagName('blockquote');

        // Loop through all blockquotes and remove nested ones
        foreach ($blockquotes as $blockquote) {
            // Check if the current blockquote is inside any ancestor blockquote
            if ($this->hasAncestorBlockquote($blockquote)) {
                // Remove the nested blockquote and everything inside it (including nested blockquotes)
                $blockquote->parentNode->removeChild($blockquote);
            }
        }

        // Save and return the cleaned HTML string
        // Trim the result to avoid any extra newline or whitespace
        return trim($dom->saveHTML());
    }

    /**
     * Checks if the given blockquote is inside any ancestor blockquote.
     *
     * This method traverses up the DOM tree from the provided blockquote element to check if it
     * is nested within another blockquote element. If an ancestor blockquote is found, the method
     * returns true, indicating that the blockquote is nested. Otherwise, it returns false.
     *
     * @param DOMElement $blockquote The blockquote element to check.
     *
     * @return bool Returns true if the blockquote is inside another blockquote, otherwise false.
     */
    private function hasAncestorBlockquote($blockquote)
    {
        // Traverse up the ancestor chain of the blockquote
        while ($blockquote = $blockquote->parentNode) {
            if ($blockquote->nodeName === 'blockquote') {
                return true; // Found an ancestor blockquote
            }
        }
        return false; // No ancestor blockquote found
    }
}
