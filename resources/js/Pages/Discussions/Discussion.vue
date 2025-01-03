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
                    v-for="message in messages"
                    :key="message.id"
                    :message="message"
                />
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import ForumLayout from '@/Layouts/ForumLayout.vue';
import Message from './Message.vue';
import Avatar from '@/Components/Avatar.vue';

const props = defineProps({
    messages: {
        type: Object,
    },
    discussion: {
        type: Object,
    },
});
</script>
