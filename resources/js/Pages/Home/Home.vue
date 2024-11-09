
<template>
<ForumLayout>
  <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
    <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">Categories</h1>
    <ul class="list-none p-0">
      <CategoryItem
        v-for="category in categories"
        :key="category.id"
        :category="category"
      />
    </ul>
  </div>
</ForumLayout>
</template>

<script setup>
import ForumLayout from '@/Layouts/ForumLayout.vue';
import CategoryItem from '@/Components/CategoryItem.vue';

const props = defineProps({
  categories: Array, // Expecting an array of categories with nested subcategories
});
</script>
