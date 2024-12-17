<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
        <!-- Profile Header -->
        <div class="grid grid-cols-10 gap-4 mb-8">
            <div class="col-span-1 flex items-center justify-center">
                <Avatar :avatarUrl="user.avatar_url" :altText="user.name"/>
            </div>
            <div class="col-span-9">
                <h1 class="text-xl font-bold text-gray-800 inline-block">{{ user.name }}</h1>

                <FlattenedButton
                    v-if="$page.props.auth.user.id != user.id && !isMessageFormVisible"
                    @click="openMessageForm"
                >
                    Send Message
                </FlattenedButton>

                <FlattenedButton @click="display('threads')">
                    Threads
                </FlattenedButton>

                <FlattenedButton @click="display('posts')">
                    Posts
                </FlattenedButton>

                <template v-if="$page.props.auth.user.id === user.id">
                    <FlattenedButton v-for="rt in routes" :key="rt.id">
                        <Link :href="route(rt.link)">
                            {{ rt.name }}
                        </Link>
                    </FlattenedButton>
                </template>

                <p class="text-sm text-gray-600 mt-4">
                    {{ user.name }} is a 20-year-old woman with a vibrant personality and a deep passion for learning. Born and raised in a small town, she has always been curious and driven, eager to explore the world beyond her immediate surroundings. Jane is currently a student, pursuing a degree in environmental science, with a strong interest in sustainability and conservation. Her goal is to make a positive impact on the environment by contributing to innovative solutions for climate change and resource management.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-10">
            <!-- User Details -->
            <div class="bg-white p-6 rounded-md shadow-md mb-6 col-span-3 mr-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Info: </h2>
                <div class="grid grid-cols-1 text-gray-600">
                    <div><strong>Age:</strong> 20</div>
                    <div><strong>Sex:</strong> F</div>
                    <div><strong>Birthday:</strong> 23</div>
                    <div><strong>Posts:</strong> 50</div>
                    <div><strong>Threads Started:</strong> 9</div>
                </div>
            </div>

            <div class="col-span-7 mb-6">
                <DisplayUserThreads v-if="displayThreads" :userId="props.user.id" />
            </div>
        </div>

      <!-- Form with QuillEditor -->
      <div v-if="isMessageFormVisible">
        <form @submit.prevent="submitReply" class="bg-white p-5 rounded-md shadow-md border">
          <QuillEditor v-model="form.message" />
          <div class="mt-4 text-right">
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600 focus:outline-none"
            >
              Submit
            </button>
          </div>
        </form>
        <div v-if="form.invalid('message')" class="text-red-500 text-sm mt-2">{{ form.errors.message }}</div>
        <div v-if="form.invalid('receiver_id')" class="text-red-500 text-sm mt-2">{{ form.errors.receiver_id }}</div>
    </div>

    </div>
  </ForumLayout>
</template>

<script setup>
import { ref, watch } from 'vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Avatar from '@/Components/Avatar.vue';
import QuillEditor from '@/Components/QuillEditor.vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import { Link } from '@inertiajs/vue3';
import FlattenedButton from '@/Components/FlattenedButton.vue';
import DisplayUserThreads from '@/Components/DisplayUserThreads.vue';

const routes = [
    {id: 1, name: 'Inbox', link: 'profile.edit'}, // Change with profile.inbox when you have route
    {id: 2, name: 'Edit my Profile', link: 'profile.edit'},
];

const displayThreads = ref(false);
const displayPosts = ref(false);

const props = defineProps({
  user: Object,  // Expecting the full visited user data (name, avatar, bio, etc.)
});

// Create a Laravel Precognition Vue form
const form = useForm('post', route('message.send'), {
    receiver_id: props.user.id,
    message: '', // The content of the post
});

watch(() => form.message, () => {
   form.validate('message');
});

function submitReply() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => {
      form.reset();  // Reset form fields, including the content

      // The message form disappears and the send message button reappears
      isMessageFormVisible.value = false;
    },
  });
}

// If the message form is visible the Send Message button
// is hidden and vice versa
const isMessageFormVisible = ref(false);

function openMessageForm () {
    isMessageFormVisible.value = true;

    // Smooth scroll to the message form
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
};

function display (value) {
    if (value === 'threads') {
        displayThreads.value = true;
        displayPosts.value = false;
    } else if (value === 'posts') {
        displayThreads.value = false;
        displayPosts.value = true;
    }
}
</script>
