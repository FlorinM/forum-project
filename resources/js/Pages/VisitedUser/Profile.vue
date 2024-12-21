<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
        <!-- Profile Header -->
        <div class="grid grid-cols-10 gap-4 mb-8">
            <div class="col-span-1 flex items-center justify-center">
                <Avatar :avatarUrl="user.avatar_url" :altText="user.name"/>
            </div>
            <div class="col-span-9">
                <h1 class="text-xl font-bold text-gray-800 inline-block">{{ user.nickname }}</h1>

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
                    <div v-if="user.description">{{ user.description }}</div>
                </p>
            </div>
        </div>

        <div class="grid grid-cols-10">
            <!-- User Details -->
            <div class="bg-white p-6 rounded-md shadow-md mb-6 col-span-3 mr-4">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Info: </h2>
                <div class="grid grid-cols-1 text-gray-600">
                    <table>
                        <tr v-for="(info, index) in infos">
                            <td>{{ info.name }}:</td>
                            <td><strong v-if="info.value">{{ info.value }}</strong><strong v-else>Unknown</strong></td>
                        </tr>
                        <tr>
                            <td>Signature:</td>
                            <td><div class="text-xs italic">{{ props.user.signature }}</div></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div v-if="displayThreads" class="col-span-7 mb-6">
                <DisplayUserThreads :userId="props.user.id" />
            </div>

            <div v-if="displayPosts" class="col-span-7 mb-6">
                <DisplayUserPosts :userId="props.user.id" />
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
import DisplayUserPosts from '@/Components/DisplayUserPosts.vue';
import { extractDayAndMonth, calculateAge } from '@/Utils/dateUtils';

const props = defineProps({
    user: {
        type: Object, // Expecting the full visited user data (name, avatar, bio, etc.)
        required: true,
    },
    totalPosts: {
        type: Number,
        default: 0,
    },
    totalThreads: {
        type: Number,
        default: 0,
    },
});

const routes = [
    {id: 1, name: 'Inbox', link: 'discussions.inbox'}, // Change with profile.inbox when you have route
    {id: 2, name: 'Edit my Profile', link: 'profile.edit'},
];

const age = props.user.birthday ? calculateAge(props.user.birthday) : null;
const birthday = props.user.birthday ? extractDayAndMonth(props.user.birthday) : null;
const infos = [
    {name: 'Age', value: age},
    {name: 'Gender', value: props.user.gender},
    {name: 'Birthday', value: birthday},
    {name: 'Posts', value: props.totalPosts},
    {name: 'Threads', value: props.totalThreads},
];

const displayThreads = ref(false);
const displayPosts = ref(false);

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
