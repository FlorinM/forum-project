<template>
    <ForumLayout>
        <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
            <Title v-if="subcategories.length > 0">
                Categories in {{ category.name }}
            </Title>

            <!-- Display Subcategories -->
            <CategoryItem
                v-for="(subcat, index) in subcategories"
                :key="subcat.id"
                :category="subcat"
                :latestPost="latestPosts[index]"
                :nrOfThreads="threadCounts[index]"
            />

            <!-- New Thread Button (only visible to authenticated users) -->
            <Link
                v-if="$page.props.auth.user"
                :href="route('threads.create', { category: category.id })"
                class="inline-block mb-4 px-4 py-2 text-white bg-blue-600 rounded-md transition duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
            >
                Start New Thread
            </Link>

            <!-- Display Threads for the Current Category -->
            <div v-if="threads.length" class="mt-6">
                <Title v-if="threads.length > 0">
                    Threads of {{ category.name }}
                </Title>

                <ThreadItem
                    v-for="(thread, index) in threads"
                    :key="thread.id"
                    :category="category"
                    :thread="thread"
                    :postCount="postCounts[index]"
                    :latestPost="latestPostInThreads[index]"
                />
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'; // Importing Link from Inertia
import CategoryItem from '@/Components/CategoryItem.vue'; // Import the CategoryItem component
import ThreadItem from '@/Components/ThreadItem.vue';
import Title from '@/Components/Title.vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import { useFormatDate } from '@/Composables/useFormatDate';

const props = defineProps({
    category: Object, // The current category passed to this component
    subcategories: Array, // The first-level subcategories passed to this component
    threads: Array, // The threads for the current category passed to the component

    // The latest post for each subcategory, synced with subcategories
    latestPosts: Array,

    // The latest post for each thread in current category, synced with threads
    latestPostInThreads: Array,

    // The number of posts for each thread in threads, synced with threads
    postCounts: Array,

    // The number of threads for each subcategory in subcategories, synced with subcategories
    threadCounts: Array,
});
</script>
