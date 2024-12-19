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
                    <h3 class="text-m font-medium text-blue-600 hover:text-blue-700">
                        <Link :href="route('threads.show', [thread.category_id, thread.id])">
                            {{ thread.title }}
                        </Link>
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">
                        Started on: {{ useFormatDate(thread.created_at) }}
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
import { Link } from '@inertiajs/vue3';
import { useFormatDate } from '@/Composables/useFormatDate';
import { useFetchData } from '@/Composables/useFetchData';

const props = defineProps({
    userId: {
        type: Number,
        required: true,
    },
});

// State variable for storing visted user's topics
const visitedUserThreads = ref([]);

onMounted(async () => {
        visitedUserThreads.value = await useFetchData('/visited-user-threads/' + props.userId, 60000);
});
</script>
