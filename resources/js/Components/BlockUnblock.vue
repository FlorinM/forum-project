<template>
    <div class="flex justify-end">
        <!-- Block or Unblock Button -->
        <button
            v-if="!blockedStatus"
            @click="blockUser"
            class="px-3 py-1 bg-red-500 text-white font-bold rounded-md hover:bg-red-600 focus:outline-none"
        >
            Block
        </button>

        <button
            v-if="blockedStatus"
            @click="unblockUser"
            class="px-3 py-1 bg-green-500 text-white font-bold rounded-md hover:bg-green-600 focus:outline-none"
        >
            Unblock
        </button>
    </div>
</template>

<script setup>
const props = defineProps({
    blockedStatus: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits();

const blockUser = () => {
    emit('block');
};

const unblockUser = () => {
    emit('unblock');
};
</script>
