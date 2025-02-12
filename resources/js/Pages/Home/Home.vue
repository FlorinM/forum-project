<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h1 class="text-center text-xl font-bold text-gray-800 mb-6">X forum</h1>

      <ul class="list-none p-0">
        <li
          v-for="category in categories"
          :key="category.id"
          class="w-full mb-1 bg-white"
        >
          <Link
            :href="route('categories.subcategories', category.id)"
            class="block text-sm text-blue-700 w-full text-left p-5"
          >
            <h2 class="text-xl mt-3 hover:text-blue-500 transition duration-200">
              {{ category.name }}
            </h2>
            <div class="text-sm pl-4">{{ category.description }}</div>
          </Link>

          <template v-if="category.subcategories && category.subcategories.length">
            <CategoryItem
              v-for="subcat in category.subcategories"
              :key="subcat.id"
              :category="subcat"
              :latestPost="latestPosts[category.id][subcat.id]"
              :nrOfThreads="threadCounts[category.id][subcat.id]"
            />
          </template>
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
  // Expecting an array of categories, including their direct subcategories
  categories: Array,

  // The latest post in each subcategory, synced with categories
  latestPosts: Array,

  // The number of threads in each subcategory, synced with categories
  threadCounts: Array,
});
</script>
