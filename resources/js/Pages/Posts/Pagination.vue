<template>
    <div class="flex justify-center items-center space-x-0.5 mt-6 mb-6">
        <!-- First Page Button -->
        <button
            :disabled="currentPage === 1"
            @click="changePage(1)"
            class="px-2 py-0.5 text-white text-sm bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50"
        >
            <<
        </button>

        <!-- Previous Page Button -->
        <button
            :disabled="currentPage === 1"
            @click="changePage(currentPage - 1)"
            class="px-2 py-0.5 text-white text-sm bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50"
        >
            Previous
        </button>

        <!-- Dynamic Page Numbers -->
        <div v-for="page in pages" :key="page">
            <!-- Current Page -->
            <span
                v-if="page === currentPage"
                class="px-2 py-0.5 rounded bg-gray-300 text-gray-600"
            >
                Page {{ page }} of {{ lastPage }}
            </span>
            <!-- Other Pages -->
            <button
                v-else
                @click="changePage(page)"
                :class="[
                    'px-2 py-0.5 text-white text-sm rounded',
                    'bg-blue-500 hover:bg-blue-600',
                ]"
            >
                {{ page }}
            </button>
        </div>

        <!-- Next Page Button -->
        <button
            :disabled="currentPage === lastPage"
            @click="changePage(currentPage + 1)"
            class="px-2 py-0.5 text-white text-sm bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50"
        >
            Next
        </button>

        <!-- Last Page Button -->
        <button
            :disabled="currentPage === lastPage"
            @click="changePage(lastPage)"
            class="px-2 py-0.5 text-white text-sm bg-blue-500 rounded hover:bg-blue-600 disabled:opacity-50"
        >
            >>
        </button>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    currentPage: {
        type: Number,
        required: true,
    },
    lastPage: {
        type: Number,
        required: true,
    },
    onPageChange: {
        type: Function,
        required: true,
    },
});

// Compute the pages to display dynamically
const pages = computed(() => {
    const range = [];
    const start = Math.max(1, props.currentPage - 3); // Up to 3 pages left of the current page
    const end = Math.min(props.lastPage, props.currentPage + 3); // Up to 3 pages right of the current page

    for (let i = start; i <= end; i++) {
        range.push(i);
    }
    return range;
});

function changePage(page) {
    if (page >= 1 && page <= props.lastPage) {
        props.onPageChange(page); // Call the parent-provided function
    }
}
</script>

<style scoped>
/* Optional styling for better appearance */
button {
    transition: background-color 0.2s;
}
</style>
