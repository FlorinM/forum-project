<!-- resources/js/Pages/Threads/Show.vue -->

<template>
<ForumLayout>
  <div class="container">
    <h1 class="heading">{{ category.name }} - Threads</h1>

    <!-- New Thread Button (only visible to authenticated users) -->
    <Link v-if="$page.props.auth.user" :href="route('threads.create', { categoryId: category.id })" class="btn-new-thread">
      Start New Thread
    </Link>

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

.btn-new-thread {
  display: inline-block; /* Allows margin and padding to take effect */
  padding: 10px 20px; /* Top/bottom and left/right padding */
  font-size: 16px; /* Font size */
  font-weight: bold; /* Bold text */
  color: white; /* Text color */
  background-color: #007bff; /* Button background color */
  border: none; /* Remove default border */
  border-radius: 5px; /* Rounded corners */
  text-align: center; /* Center text */
  text-decoration: none; /* Remove underline */
  transition: background-color 0.3s ease, transform 0.2s ease; /* Animation for hover effects */
}

.btn-new-thread:hover {
  background-color: #0056b3; /* Darker blue on hover */
  transform: translateY(-2px); /* Slight lift on hover */
}

.btn-new-thread:focus {
  outline: none; /* Remove default focus outline */
  box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.5); /* Add a shadow on focus */
}
</style>
