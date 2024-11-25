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
            ['code-block'],
         ],
      },
   });

   // Sync Quill editor content with the modelValue
   quillInstance.on('text-change', () => {
      modelValue.value = quillInstance.root.innerHTML; // Sync to parent
   });
});

// Watch for changes in modelValue and update the Quill editor
watch(modelValue, (newValue) => {
   if (quillInstance && quillInstance.root.innerHTML !== newValue) {
      quillInstance.root.innerHTML = newValue; // Update the editor content
   }
});
</script>
