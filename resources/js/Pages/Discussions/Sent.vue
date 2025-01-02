<template>
    <div class="bg-white p-6 rounded-md shadow-md overflow-scroll">
        <!-- Header -->
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Sent messages</h2>

        <!-- Empty Sent Case -->
        <p v-if="sent.length === 0" class="text-gray-600">
            Sent container is empty.
        </p>

        <!-- Sent List -->
        <ul v-else class="divide-y divide-gray-200">
            <li
                v-for="(discussion, index) in sent"
                :key="index"
                class="py-4 flex items-start justify-between"
            >
                <!-- Discussion Data -->
                <div>
                    <h3 class="text-m font-medium text-blue-600 hover:text-blue-700 flex">
                        <div>
                            {{ discussion.participant_nickname }}
                        </div>

                        <div class="ml-4">
                            {{ discussion.subject }}
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
const sent = ref([]);

onMounted(async () => {
        sent.value = await useFetchData('/discussions-sent/' + usePage().props.auth.user.id);
        console.log(sent.value);
});
</script>
