<template>
    <div class="bg-white p-6 rounded-md shadow-md overflow-scroll">
        <!-- Header -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Inbox messages</h2>

        <!-- Empty Inbox Case -->
        <p v-if="inbox.length === 0" class="text-gray-600">
            Inbox is empty.
        </p>

        <!-- Inbox List -->
        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="(discussion, index) in inbox"
                :key="index"
                class="py-4 flex items-start justify-between"
            >
                <!-- Discussion Data -->
                <div>
                    <h3 class="text-m font-medium text-blue-600 hover:text-blue-700 flex">
                        <div>
                            {{ discussion.initiator_nickname }}
                        </div>

                        <div class="ml-4">
                            <Link :href="route('discussions.show', [discussion.id])">
                                {{ discussion.subject }}
                            </Link>
                        </div>
                    </h3>

                    <p class="text-sm text-gray-600 mt-1">
                        Last message: {{ useFormatDate(discussion.last_message_at) }}
                    </p>
                </div>
            </li>
        </ul>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { useFormatDate } from '@/Composables/useFormatDate';
import { useFetchData } from '@/Composables/useFetchData';

// State variable for storing discussions in Inbox
const inbox = ref([]);

onMounted(async () => {
        inbox.value = await useFetchData('/discussions-inbox/' + usePage().props.auth.user.id);
        console.log(inbox.value);
});
</script>
