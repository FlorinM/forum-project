<?php

use App\Models\Category;
use App\Models\Thread;

if (! function_exists('breadcrumbs')) {
    /**
     * Generate breadcrumb data for a given category and optional thread.
     *
     * @return array The breadcrumb trail with names and IDs
     */
    function breadcrumbs(): array {
        $breadcrumbs = [];

        // Retrieve the category and thread instances from route model binding
        $category = request()->route('category');  // Automatically gets the category from the route
        $thread = request()->route('thread');      // Automatically gets the thread from the route

        // Build breadcrumb path by traversing up through parent categories if category exists
        if ($category instanceof Category) {
            while ($category) {
                $breadcrumbs[] = ['name' => $category->name, 'id' => $category->id];
                $category = $category->parent; // Move up to the parent category
            }

            // Reverse to display from top-level to current category
            $breadcrumbs = array_reverse($breadcrumbs);
        }

        // If a thread is present, add it as the last breadcrumb
        if ($thread instanceof Thread) {
            $breadcrumbs[] = ['name' => $thread->title, 'id' => $thread->id];
        }

        return $breadcrumbs;
    }
}
