<template>
  <li class="w-full mb-4 bg-white border border-gray-300 rounded-md">
    <div class="block text-left p-5">
      <!-- Avatar Component -->
      <div class="flex items-center space-x-2">
        <Avatar :avatarUrl="post.user.avatar_url" :altText="post.user.name" />
        <p class="text-sm text-gray-800">{{ post.user.name }}</p>
      </div>
      <div class="prose max-w-full text-xs text-gray-800" v-html="post.content"></div>
    </div>
    <!-- Quote button -->
    <div v-if="$page.props.auth.user" class="flex justify-end p-4">
      <button
        @click="quotePost(post)"
        class="quote-btn text-right py-1 px-3 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 focus:outline-none"
      >
        Quote
      </button>
    </div>
  </li>
</template>

<script setup>
import Avatar from '@/Components/Avatar.vue';

const props = defineProps({
  post: Object, // Post data
  quotePost: Function, // Quote method passed from the parent
});
</script>
