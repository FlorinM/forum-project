<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <!-- Thread Title -->
      <h1 class="text-center text-4xl font-bold text-gray-800 mb-6">{{ thread.title }}</h1>

        <!-- Pagination top -->
        <Pagination
            :currentPage="posts.current_page"
            :lastPage="posts.last_page"
            :onPageChange="handlePageChange"
        />

      <!-- Posts List -->
      <div class="list-none p-0">
        <Post
          v-for="post in posts.data"
          :key="post.id"
          :post="post"
          :quotePost="quotePost"
          :id="'post-' + post.id"
        />
      </div>

        <!-- Pagination bottom -->
        <Pagination
            :currentPage="posts.current_page"
            :lastPage="posts.last_page"
            :onPageChange="handlePageChange"
        />

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
import Post from './Post.vue';
import Pagination from './Pagination.vue';
import { watch } from 'vue';
import { onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { nextTick } from 'vue';

// Define props passed to the component
const props = defineProps({
  category: Object,
  thread: Object,
  posts: Object,
  targetPostId: {
    type: [Number, null],
    default: null,
  },
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
    nextTick(() => {
        // Scroll to the specific post if targetPostId is present
        if (props.targetPostId) {
            const targetElement = document.querySelector('#post-' + props.targetPostId);
            if (targetElement) {
                targetElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });

    document.querySelectorAll('img').forEach(img => {
        img.onerror = () => (img.style.display = 'none');
    });
});

function handlePageChange(page) {
  router.get(
    route('threads.show', {
      category: props.category.id,
      thread: props.thread.id,
      page: page, // The page number you want to load
    }),
    {}, // You can add extra data if needed
    { preserveScroll: false } // If true, the scroll position is preserved
  );
}
</script>
