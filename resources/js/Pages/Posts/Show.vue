<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">{{ thread.title }}</h1>

      <ul class="list-none p-0">
        <li
          v-for="post in posts"
          :key="post.id"
          class="w-full mb-4 bg-white border border-gray-300 rounded-md"
        >
          <div class="block text-left p-5">
            <p class="text-xs text-gray-600">Posted by User ID: {{ post.user_id }}</p>
            <div class="text-xs text-gray-800">{{ post.content }}</div>
          </div>
        </li>
      </ul>
    </div>
  </ForumLayout>
</template>

<script setup>
import { defineProps } from 'vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';

const props = defineProps({
  category: Object, // The selected category
  thread: Object,   // The selected thread
  posts: Array,     // Posts associated with this category/thread
});
</script>
