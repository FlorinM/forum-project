<template>
  <div class="w-full mb-4 bg-white border border-gray-300 rounded-md p-5">
    <div class="grid grid-cols-10 gap-4">

      <!-- Left Side: Avatar and Name (10%) -->
      <div class="col-span-1 flex flex-col items-center space-y-2">
        <Avatar :avatarUrl="post.user.avatar_url" :altText="post.user.name" />
        <p class="text-sm text-gray-800 text-center truncate">{{ post.user.name }}</p>
      </div>

      <!-- Right Side: Content and Quote Button (90%) -->
      <div class="col-span-9 flex flex-col">
        <div class="prose max-w-full text-xs text-gray-800 mb-4" v-html="post.content"></div>
        <div v-if="$page.props.auth.user" class="flex justify-end">
          <button
            @click="quotePost(post)"
            class="quote-btn text-right py-1 px-3 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 focus:outline-none"
          >
            Quote
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import Avatar from '@/Components/Avatar.vue';

const props = defineProps({
  post: Object, // Post data
  quotePost: Function, // Quote method passed from the parent
});
</script>
