<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Requests\StorePostRequest;

class PostController extends BaseServiceController
{
    /**
     * Display the posts for a given thread in a category.
     *
     * @param Category $category The category to which the thread belongs
     * @param Thread $thread The thread to display posts for
     * @return \Inertia\Response The Inertia response with the category, thread, and posts
     */
    public function show(Category $category, Thread $thread)
    {
        // Get the current page from the query string, default to page 1 if not set
        $page = request()->query('page', 1);

        // Paginate the posts for the current thread
        $posts = $thread->posts()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'page', $page);

        return Inertia::render('Posts/Show', [
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts,
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
        $authUser = auth()->user();

        // Use the UserService to handle bans
        if ($redirect = $this->userService->ifBanned($authUser, 'You are banned from posting.')) {
            return $redirect; // Return the redirect response directly if user is banned
        }

        // Check if the user is a "NewUser" and if their posts are approved
        if ($authUser->hasRole('NewUser') && $this->userService->hasUnapprovedPosts($authUser)) {
            return back()->with([
                'errorNewUserMessage' => 'You cannot post until your previous post is approved.',
            ]);
        }

        // Prepare and sanitize the post content
        $content = $this->prepareContent($request);

        // Create the new post
        $this->createNewPost($thread, $content);

        return back(); // Redirect back to the thread view
    }

    /**
     * Prepare and sanitize the post content.
     *
     * @param \Illuminate\Http\Request $request The validated incoming request containing the post content.
     * @return string The sanitized and processed content ready to be saved in the database.
     */
    private function prepareContent(Request $request)
    {
        if (config('quill.use_image_handler')) {
            // Extract images from the string and replace with urls
            $content = $this->imageExtractorService->extractAndReplaceImages($request->input('content'));
        } else {
            $content = $request->input('content');
        }

        // Sanitize the content using the service
        $content = $this->sanitizationService->sanitize($content);

        // Remove nested <blockquote>...</blockquote> if any
        $content = $this->removeNestedBlockquotes($content);

        return $content;
    }

    /**
     * Create a new post and set the 'approved' field to false (or true if the user is not a 'NewUser').
     *
     * This method creates a new post associated with the given thread. It checks if the authenticated user
     * has the 'NewUser' role, and if so, the post's 'approved' field is set to false (indicating it needs approval).
     * If the user is not a 'NewUser', the 'approved' field is set to true, allowing the post to be automatically approved.
     *
     * @param \App\Models\Thread $thread The thread to which the post will be associated.
     * @param string $content The content of the post being created.
     * @return void
     */
    private function createNewPost(Thread $thread, String $content): void
    {
        $approved = false;
        if (!auth()->user()->hasRole('NewUser')) {
            $approved = true;
        }

        $thread->posts()->create([
            'user_id' => auth()->id(),
            'thread_id' => $thread->id,
            'content' => $content,
            'approved' => $approved,
        ]);
    }

    /**
     * Display the thread and posts based on a specific post ID.
     *
     * @param int $postId The ID of the post to be displayed.
     * @return \Inertia\Response The Inertia response containing the thread,
     *         category, and paginated posts, along with the current page.
     */
    public function showByPostId($postId)
    {
        // Find the post by its ID
        $post = Post::with('thread.category')->findOrFail($postId);

        // Extract the thread and category from the post
        $thread = $post->thread;
        $category = $thread->category;

        // Get the current page from the query string, default to page 1 if not set
        $page = request()->query('page', 1);

        // Determine the current page based on the post position
        // Calculate the page number for the specific post
        $postIndex = $thread->posts()
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->search($postId);

        if ($postIndex !== false) {
            // Calculate the page number by dividing the post index by the number of posts per page
            $page = floor($postIndex / 10) + 1;
        }

        // Paginate the posts for the current thread
        $posts = $thread->posts()
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->paginate(10, ['*'], 'page', $page);

        // Make sure the global prop breadcrumbs is shared via Inertia with latest value
        Inertia::share('breadcrumbs', breadcrumbs($category, $thread));

        return Inertia::render('Posts/Show', [
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts,
            'currentPage' => $page,
            'targetPostId' => $postId,
        ]);
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
