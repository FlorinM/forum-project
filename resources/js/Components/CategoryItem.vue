<template>
  <div class="w-full mb-1 bg-blue-50 border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200">
    <Link
      :href="route('categories.subcategories', category.id)"
      class="block text-sm text-blue-600 w-full text-left pl-5 pt-3 pb-3"
    >
      <span class="inline-block w-1 h-1 bg-blue-500 rounded-full mb-1"></span>
      {{ category.name }}
      <div class="text-xs mt-1 pl-2">{{ category.description }}</div>
    </Link>

  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import { useFormatDate } from '@/Composables/useFormatDate';

const props = defineProps({
  category: Object,
});
</script>
