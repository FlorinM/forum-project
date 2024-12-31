<template>
    <ForumLayout>
        <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
            <FlattenedButton @click="display('inbox')">
                Inbox
            </FlattenedButton>

            <FlattenedButton @click="display('sent')">
                Sent
            </FlattenedButton>

            <div v-if="displayInbox" class="col-span-7 mb-6">
                <Inbox />
            </div>

            <div v-if="displaySent" class="col-span-7 mb-6">
                <Sent />
            </div>
        </div>
    </ForumLayout>
</template>

<script setup>
import { ref } from 'vue';
import ForumLayout from '@/Layouts/ForumLayout.vue';
import FlattenedButton from '@/Components/FlattenedButton.vue';
import Inbox from './Inbox.vue';
import Sent from './Sent.vue';

const displayInbox = ref(true);
const displaySent = ref(false);

function display (value) {
    if (value === 'inbox') {
        displayInbox.value = true;
        displaySent.value = false;
    } else if (value === 'sent') {
        displayInbox.value = false;
        displaySent.value = true;
    }
}
</script>
