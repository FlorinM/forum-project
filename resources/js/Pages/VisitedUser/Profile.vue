<template>
    <ForumLayout>
        <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
            <!-- Profile Header -->
            <div class="grid grid-cols-10 gap-4 mb-8">
                <div class="col-span-1 flex items-center justify-center">
                    <Avatar :avatarUrl="user.avatar_url" :altText="user.name" />
                </div>
                <div class="col-span-9">
                    <h1 class="text-xl font-bold text-gray-800 inline-block">
                        {{ user.nickname }}
                    </h1>

                    <FlattenedButton
                        v-if="
                            $page.props.auth.user.id != user.id &&
                            !isMessageFormVisible
                        "
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
                        <FlattenedButton @click="display('inbox')">
                            Inbox
                        </FlattenedButton>

                        <FlattenedButton @click="display('sent')">
                            Sent
                        </FlattenedButton>

                        <FlattenedButton>
                            <Link :href="route('profile.edit')">
                                Edit Profile
                            </Link>
                        </FlattenedButton>
                    </template>

                    <div class="text-sm text-gray-600 mt-4">
                        <div v-if="user.description">
                            {{ user.description }}
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-10">
                <!-- User Details -->
                <div
                    class="bg-white p-6 rounded-md shadow-md mb-6 col-span-3 mr-4"
                >
                    <h2 class="font-semibold text-gray-800 mb-4">
                        {{ props.user.name }}
                    </h2>
                    <div class="grid grid-cols-1 text-gray-600">
                        <table>
                            <tbody>
                                <tr v-for="(info, index) in infos">
                                    <td>{{ info.name }}:</td>
                                    <td>
                                        <strong v-if="info.value">{{ info.value }}</strong>
                                        <strong v-else>Unknown</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Signature:</td>
                                    <td>
                                        <div class="text-xs italic">
                                            {{ props.user.signature }}
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-if="show.threads" class="col-span-7 mb-6">
                    <DisplayUserThreads :userId="props.user.id" />
                </div>

                <div v-if="show.posts" class="col-span-7 mb-6">
                    <DisplayUserPosts :userId="props.user.id" />
                </div>

                <div v-if="show.inbox" class="col-span-7 mb-6">
                    <Inbox />
                </div>

                <div v-if="show.sent" class="col-span-7 mb-6">
                    <Sent />
                </div>
            </div>

            <!-- Display Ban Message if Present -->
            <div
                v-if="$page.props.flash.banMessage"
                class="text-red-500 text-sm font-semibold mb-4"
            >
                {{ $page.props.flash.banMessage }}
            </div>

            <!-- Display Error Send Message if Present -->
            <div
                v-if="$page.props.flash.errorSendMessage"
                class="text-red-500 text-sm font-semibold mb-4"
            >
                {{ $page.props.flash.errorSendMessage }}
            </div>

            <!-- Form with QuillEditor -->
            <div v-if="isMessageFormVisible">
                <form
                    @submit.prevent="submitReply"
                    class="bg-white p-5 rounded-md shadow-md border"
                >
                    <!-- Subject Input -->
                    <div class="flex flex-col mb-5">
                        <label
                            for="subject"
                            class="text-gray-700 font-semibold mb-2 w-4/5 text-left"
                            >Subject</label
                        >
                        <input
                            id="subject"
                            v-model="form.subject"
                            @input="form.validate('subject')"
                            class="w-4/5 p-3 text-base border border-gray-300 rounded-md"
                            required
                            placeholder="Enter thread subject"
                        />
                    </div>
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
                <div
                    v-if="form.invalid('subject')"
                    class="text-red-500 text-sm mt-2"
                >
                    {{ form.errors.subject }}
                </div>
                <div
                    v-if="form.invalid('message')"
                    class="text-red-500 text-sm mt-2"
                >
                    {{ form.errors.message }}
                </div>
                <div
                    v-if="form.invalid('receiver_id')"
                    class="text-red-500 text-sm mt-2"
                >
                    {{ form.errors.receiver_id }}
                </div>
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
import Inbox from '@/Pages/Discussions/Inbox.vue';
import Sent from '@/Pages/Discussions/Sent.vue';
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

const age = props.user.birthday ? calculateAge(props.user.birthday) : null;
const birthday = props.user.birthday
    ? extractDayAndMonth(props.user.birthday)
    : null;
const infos = [
    { name: 'Age', value: age },
    { name: 'Gender', value: props.user.gender },
    { name: 'Birthday', value: birthday },
    { name: 'Posts', value: props.totalPosts },
    { name: 'Threads', value: props.totalThreads },
];

// Create a Laravel Precognition Vue form
const form = useForm('post', route('discussions.start'), {
    receiver_id: props.user.id,
    subject: '', // The subject of the discussion
    message: '', // The content of the message
});

watch(
    () => form.message,
    () => {
        form.validate('message');
    },
);

function submitReply() {
    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            form.reset(); // Reset form fields, including the content

            // The message form disappears and the send message button reappears
            isMessageFormVisible.value = false;
        },
    });
}

// If the message form is visible the Send Message button
// is hidden and vice versa
const isMessageFormVisible = ref(false);

function openMessageForm() {
    isMessageFormVisible.value = true;

    // Smooth scroll to the message form
    window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
}

// Display Threads, Posts, Inbox or Sent hidden components
const show = ref({
    threads: false,
    posts: false,
    inbox: false,
    sent: false,
});

function display(value) {
    if (
        value != 'threads' &&
        value != 'posts' &&
        value != 'inbox' &&
        value != 'sent'
    ) {
        return;
    }

    for (const key in show.value) {
        if (value === key) {
            show.value[key] = true;
        } else {
            show.value[key] = false;
        }
    }
}
</script>
