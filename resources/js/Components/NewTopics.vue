<template>
    <div class="new-topics pt-20">

        <div class="inline-block">
            <h2 class="text-m font-bold mb-4">Latest Topics</h2>
        </div>

        <!-- Cycle Button -->
        <FlattenedButton @click="nextPage">
            {{ currentPage }}/{{ totalPages }} >>
        </FlattenedButton>

        <!-- Grid of Topics -->
        <div class="grid grid-cols-4 gap-1">
            <div
                v-for="topic in paginatedTopics"
                :key="topic.id"
                class="topic-item text-xs p-4 border rounded-md"
            >
                <span class="w-1 h-1 bg-blue-500 rounded-full"></span>&nbsp;
                <Link
                    :href="`/categories/${topic.category_id}/threads/${topic.id}`"
                    class="text-blue-600 hover:underline"
                    :title="topic.title"
                >
                    {{ truncateTitle(topic.title) }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref, computed, onMounted } from 'vue';
    import axios from 'axios';
    import { Link } from '@inertiajs/vue3';
    import FlattenedButton from '@/Components/FlattenedButton.vue';
    import { useFetchData } from '@/Composables/useFetchData';

    // State variables
    const topics = ref([]);
    const currentPage = ref(1); // Start at page 1
    const topicsPerPage = 16;
    const totalPages = 5;

    // Truncate long titles
    const truncateTitle = (title, maxLength = 30) => {
        return title.length > maxLength ? `${title.slice(0, maxLength)}...` : title;
    };

    // Compute the topics to display on the current page
    const paginatedTopics = computed(() => {
        const start = (currentPage.value - 1) * topicsPerPage;
        const end = start + topicsPerPage;
        return topics.value.slice(start, end);
    });

    // Cycle to the next page
    const nextPage = () => {
        currentPage.value = currentPage.value === totalPages ? 1 : currentPage.value + 1;
    };

onMounted(async () => {
        topics.value = await useFetchData('/new-topics');
});
</script>

<style scoped>
    .new-topics {
        max-width: 1000px;
        margin: 0 auto;
    }

    .topic-item {
        display: flex;
        align-items: center;
        text-align: center;
        height: 30px;
    }

    button {
        cursor: pointer;
    }
</style>
