<template>
   <div>
      <!-- Quill editor container -->
      <div ref="quillEditor" class="min-h-[200px] border border-gray-300 rounded-lg bg-white"></div>
   </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import Quill from 'quill';
import 'quill/dist/quill.snow.css'; // Import the desired Quill theme CSS

// Define a v-model-compatible prop for two-way data binding
const modelValue = defineModel({
   type: String,
   required: true,
});

// References for the editor DOM element and the Quill instance
const quillEditor = ref(null);
let quillInstance = null;

// Lifecycle hook to initialize Quill
onMounted(() => {
   // Initialize Quill editor
   quillInstance = new Quill(quillEditor.value, {
      theme: 'snow', // Change to 'bubble' if desired
      placeholder: 'Write something amazing...',
      modules: {
         toolbar: [
            [{ header: '1' }, { header: '2' }, { font: [] }],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ align: [] }],
            ['bold', 'italic', 'underline'],
            [{ color: [] }, { background: [] }],
            ['link', 'image', 'video'],
                // If 'image' is included in the toolbar, ensure that
                // $useDefaultQuillImageHandler is set to true in PostController
                // to prevent base64-encoded image data from polluting posts.
            ['code-block'],
         ],
      },
   });

   // Sync Quill editor content with the modelValue
   quillInstance.on('text-change', () => {
      modelValue.value = quillInstance.root.innerHTML; // Sync to parent
   });
});

// Watch for changes in modelValue and update the Quill editor content
watch(modelValue, (newValue) => {
  if (quillInstance && newValue !== quillInstance.root.innerHTML) {
    // Check if quoted content exists in the newValue
    if (newValue.includes('<blockquote>')) {
      // If quoted content exists, insert it using dangerouslyPasteHTML
      quillInstance.clipboard.dangerouslyPasteHTML(newValue);
    } else {
      // Otherwise, just set the HTML content normally
      quillInstance.root.innerHTML = newValue;
    }
  }
});
</script>
