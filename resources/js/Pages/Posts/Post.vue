<template>
    <div class="w-full mb-4 bg-white border border-gray-300 rounded-md p-5">
        <div class="grid grid-cols-10 gap-4">
            <!-- Left Side: Avatar and Name (10%) -->
            <div class="col-span-1 flex flex-col space-y-2">
                <Avatar
                    :avatarUrl="post.user.avatar_url"
                    :altText="post.user.name"
                />
                <p class="text-sm text-gray-800 text-center truncate">
                    <Link :href="route('visited.user.show', post.user.id)">
                        {{ post.user.nickname }}
                    </Link>
                </p>
                <div class="text-xs">Registered: {{ formatDate(post.user.created_at) }}</div>
                <div class="text-xs">Posts: {{ post.user.posts_count }}</div>
                <div v-if="isUserBanned" class="text-sm"><strong>banned</strong></div>
            </div>

            <!-- Right Side: Content and Quote Button (90%) -->
            <div class="col-span-9 flex flex-col">
                <div class="text-xs mb-6">Posted {{ new Date(post.created_at).toLocaleString() }}</div>

                <div
                    class="prose max-w-full text-xs text-gray-800 mb-4"
                    v-html="post.content"
                ></div>
                <div v-if="$page.props.auth.user" class="flex justify-end">
                    <button
                        @click="quotePost(post)"
                        class="quote-btn text-right py-0.5 px-3 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 focus:outline-none"
                    >
                        Quote
                    </button>

                    <button
                        @click="isReportModalOpen = true"
                        class="report-btn text-right py-0.5 px-3 ml-0.5 bg-red-500 text-white text-sm rounded hover:bg-red-600 focus:outline-none"
                    >
                        Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div
            v-if="
                successMessage && post.id === $page.props.flash.report.post_id
            "
            :class="{
                'text-blue-500': $page.props.flash.report.type === 'success',
                'text-red-500': $page.props.flash.report.type === 'error',
            }"
            class="bg-blue-50 py-2 px-4 rounded shadow-md transition-opacity duration-300"
        >
            {{ successMessage }}
        </div>
    </div>

    <!-- Import and use Report modal -->
    <Report
        :postId="post.id"
        :isOpen="isReportModalOpen"
        @close="isReportModalOpen = false"
    />
</template>

<script setup>
import Avatar from '@/Components/Avatar.vue';
import { Link } from '@inertiajs/vue3';
import Report from '@/Components/Report.vue';
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useFormatDate } from '@/Composables/useFormatDate.js';

const props = defineProps({
    post: Object, // Post data
    quotePost: Function, // Quote method passed from the parent
});

const isReportModalOpen = ref(false);

const successMessage = ref(null);
// Watch for Inertia flash messages
watch(
    () => usePage().props.flash.report,
    (report) => {
        if (report && report.message) {
            successMessage.value = report.message;
            setTimeout(() => {
                successMessage.value = null;
                if (usePage().props.flash.report) {
                    usePage().props.flash.report = null;
                }
            }, 6000);
        }
    },
    { immediate: true },
);

// Check if the user that made the post is banned
const isUserBanned = computed(() => {
    return props.post.user.is_banned &&
           new Date(props.post.user.is_banned) > new Date();
});

// Use custom date formatting function
const formatDate = useFormatDate;
</script>
