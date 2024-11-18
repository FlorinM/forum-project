<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <!-- Thread Title -->
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">{{ thread.title }}</h1>

      <!-- Posts List -->
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

      <!-- Reply Form -->
      <div v-if="$page.props.auth.user" class="mt-6">
        <form @submit.prevent="submitReply" class="bg-white p-5 rounded-md shadow-md border">
          <textarea
            v-model="form.content"
            @change="form.validate('content')"
            required
            placeholder="Write your reply here..."
            class="w-full h-24 p-3 border border-gray-300 rounded-md focus:outline-none focus:ring focus:ring-blue-200"
          ></textarea>
          <div class="mt-4 text-right">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600 focus:outline-none"
            >
              Post Reply
            </button>
          </div>
        </form>
        <div v-if="form.invalid('content')" class="text-red-500 text-sm mt-2">{{ form.errors.content }}</div>
      </div>
    </div>
  </ForumLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import ForumLayout from '@/Layouts/ForumLayout.vue';

// Define props passed to the component
const props = defineProps({
  category: Object,
  thread: Object,
  posts: Array,
});

// Create a Laravel Precognition Vue form
const form = useForm('post', route('posts.store', {category: props.category.id, thread: props.thread.id}), {
  content: '', // The contend of the post
});

function submitReply() {
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>
