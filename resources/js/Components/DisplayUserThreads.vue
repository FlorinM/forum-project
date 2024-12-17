<template>
    <div class="bg-white p-6 rounded-md shadow-md overflow-scroll max-h-[500px]">
        <!-- Header -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Threads</h2>

        <!-- No Threads Case -->
        <p v-if="threads.length === 0" class="text-gray-600">
            No threads to display.
        </p>

        <!-- Threads List -->
        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="(thread, index) in threads"
                :key="index"
                class="py-4 flex items-start justify-between"
            >
                <!-- Thread Content -->
                <div>
                    <h3 class="text-lg font-medium text-blue-600 hover:text-blue-700">
                        <a :href="thread.link">{{ thread.title }}</a>
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Started on: {{ formatDate(thread.created_at) }}
                    </p>
                </div>

                <!-- Optional Number Display -->
                <span v-if="number" class="text-sm font-semibold text-gray-700">
                    {{ number }}
                </span>
            </li>
        </ul>
    </div>
</template>

<script setup>
const props = defineProps({
    threads: {
        type: Array,
        required: true,
    },
    number: {
        type: Number,
        default: 10,
    },
});

/**
 * Utility function to format date.
 * @param {string} dateString
 * @returns {string}
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
}
</script>
