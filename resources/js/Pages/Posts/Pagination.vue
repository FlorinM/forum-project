<template>
  <div class="flex justify-center items-center space-x-2 mt-6 mb-6">
    <!-- First Page Button -->
    <button
      :disabled="currentPage === 1"
      @click="changePage(1)"
      class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50"
    >
      First
    </button>

    <!-- Previous Page Button -->
    <button
      :disabled="currentPage === 1"
      @click="changePage(currentPage - 1)"
      class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50"
    >
      Previous
    </button>

    <!-- Dynamic Page Numbers -->
    <div v-for="page in pages" :key="page">
      <!-- Current Page -->
      <span
        v-if="page === currentPage"
        class="px-3 py-1 rounded bg-blue-500 text-white font-bold"
      >
        Page {{ page }} of {{ lastPage }}
      </span>
      <!-- Other Pages -->
      <button
        v-else
        @click="changePage(page)"
        :class="[
          'px-3 py-1 rounded',
          'bg-gray-300 hover:bg-gray-400'
        ]"
      >
        {{ page }}
      </button>
    </div>

    <!-- Next Page Button -->
    <button
      :disabled="currentPage === lastPage"
      @click="changePage(currentPage + 1)"
      class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50"
    >
      Next
    </button>

    <!-- Last Page Button -->
    <button
      :disabled="currentPage === lastPage"
      @click="changePage(lastPage)"
      class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400 disabled:opacity-50"
    >
      Last
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },
  lastPage: {
    type: Number,
    required: true,
  },
  onPageChange: {
    type: Function,
    required: true,
  },
});

// Compute the pages to display dynamically
const pages = computed(() => {
  const range = [];
  const start = Math.max(1, props.currentPage - 3); // Up to 3 pages left of the current page
  const end = Math.min(props.lastPage, props.currentPage + 3); // Up to 3 pages right of the current page

  for (let i = start; i <= end; i++) {
    range.push(i);
  }
  return range;
});

function changePage(page) {
  if (page >= 1 && page <= props.lastPage) {
    props.onPageChange(page); // Call the parent-provided function
  }
}
</script>

<style scoped>
/* Optional styling for better appearance */
button {
  transition: background-color 0.2s;
}
</style>
