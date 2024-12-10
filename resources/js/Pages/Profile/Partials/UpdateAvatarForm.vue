<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">
                Update Avatar
            </h2>

            <p class="mt-1 text-sm text-gray-600">
                Choose a profile picture to personalize your account.
            </p>
        </header>

        <form @submit.prevent="updateAvatar" class="mt-6 space-y-6">
            <!-- Avatar Preview -->
            <div class="flex items-center justify-center">
                <div class="w-24 h-24 rounded-full overflow-hidden border border-gray-300">
                    <img
                        v-if="avatarUrl"
                        :src="avatarUrl"
                        alt="User Avatar"
                        class="object-cover w-full h-full"
                    />
                    <img
                        v-else-if="hasAvatar"
                        :src="avatar_url"
                        alt="Your Avatar"
                        class="object-cover w-full h-full"
                    />
                    <img
                        v-else
                        :src="baseUrl + '/storage/avatars/default-avatar.jpg'"
                        alt="Default Avatar"
                        class="object-cover w-full h-full"
                    />
                </div>
            </div>

            <!-- File Input -->
            <div>
                <InputLabel for="avatar" value="Select Avatar" />

                <input
                    type="file"
                    id="avatar"
                    accept="image/*"
                    @change="handleFileChange"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.avatar" class="mt-2" />
            </div>

            <!-- Remove Avatar Option -->
            <div v-if="hasAvatar">
                <p class="text-sm text-gray-600 mt-2">
                    If you wish, you can remove your avatar.
                </p>
                <PrimaryButton @click.prevent="removeAvatar">Remove Avatar</PrimaryButton>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm text-gray-600"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from 'laravel-precognition-vue-inertia';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { usePage } from '@inertiajs/vue3'

const props = defineProps ({
    avatar: {
        type: [null, String],
        default: null,
    },
});

const baseUrl = ref(usePage().props.baseUrl);
const hasAvatar = ref(props.avatar != null ? true : false);
const avatar_url = ref(baseUrl.value + '/storage/' + props.avatar);

// Form for avatar handling
const form = useForm('post', route('avatar.update'), {
    avatar: null,
});

const avatarUrl = ref(null); // For storing the avatar URL
const avatarFile = ref(null); // For storing the file to upload

// Handle file input change
const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        avatarFile.value = file;
        avatarUrl.value = URL.createObjectURL(file); // Preview the selected file
    }
};

// Handle avatar update
const updateAvatar = () => {
    if (avatarFile.value) {
        form.avatar = avatarFile.value; // Directly assign the file to the form object
    }

    form.post(route('avatar.update'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('avatar');
            avatarUrl.value = null; // Clear preview after saving
            window.location.reload(); // Reload the parent page with fresh props
        },
        onError: () => {
            if (form.errors.avatar) {
                avatarFile.value = null;
                avatarUrl.value = null;
            }
        },
    });
};

// Remove avatar functionality
const removeAvatar = () => {
    form.delete(route('avatar.delete'), {
        onSuccess: () => {
            avatarUrl.value = null; // Reset avatar URL after removal
            hasAvatar.value = false;
            form.reset('avatar');
        },
    });
};
</script>
