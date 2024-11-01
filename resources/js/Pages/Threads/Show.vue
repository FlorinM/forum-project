<!-- resources/js/Pages/Threads/Show.vue -->

<template>
<ForumLayout>
  <div class="container">
    <h1 class="heading">{{ category.name }} - Threads</h1>
    <ul class="thread-list">
      <li
        v-for="thread in threads"
        :key="thread.id"
        class="thread-item"
      >
        <Link :href="route('threads.show', [category.id, thread.id])">
            <h2>{{ thread.title }}</h2>
            <p>Posted by User ID: {{ thread.user_id }}</p>
        </Link>
      </li>
    </ul>
  </div>
</ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
  category: Object, // The selected category
  threads: Array,   // The threads under the selected category
});
</script>

<style scoped>
/* Add your styles here for the thread list */
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

.thread-list {
  list-style: none;
  padding: 0;
}

.thread-item {
  padding: 15px;
  margin: 10px 0;
  background-color: white;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  transition: background-color 0.2s;
}

.thread-item:hover {
  background-color: #f0f0f0; /* Light gray background on hover */
}
</style>
