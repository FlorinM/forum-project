<template>
    <Dropdown align="right" width="48">
        <template #trigger>
            <button
                v-if="notifications.length > 0"
                class="relative flex items-center p-2 rounded-full bg-blue-800 text-white hover:bg-blue-700 focus:outline-none"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="h-6 w-6"
                >
                    <path
                        d="M12 2a7 7 0 00-7 7v3.18l-1.64 3.27A1 1 0 005 18h14a1 1 0 00.87-1.55L18 12.18V9a7 7 0 00-7-7zm0 18a2 2 0 01-2-2h4a2 2 0 01-2 2z"
                    />
                </svg>
                <span
                    v-if="notifications.length > 0"
                    class="absolute top-0 right-0 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full"
                >
                    {{ notifications.length }}
                </span>
            </button>
        </template>

        <template #content>
            <div v-if="notifications.length > 0">
                <DropdownLink
                    v-for="notification in notifications"
                    :key="notification.id"
                    :href="route('discussions.show', notification.data.discussion_id)"
                    @click.prevent="markAsRead(notification.id)"
                    class="text-gray-700 hover:bg-gray-200"
                >
                    You got a message from {{ notification.data.sender }}
                </DropdownLink>
            </div>
            <div v-else class="px-4 py-2 text-gray-500">No new notifications</div>
        </template>
    </Dropdown>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { useFetchData } from '@/Composables/useFetchData';

const notifications = ref([]);

onMounted(async () => {
    notifications.value = await useFetchData('/notifications-unread');
});

const markAsRead = async (notificationId) => {
    try {
        await axios.patch(`/notifications/${notificationId}/read`);

        // Update the local notifications list to reflect the changes
        notifications.value = notifications.value.filter(notification => notification.id !== notificationId);

        // Also remove the marked notification from sessionStorage
        const cacheName = '/notifications-unread'.replace(/\//g, 'c'); // Same as in useFetchData
        const storedData = sessionStorage.getItem(cacheName);

        if (storedData) {
            let cachedNotifications = JSON.parse(storedData);

            // Remove the marked notification from cached data
            cachedNotifications = cachedNotifications.filter(notification => notification.id !== notificationId);

            // Update sessionStorage with the new array of notifications
            sessionStorage.setItem(cacheName, JSON.stringify(cachedNotifications));
        }
    } catch (error) {
        console.error('Failed to mark notification as read:', error);
    }
};
</script>
