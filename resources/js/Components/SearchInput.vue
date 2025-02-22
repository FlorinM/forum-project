<template>
    <div>
        <!-- Search Input and Button -->
        <div
            class="flex items-center border border-gray-300 rounded-lg p-0 bg-blue-100 shadow-sm"
        >
            <input
                v-model="form.query"
                @keyup.enter="submitSearch"
                type="text"
                class="p-1 w-40 md:w-64 text-black border-none rounded-lg focus:outline-none"
                placeholder="Search..."
            />
            <button
                @click="submitSearch"
                class="ml-2 bg-blue-800 text-white p-1 rounded-lg hover:bg-blue-600 focus:outline-none"
                aria-label="Search"
            >
                <!-- Magnifying Glass Icon -->
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    class="h-5 w-5"
                >
                    <circle
                        cx="10"
                        cy="10"
                        r="7"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                    <line
                        x1="16"
                        y1="16"
                        x2="21"
                        y2="21"
                        stroke="currentColor"
                        stroke-width="2"
                    />
                </svg>
            </button>
        </div>

        <!-- Modal for displaying errors -->
        <Modal :show="showModal" @close="closeModal">
            <template #default>
                <div class="p-6 bg-blue-50">
                    <h2 class="text-xl font-semibold text-red-500">Validation Error</h2>
                    <div>
                        {{ form.errors.query }}
                    </div>
                </div>
            </template>
        </Modal>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import Modal from './Modal.vue'; // Import your Modal component

// Create the form object with the query field
const form = useForm('post', route('search'), {
    query: '',
});

// Local state for controlling modal visibility
const showModal = ref(false);

// Submit the form and show the modal if there are validation errors
function submitSearch() {
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            // Show the modal when there are validation errors
            showModal.value = true;
        },
    });
}

// Close the modal
function closeModal() {
    showModal.value = false;
}
</script>
