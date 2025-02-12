<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 z-50"
    >
        <div class="bg-white p-6 rounded-md shadow-md w-1/3">
            <h2 class="text-lg font-semibold mb-4">Report Post</h2>

            <textarea
                v-model="reportContent"
                class="w-full p-2 border border-gray-300 rounded-md"
                placeholder="Describe the issue..."
                rows="4"
            ></textarea>

            <div class="flex justify-end space-x-2 mt-4">
                <button
                    @click="closeModal"
                    class="px-4 py-2 bg-gray-400 text-white rounded-md hover:bg-gray-500"
                >
                    Cancel
                </button>

                <button
                    @click="submitReport"
                    class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600"
                >
                    Submit
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const props = defineProps({
    postId: Number,
    isOpen: Boolean,
});

const emit = defineEmits(['close']);

const reportContent = ref('');

const closeModal = () => {
    reportContent.value = '';
    emit('close');
};

const submitReport = () => {
    if (!reportContent.value.trim()) return;

    router.post(
        '/reports',
        {
            post_id: props.postId,
            reporter_id: usePage().props.auth.user.id, // Authenticated user ID
            content: reportContent.value.trim(),
        },
        {
            preserveScroll: true, // Keep user on the same page
            onSuccess: () => closeModal(),
        },
    );
};
</script>
