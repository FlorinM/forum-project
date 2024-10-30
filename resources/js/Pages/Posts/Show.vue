<!-- resources/js/Pages/Posts/Show.vue -->

<template>
<ForumLayout>
  <div class="container">
    <h1 class="heading">{{ thread.title }}</h1>
    <ul class="post-list">
      <li
        v-for="post in posts"
        :key="post.id"
        class="post-item"
      >
        <p>Posted by User ID: {{ post.user_id }}</p>
        <p>{{ post.content }}</p>
      </li>
    </ul>
  </div>
</ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import ForumLayout from '../../Layouts/ForumLayout.vue';

const props = defineProps({
  category: Object, // The selected category
  thread: Object,   // The selected thread
  posts: Array, // Posts associated with this category/thread
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

.post-list {
  list-style: none;
  padding: 0;
}

.post-item {
  padding: 15px;
  margin: 10px 0;
  background-color: white;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
}

p {font-size: 0.8rem;}
</style>
