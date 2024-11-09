<template>
  <li class="w-full mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200">
    <Link
      :href="route('categories.show', category.id)"
      class="block text-sm text-blue-600 w-full text-left p-5"
    >
      {{ category.name }} (Created by User ID: {{ category.user_id }})
    </Link>

    <ul v-if="category.all_subcategories && category.all_subcategories.length" class="pl-5">
      <CategoryItem
        v-for="subcat in category.all_subcategories"
        :key="subcat.id"
        :category="subcat"
      />
    </ul>
  </li>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  category: Object, // Each category item passed recursively
});
</script>
