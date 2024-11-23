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
        </li>
      </ul>

      <!-- Reply Form -->
      <div v-if="$page.props.auth.user" class="mt-6">
        <form @submit.prevent="submitReply" class="bg-white p-5 rounded-md shadow-md border">
          <div ref="quillEditor"></div> <!-- Quill editor container -->
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
import { ref, onMounted } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css';

// Define props passed to the component
const props = defineProps({
  category: Object,
  thread: Object,
  posts: Array,
});

// Create a Laravel Precognition Vue form
const form = useForm('post', route('posts.store', {category: props.category.id, thread: props.thread.id}), {
  content: '', // The content of the post
});

// Initialize Quill Editor
const quillEditor = ref(null);
let quillInstance = null;

onMounted(() => {
  // Initialize the Quill editor inside the div container
  quillInstance = new Quill(quillEditor.value, {
    theme: 'snow',
    placeholder: 'Write your reply here...',
    modules: {
      toolbar: [
        [{ 'header': '1' }, { 'header': '2' }, { 'font': [] }],
        [{ 'list': 'ordered' }, { 'list': 'bullet' }],
        [{ 'align': [] }],
        ['bold', 'italic', 'underline'],
        [{ color: [] }],
        ['link'],
        ['image', 'video'],
        ['code-block'],
      ],
    },
  });

  // Sync Quill content with form.content
  quillInstance.on('text-change', () => {
    form.content = quillInstance.root.innerHTML;
  });
});

function submitReply() {
  // Get the content from the Quill editor
  let content = quillInstance.root.innerHTML;

  // Remove any <input> elements from the content
  content = content.replace(/<input[^>]*>/g, '');

  // Set the cleaned content to the form
  form.content = content;

  // Submit the form
  form.submit({
    preserveScroll: true,
    onSuccess: () => {
      form.reset();  // Reset form fields, including the content
      quillInstance.setContents([]);  // Clear the Quill editor content properly
    },
  });
}
</script>
