<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">Categories</h1>

      <ul class="list-none p-0">
        <li
          v-for="category in categories"
          :key="category.id"
          class="w-full mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
        >
          <Link
            :href="route('categories.subcategories', category.id)"
            class="block text-sm text-blue-600 w-full text-left p-5"
          >
            {{ category.name }} (Created by User ID: {{ category.user_id }})
          </Link>

          <ul v-if="category.subcategories && category.subcategories.length" class="pl-5">
            <CategoryItem
              v-for="subcat in category.subcategories"
              :key="subcat.id"
              :category="subcat"
            />
          </ul>
        </li>
      </ul>
    </div>
  </ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import CategoryItem from '@/Components/CategoryItem.vue';

const props = defineProps({
  categories: Array, // Expecting an array of categories, including their direct subcategories
});
</script>
