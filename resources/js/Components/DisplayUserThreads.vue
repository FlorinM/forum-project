<template>
    <div class="bg-white p-6 rounded-md shadow-md overflow-scroll max-h-[500px]">
        <!-- Header -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Threads</h2>

        <!-- No Threads Case -->
        <p v-if="visitedUserThreads.length === 0" class="text-gray-600">
            No threads to display.
        </p>

        <!-- Threads List -->
        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="(thread, index) in visitedUserThreads"
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
import { ref, onMounted } from 'vue';

const props = defineProps({
    userId: {
        type: Number,
        required: true,
    },
});

// State variable for storing visted user's topics
const visitedUserThreads = ref([]);

// Fetch topics from backend or sessionStorage
const fetchThreads = async () => {
    // Make a variable name for cache
    const cachedThreads = 'visitedUserThreads' + props.userId;

    // Check if threads are already stored in sessionStorage
    const storedThreads = sessionStorage.getItem(cachedThreads);

    if (storedThreads) {
        // If threads are stored in sessionStorage, use them
        visitedUserThreads.value = JSON.parse(storedThreads);
        return; // Skip fetching data again
    }

    // If no topics in sessionStorage, fetch from the backend
    try {
        const response = await axios.get('/visited-user-threads/' + props.userId);
        visitedUserThreads.value = response.data.threads || [];

        // Store the fetched threads in sessionStorage for the rest of the session
        sessionStorage.setItem(cachedThreads, JSON.stringify(visitedUserThreads.value));
    } catch (error) {
        console.error('Error fetching threads:', error);
    }
};

/**
 * Utility function to format date.
 * @param {string} dateString
 * @returns {string}
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString(undefined, { year: 'numeric', month: 'long', day: 'numeric' });
}

onMounted(fetchThreads);
</script>
