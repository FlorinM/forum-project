<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">Subcategories of {{ category.name }}</h1>

      <ul class="list-none p-0">
        <li
          v-for="subcategory in subcategories"
          :key="subcategory.id"
          class="w-full mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
        >
          <!-- Link to subcategory page -->
          <Link
            :href="route('categories.subcategories', subcategory.id)"
            class="block text-sm text-blue-600 w-full text-left p-5"
          >
            {{ subcategory.name }} (Created by User ID: {{ subcategory.user_id }})
          </Link>

          <!-- Render first-level subcategories using CategoryItem component -->
          <ul v-if="subcategory.subcategories && subcategory.subcategories.length" class="pl-5">
            <CategoryItem
              v-for="subcat in subcategory.subcategories"
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
import { Link } from '@inertiajs/vue3'; // Importing Link from Inertia
import CategoryItem from '@/Components/CategoryItem.vue'; // Import the CategoryItem component
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
  category: Object, // The current category passed to this component
  subcategories: Array, // The first-level subcategories passed to the component
});
</script>
