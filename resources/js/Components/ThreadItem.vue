<template>
    <div class="w-full grid grid-cols-10 mb-1 bg-blue-50 border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200">
        <div class="col-span-7">
            <Link :href="route('threads.show', [category.id, thread.id])"
                class="block text-sm text-blue-600 w-full text-left pl-5 pt-2 pb-0 mb-1"
            >
                {{ thread.title }}
            </Link>

            <div class="col-span-1 mt-1 text-xs text-blue-500 pl-5 pt-0 mb-1">
                Started by {{ thread.user.nickname }} at
                {{ formatDate(thread.created_at) }}
            </div>
        </div>

        <div class="col-span-1 mt-5 text-xs text-blue-500">
            {{ postCount }} replies
        </div>

        <!-- Display Latest Post for Each Thread -->
        <div
            v-if="latestPost"
            class="col-span-2 mt-3 text-xs text-blue-500"
        >
            <div v-if="latestPost.user" class="hover:underline">
                <Link :href="route('visited.user.show', latestPost.user.id)">
                    {{ latestPost.user.nickname }}
                </Link>
            </div>

            <div class="hover:underline">
                <Link :href="route('find.post', latestPost.id)">
                    {{  new Date(latestPost.created_at).toLocaleString()  }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useFormatDate } from '@/Composables/useFormatDate.js';
import { Link } from '@inertiajs/vue3';

// Props to receive data
const props = defineProps({
    category: Object, // The current category
    thread: Object, // Individual thread data
    postCount: Number, // Number of replies to the thread
    latestPost: Object, // Latest post in this thread
});

// Use custom date formatting function
const formatDate = useFormatDate;
</script>
