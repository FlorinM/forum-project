<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="h-6 w-6">

                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-6V3.5L18.5 8H13z" />
                </svg>
            </button>
        </template>

        <template #content>
            <div v-if="contentIFollow.length > 0">
                <DropdownLink
                    v-for="thread in contentIFollow"
                    :key="thread.id"
                    :href="route('threads.show', [thread.category_id, thread.id])"
                    class="text-gray-700 hover:bg-blue-200 border-b border-gray-300"
                    :class="{'font-bold': thread.bold}"
                >
                    {{ thread.title }}
                </DropdownLink>
            </div>
            <div v-else class="px-4 py-2 text-gray-500">You haven't followed any content yet.</div>
        </template>
    </Dropdown>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useFetchData } from '@/Composables/useFetchData';

const contentIFollow = ref([]);

onMounted(async () => {
    contentIFollow.value = await useFetchData('/followed-content', 6000);
});
</script>
