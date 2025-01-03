<template>
    <div class="w-full mb-4 border border-gray-300 rounded-md p-5"
        :class="{'bg-blue-50': $page.props.auth.user.id === message.sender_id,
                 'bg-white': $page.props.auth.user.id != message.sender_id,}"
    >
        <div
            class="max-w-full text-xs text-gray-800 mb-4 msg"
            v-html="message.message"
        >
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    message: Object,
});
</script>
