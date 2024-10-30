
<!-- resources/js/Pages/Home/Home.vue -->

<template>
<ForumLayout>
  <div class="container">
    <h1 class="heading">Categories</h1>
    <ul class="category-list">
      <li
        v-for="category in categories"
        :key="category.id"
        class="category-item"
      >
        <Link
          :href="route('categories.show', category.id)"
        >
          {{ category.name }} (Created by User ID: {{ category.user_id }})
        </Link>
      </li>
    </ul>
  </div>
</ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3'; // Importing Link from Inertia
import ForumLayout from '../../Layouts/ForumLayout.vue';

const props = defineProps({
  categories: Array, // Expecting an array of categories
});
</script>

<style scoped>
.container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.heading {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
  font-size: 2rem;
  font-weight: bold;
}

.category-list {
  list-style: none;
  padding: 0;
}

.category-item {
  padding: 15px;
  margin: 10px 0;
  background-color: white;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.category-item:hover {
  background-color: #f0f0f0; /* Light gray background on hover */
}
</style>



