<template>
    <ForumLayout>
        <div class="max-w-5xl mx-auto p-3 bg-gray-100 rounded-lg shadow-md">
            <Title>Search Results</Title>

            <div v-if="posts.length === 0" class="text-center p-6">
                <p class="text-lg text-gray-600">
                    No results found for "{{ query }}"
                </p>
            </div>

            <div v-else>
                <p class="mb-4 text-gray-800">
                    Showing results for: <strong>"{{ query }}"</strong>
                </p>

                <ul class="list-none p-0">
                    <li
                        v-for="post in posts"
                        :key="post.id"
                        class="w-full mb-2 bg-white p-4 rounded-lg shadow-sm"
                    >
                        <Link
                            :href="route('find.post', post.id)"
                            class="block text-sm text-blue-700 w-full text-left"
                        >
                            <h2
                                class="text-sm hover:text-blue-500 transition duration-200"
                            >
                                {{ post.thread.title }}
                            </h2>
                            <div class="text-sm text-gray-600 mt-1">
                                <span class="ml-2">{{
                                    formatDate(post.created_at)
                                }}</span>
                            </div>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Title from '@/Components/Title.vue';
import { useFormatDate } from '@/Composables/useFormatDate';

const props = defineProps({
    // Expecting the search query passed as a prop
    query: String,

    // Expecting an array of threads from the search results
    posts: Array,
});

// Use custom date formatting function
const formatDate = useFormatDate;
</script>
