<template>
    <div class="flex justify-center items-center space-x-0.5">
        <!-- Page 1 Link -->
        <Link
            v-if="totalPages > 1"
            :href="generateLink(1)"
            class="px-1.5 py-0.5 text-white text-xs bg-blue-500 rounded hover:bg-blue-600"
        >
            1
        </Link>

        <!-- Page 2 Link -->
        <Link
            v-if="totalPages > 1"
            :href="generateLink(2)"
            class="px-1.5 py-0.5 text-white text-xs bg-blue-500 rounded hover:bg-blue-600"
        >
            2
        </Link>

        <!-- Page 3 Link -->
        <Link
            v-if="totalPages > 2"
            :href="generateLink(3)"
            class="px-1.5 py-0.5 text-white text-xs bg-blue-500 rounded hover:bg-blue-600"
        >
            3
        </Link>

        <!-- Last Page Link -->
        <Link
            v-if="totalPages > 3"
            :href="generateLink(totalPages)"
            class="px-1.5 py-0.5 text-white text-xs bg-blue-500 rounded hover:bg-blue-600"
        >
            {{ totalPages }} >>
        </Link>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';

const props = defineProps({
    category: Object,
    thread: Object,
    postCount: Number,
});

// Calculate total pages, rounding up if there's a remainder
const postsPerPage = inject('postsPerPage');
const totalPages = Math.ceil(props.postCount / postsPerPage);

function generateLink(page) {
    return route('threads.show', {
        category: props.category.id,
        thread: props.thread.id,
        page: page, // The page number you want to load
    });
}
</script>

<style scoped>
button, a {
    transition: background-color 0.2s;
}
</style>
