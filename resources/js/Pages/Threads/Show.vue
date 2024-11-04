<!-- resources/js/Pages/Threads/Show.vue -->

<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">{{ category.name }} - Threads</h1>

      <!-- New Thread Button (only visible to authenticated users) -->
      <Link
        v-if="$page.props.auth.user"
        :href="route('threads.create', { categoryId: category.id })"
        class="inline-block mb-4 px-4 py-2 text-white bg-blue-600 rounded-md transition duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
      >
        Start New Thread
      </Link>

      <ul class="list-none p-0">
        <li
          v-for="thread in threads"
          :key="thread.id"
          class="w-full mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
        >
          <Link
            :href="route('threads.show', [category.id, thread.id])"
            class="block text-sm text-blue-600 w-full text-left p-5"
          >
            {{ thread.title }}
            <span class="block text-xs text-gray-600">Posted by User ID: {{ thread.user_id }}</span>
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
