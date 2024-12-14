<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
        <!-- Profile Header -->
        <div class="grid grid-cols-10 gap-4 mb-8">
            <div class="col-span-1 flex items-center justify-center">
                <Avatar :avatarUrl="user.avatar_url" :altText="user.name"/>
            </div>
            <div class="col-span-9">
                <h1 class="text-xl font-bold text-gray-800">{{ user.name }}</h1>
                <p class="text-sm text-gray-600 mt-4">
                    {{ user.name }} is a 20-year-old woman with a vibrant personality and a deep passion for learning. Born and raised in a small town, she has always been curious and driven, eager to explore the world beyond her immediate surroundings. Jane is currently a student, pursuing a degree in environmental science, with a strong interest in sustainability and conservation. Her goal is to make a positive impact on the environment by contributing to innovative solutions for climate change and resource management.
                </p>
            </div>
        </div>

      <!-- User Details -->
      <div class="bg-white p-6 rounded-md shadow-md mb-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">User Info</h2>
        <div class="grid grid-cols-1 text-gray-600">
          <div><strong>Age:</strong> 20</div>
          <div><strong>Sex:</strong> F</div>
          <div><strong>Birthday:</strong> 23</div>
          <div><strong>Posts:</strong> 50</div>
          <div><strong>Threads Started:</strong> 9</div>
        </div>
      </div>

      <!-- Send Message Button -->
      <div class="text-center mt-8">
        <button
          @click="showMessageForm"
          class="px-3 py-1 text-m font-semibold text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
          Send Message
        </button>
      </div>

    </div>
  </ForumLayout>
</template>

<script setup>
import { ref } from 'vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Avatar from '@/Components/Avatar.vue';

const props = defineProps({
  user: Object,  // Expecting the full user data (name, avatar, bio, etc.)
});

const isMessageFormVisible = ref(false);

const showMessageForm = () => {
  isMessageFormVisible.value = true;
};

const closeMessageForm = () => {
  isMessageFormVisible.value = false;
};
</script>
