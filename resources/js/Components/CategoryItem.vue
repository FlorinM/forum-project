<template>
    <div
        class="w-full mb-1 grid grid-cols-10 bg-blue-50 border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
    >
        <div class="col-span-7">
            <Link
                :href="route('categories.subcategories', category.id)"
                class="block text-sm text-blue-600 w-full text-left pl-5 pt-3 pb-3"
            >
                <span
                    class="inline-block w-1 h-1 bg-blue-500 rounded-full mb-1"
                ></span>
                {{ category.name }}
                <div class="text-xs mt-1 pl-2">{{ category.description }}</div>
            </Link>
        </div>

        <div class="col-span-1 mt-5 text-xs text-blue-500">
            {{ nrOfThreads }} threads
        </div>

        <!-- Display Latest Post for current category -->
        <div
            v-if="latestPost"
            class="col-span-2 content-center text-xs text-blue-500"
        >
            <div class="hover:underline">
                <Link
                    :href="
                        route('threads.show', [
                            category.id,
                            latestPost?.thread?.id,
                        ])
                    "
                >
                    {{ latestPost?.thread?.title }}
                </Link>
            </div>

            <div class="hover:underline">
                <Link :href="route('find.post', latestPost.id)">
                    {{ new Date(latestPost?.created_at).toLocaleString() }}
                </Link>
            </div>

            <div v-if="$page.props.auth.user" class="hover:underline">
                <Link :href="route('visited.user.show', latestPost?.user?.id)">
                    By {{ latestPost?.user?.nickname }}
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useFormatDate } from '@/Composables/useFormatDate';

const props = defineProps({
    category: Object, // Current category
    latestPost: Object, // The latest post in current category
    nrOfThreads: Number, // The number of threads in current category
});
</script>
