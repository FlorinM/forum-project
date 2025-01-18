<template>
  <ForumLayout>
    <div class="max-w-3xl mx-auto p-6 bg-gray-50 rounded-lg shadow-lg">
      <h1 class="text-center text-2xl font-bold text-gray-800 mb-6">Create New Thread in {{ category.name }}</h1>

        <!-- Display Ban Message if Present -->
        <div v-if="$page.props.flash.banMessage" class="text-red-500 text-sm font-semibold mb-4">
            {{ $page.props.flash.banMessage }}
        </div>

      <form @submit.prevent="submitForm" class="flex flex-col gap-4">
        <!-- Title Input -->
        <div class="flex flex-col items-center">
          <label for="threadTitle" class="text-gray-700 font-semibold mb-2 w-4/5 text-left">Title</label>
          <input
            id="threadTitle"
            v-model="form.title"
            @input="form.validate('title')"
            class="w-4/5 p-3 text-base border border-gray-300 rounded-md"
            required
            placeholder="Enter thread title"
          />
        </div>

        <!-- Content Input for Thread Description -->
        <div class="flex flex-col items-center">
          <label for="threadDescription" class="text-gray-700 font-semibold mb-2 w-4/5 text-left">Thread Description</label>
          <textarea
            id="threadDescription"
            v-model="form.content"
            @change="form.validate('content')"
            name="content"
            class="w-4/5 p-3 text-base border border-gray-300 rounded-md"
            required
            rows="3"
            placeholder="Write a brief description of your thread"
          ></textarea>
        </div>

        <!-- Initial Post Content Input -->
        <div class="flex flex-col items-center">
          <label for="postContent" class="text-gray-700 font-semibold mb-2 w-4/5 text-left">First Post Content</label>
          <QuillEditor id="postContent" v-model="form.postContent"/>
        </div>

        <button :disabled="form.processing" type="submit" class="w-24 mx-auto p-3 font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 transition-colors mt-4">
          Create Thread
        </button>
      </form>

      <div v-if="form.invalid('title')" class="text-red-500 text-sm mt-2">{{ form.errors.title }}</div>
      <div v-if="form.invalid('content')" class="text-red-500 text-sm mt-2">{{ form.errors.content }}</div>
      <div v-if="form.invalid('postContent')" class="text-red-500 text-sm mt-2">{{ form.errors.postContent }}</div>
    </div>
  </ForumLayout>
</template>

<script setup>
import { useForm } from 'laravel-precognition-vue-inertia';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import QuillEditor from '@/Components/QuillEditor.vue';
import { watch } from 'vue';

const props = defineProps({
  category: Object,
});

const form = useForm('post', route('threads.store', {category: props.category.id}), {
  title: '',
  content: '',        // For thread description
  postContent: '',    // For the first post content
});

watch(() => form.postContent, () => {
   form.validate('postContent');
});

function submitForm() {
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>
