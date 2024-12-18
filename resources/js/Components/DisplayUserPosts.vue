<template>
    <div class="bg-white p-6 rounded-md shadow-md overflow-scroll max-h-[500px]">
        <!-- Header -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Posts</h2>

        <!-- No Posts Case -->
        <p v-if="visitedUserPosts.length === 0" class="text-gray-600">
            No posts to display.
        </p>

        <!-- Posts List -->
        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="(post, index) in visitedUserPosts"
                :key="index"
                class="py-4 flex items-start justify-between"
            >
                <!-- Post Content -->
                <div>
                    <h3 class="text-lg font-medium text-blue-600 hover:text-blue-700">
                        <Link :href="route('threads.show', [post.thread.category.id, post.thread.id])">
                            Post in: {{ post.thread.title }}
                        </Link>
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Started on: {{ formatDate(post.created_at) }}
                    </p>
                    <FlattenedButton @click="toggleVisibility(index)">{{ showClose[index] }}</FlattenedButton>
                    <p v-if="visibility[index]" v-html="post.content" class="pl-5 mt-2"></p>
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
import { ref, onMounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import FlattenedButton from '@/Components/FlattenedButton.vue';

const props = defineProps({
    userId: {
        type: Number,
        required: true,
    },
});

// State variable for storing visted user's posts
const visitedUserPosts = ref([]);

// State variables for expanding a post
const visibility = ref([]);
const showClose = ref([]);
const length = ref(0);

// Initialize visibility and showClose refs
watch(() => visitedUserPosts.value, () => {
    length.value = visitedUserPosts.value.length;

    for (let i = 0; i < length.value; ++i) {
        visibility.value[i] = false;
        showClose.value[i] = 'Show';
    }
});

// Show/close visibility of a post
function toggleVisibility (index) {
    visibility.value[index] = !visibility.value[index];
    showClose.value[index] = showClose.value[index] === 'Show' ? 'Close' : 'Show';
}

// Fetch posts from backend or sessionStorage
const fetchPosts = async () => {
    // Make a variable name for cache
    const cachedPosts = 'visitedUserPosts' + props.userId;

    // Check if posts are already stored in sessionStorage
    const storedPosts = sessionStorage.getItem(cachedPosts);

    if (storedPosts) {
        // If posts are stored in sessionStorage, use them
        visitedUserPosts.value = JSON.parse(storedPosts);
        return; // Skip fetching data again
    }

    // If no posts in sessionStorage, fetch from the backend
    try {
        const response = await axios.get('/visited-user-posts/' + props.userId);
        visitedUserPosts.value = response.data.posts || [];

        // Store the fetched posts in sessionStorage for the rest of the session
        sessionStorage.setItem(cachedPosts, JSON.stringify(visitedUserPosts.value));
    } catch (error) {
        console.error('Error fetching posts:', error);
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

onMounted(fetchPosts);
</script>
