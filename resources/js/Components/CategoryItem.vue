<template>
  <li class="w-full mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200">
    <Link
      :href="route('categories.subcategories', category.id)"
      class="block text-sm text-blue-600 w-full text-left p-5"
    >
      {{ category.name }} (Created by User ID: {{ category.user_id }})
    </Link>
  </li>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  category: Object,
});
</script>
