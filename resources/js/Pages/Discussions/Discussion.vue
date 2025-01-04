<template>
    <ForumLayout>
        <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">

            <div class="flex items-center justify-center space-x-4 mb-6">
                <!-- Initiator's avatar -->
                <Avatar :avatarUrl="discussion.initiator.avatar_url" :altText="discussion.initiator.name" />

                <!-- Discussion subject -->
                <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ discussion.subject }}</h1>

                <!-- Participant's avatar -->
                <Avatar :avatarUrl="discussion.participant.avatar_url" :altText="discussion.participant.name" />
            </div>

            <!-- Messages List -->
            <div class="list-none p-0">
                <Message
                    v-for="message in refMessages"
                    :key="message.id"
                    :message="message"
                />
            </div>

            <!-- Reply Form -->
            <div class="mt-6">
                <form ref="replyForm" @submit.prevent="submitReply" class="bg-white p-5 rounded-md shadow-md border">
                    <QuillEditor v-model="form.message" />
                    <div class="mt-4 text-right">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-6 py-2 bg-blue-500 text-white font-bold rounded-md hover:bg-blue-600 focus:outline-none"
                        >
                            Send Message
                        </button>
                    </div>
                </form>
                <div v-if="form.invalid('receiver_id')" class="text-red-500 text-sm mt-2">{{ form.errors.receiver_id }}</div>
                <div v-if="form.invalid('discussion_id')" class="text-red-500 text-sm mt-2">{{ form.errors.discussion_id }}</div>
                <div v-if="form.invalid('message')" class="text-red-500 text-sm mt-2">{{ form.errors.message }}</div>
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Message from './Message.vue';
import Avatar from '@/Components/Avatar.vue';
import QuillEditor from '@/Components/QuillEditor.vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import { usePage } from '@inertiajs/vue3';
import { watch, ref } from 'vue';
import Echo from '@/echo';

const props = defineProps({
    messages: {
        type: Array,
    },
    discussion: {
        type: Object,
    },
});

// Make `messages` reactive for broadcasting implementation
const refMessages = ref(props.messages);

// The receiver of the message is always the other interlocutor not the auth user
const receiverId = usePage().props.auth.user.id === props.discussion.initiator_id ?
                   props.discussion.participant_id :
                   props.discussion.initiator_id;

// Create a Laravel Precognition Vue form
const form = useForm('post', route('send.message'), {
    receiver_id: receiverId,
    discussion_id: props.discussion.id,
    message: '', // The content of the message
});

watch(() => form.message, () => {
   form.validate('message');
});

function submitReply() {
  form.submit({
    preserveScroll: true,
    onSuccess: () => {
      form.reset();  // Reset form fields, including the content
    },
  });
}

// Listen to the private discussion channel
Echo.private(`discussion.${props.discussion.id}`)
    .listen('.MessageCreated', (event) => {
        refMessages.value.push(event.model);  // Add the new message to the UI
    })
    .error((error) => {
        console.error('Subscription error:', error);
    });
</script>
