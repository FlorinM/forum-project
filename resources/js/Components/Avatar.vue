<template>
  <img
    :src="avatarUrl"
    :alt="props.altText"
    class="w-12 h-12 rounded-full border border-gray-300"
  />
</template>

<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Props passed to the Avatar component
const props = defineProps({
  avatarUrl: {
    type: [String, null],
    default: null,
  },
  altText: {
    type: String,
    default: 'User Avatar'
  }
});

// Compute the final avatar URL, using a fallback if not available
const avatarUrl = computed(() => {
    const $path = usePage().props.baseUrl + '/storage/';
    return props.avatarUrl ? $path + props.avatarUrl : $path + 'avatars/default-avatar.jpg';
});
</script>
