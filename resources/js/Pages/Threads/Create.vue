<template>
  <div class="create-thread-container">
    <h1>Create New Thread in {{ category.name }}</h1>
    <form @submit.prevent="submitForm" class="thread-form">
      <!-- Title Input -->
      <div class="form-group">
        <label for="threadTitle">Title</label>
        <input
          id="threadTitle"
          v-model="form.title"
          @change="form.validate('title')"
          class="input-field"
          required
          placeholder="Enter thread title"
        />
      </div>

      <!-- Content Input for Thread Description -->
      <div class="form-group">
        <label for="threadDescription">Thread Description</label>
        <textarea
          id="threadDescription"
          v-model="form.content"
          @change="form.validate('content')"
          name="content"
          class="input-field"
          required
          rows="3"
          placeholder="Write a brief description of your thread"
        ></textarea>
      </div>

      <!-- Initial Post Content Input -->
      <div class="form-group">
        <label for="postContent">First Post Content</label>
        <textarea
          id="postContent"
          v-model="form.postContent"
          @change="form.validate('postContent')"
          name="postContent"
          class="input-field"
          required
          rows="5"
          placeholder="Write the content of your first post"
        ></textarea>
      </div>

      <button :disabled="form.processing" type="submit" class="btn-submit">Create Thread</button>
    </form>

    <div v-if="form.invalid('title')" class="error">{{ form.errors.title }}</div>
    <div v-if="form.invalid('content')" class="error">{{ form.errors.content }}</div>
    <div v-if="form.invalid('postContent')" class="error">{{ form.errors.postContent }}</div>
  </div>
</template>

<script setup>
import { useForm } from 'laravel-precognition-vue-inertia';

const props = defineProps({
  category: Object,
});

const form = useForm('post', route('threads.store', {categoryId: props.category.id}), {
  title: '',
  content: '',        // For thread description
  postContent: '',    // For the first post content
});

function submitForm() {
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}
</script>

<style scoped>
.create-thread-container {
  max-width: 800px;
  margin: 0 auto;
  padding: 20px;
  background-color: #f9f9f9;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

h1 {
  text-align: center;
  color: #333;
  margin-bottom: 20px;
}

.thread-form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
  align-items: center;  /* Center align the label and input */
}

.label {
  margin-bottom: 5px;
  font-weight: bold;
  color: #555;
  width: 80%;           /* Align label with the input field */
  text-align: left;     /* Align label text to the left */
}

.input-field {
  padding: 10px;
  font-size: 1rem;
  border-radius: 4px;
  border: 1px solid #ddd;
  max-width: 100%;      /* Allow 100% width but limit to max width below */
  width: 80%;           /* Set width to 80% for centered layout */
  margin: 0 auto;       /* Center the input fields */
}

.btn-submit {
  padding: 10px;
  font-size: 1rem;
  font-weight: bold;
  color: white;
  background-color: #007bff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
  margin-top: 10px;     /* Add some space above the button */
  margin: 0 auto;
  width: 100px;
}

.btn-submit:hover {
  background-color: #0056b3;
}

.error {
  color: red;
  font-size: 0.875rem;
  margin-top: 5px;
}
</style>
