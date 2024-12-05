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
            <div class="prose max-w-full text-xs text-gray-800" v-html="post.content"></div>
          </div>
          <!-- Quote button -->
          <div v-if="$page.props.auth.user" class="flex justify-end p-4">
            <button @click="quotePost(post)" class="quote-btn text-right py-1 px-3 bg-blue-500 text-white text-sm rounded hover:bg-blue-600 focus:outline-none">
                Quote
            </button>
          </div>
        </li>
      </ul>

      <!-- Reply Form -->
      <div v-if="$page.props.auth.user" class="mt-6">
        <form ref="replyForm" @submit.prevent="submitReply" class="bg-white p-5 rounded-md shadow-md border">
          <QuillEditor v-model="form.content" />
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
import QuillEditor from '@/Components/QuillEditor.vue';
import { watch } from 'vue';
import { onMounted } from 'vue';

// Define props passed to the component
const props = defineProps({
  category: Object,
  thread: Object,
  posts: Array,
});

// Define a reference for the reply form
const replyForm = ref(null);

// Create a Laravel Precognition Vue form
const form = useForm('post', route('posts.store', {category: props.category.id, thread: props.thread.id}), {
  content: '', // The content of the post
});

watch(() => form.content, () => {
   form.validate('content');
});

function extractUserText(postContent) {
  const tempDiv = document.createElement('div');
  tempDiv.innerHTML = postContent;

  // Remove all <blockquote> elements from the content
  tempDiv.querySelectorAll('blockquote').forEach((blockquote) => {
    blockquote.remove();
  });

  return tempDiv.innerHTML; // Return the cleaned-up HTML
}

// Method to handle quote button click
function quotePost(post) {
  // Extract only the user's text (excluding quotes)
  const postContent = extractUserText(post.content);
  const postAuthor = `Posted by User ID: ${post.user_id}`; // You can customize this as needed
  const postTimestamp = `Posted on ${post.created_at}`; // You can also customize this

  // Create the quoted HTML content
  const quotedText = `<blockquote>
                        <strong>${postAuthor}</strong><br>
                        ${postTimestamp}:<br>
                        ${postContent}
                      </blockquote>`;

  // Pass quoted content to child component
  form.content += quotedText; // Add the quoted content to the form content (QuillEditor will sync)

  // Scroll to the reply form
  if (replyForm.value) {
    replyForm.value.scrollIntoView({ behavior: 'smooth' }); // Smooth scroll to the form
  }
}

function submitReply() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => {
      form.reset();  // Reset form fields, including the content
    },
  });
}

onMounted(() => {
  document.querySelectorAll('img').forEach(img => {
    img.onerror = () => (img.style.display = 'none');
  });
});
</script>
