<template>
    <div class="max-w-screen-xl mx-auto min-h-screen bg-blue-50">
        <nav
            class="fixed flex w-full max-w-screen-xl mx-auto justify-between items-center bg-blue-600 text-white shadow-md p-4 rounded-md z-20"
        >
            <div class="flex items-center">
                <h1 class="text-lg font-bold"></h1>
            </div>

            <div class="flex items-center">
                <div class="mr-4">
                    <SearchInput />
                </div>

                <div v-if="$page.props.auth.user" class="mr-4">
                    <ContentIFollowButton />
                </div>

                <div v-if="$page.props.auth.user" class="mr-3">
                    <Notifications />
                </div>

                <!-- Authenticated User Dropdown -->
                <div v-if="$page.props.auth.user" class="relative">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center rounded-md border border-transparent bg-blue-800 px-4 py-2 text-sm font-medium text-white transition duration-150 ease-in-out hover:bg-blue-700 focus:outline-none"
                            >
                                {{ $page.props.auth.user.name }}
                                <svg
                                    class="-mr-1 ml-2 h-5 w-5"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </button>
                        </template>

                        <template #content>
                            <DropdownLink
                                :href="route('profile.edit')"
                                class="dropdown_link text-gray-700 border-b border-gray-300"
                            >
                                Edit Profile
                            </DropdownLink>
                            <DropdownLink
                                :href="
                                    route(
                                        'visited.user.show',
                                        $page.props.auth.user.id,
                                    )
                                "
                                class="dropdown_link text-gray-700 border-b border-gray-300"
                            >
                                Profile
                            </DropdownLink>
                            <template v-if="isAdminOrModerator">
                                <DropdownLink
                                    @click.prevent="goToAdminPanel"
                                    class="dropdown_link text-gray-700 cursor-pointer border-b border-gray-300"
                                >
                                    Admin Panel
                                </DropdownLink>
                            </template>
                            <DropdownLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                                class="dropdown_link text-gray-700 border-b border-gray-300"
                            >
                                Log Out
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>

                <!-- Guest Links for Login and Register -->
                <template v-else>
                    <div class="flex space-x-4">
                        <Link
                            :href="route('login')"
                            class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded-md transition"
                            >Log in</Link
                        >
                        <Link
                            v-if="$page.props.canRegister"
                            :href="route('register')"
                            class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded-md transition"
                            >Register</Link
                        >
                    </div>
                </template>
            </div>
        </nav>

        <div
            class="left-0 w-full h-[350px] bg-gradient-to-b from-blue-600 to-blue-50 rounded-t-md z-10"
        >
            <NewTopics />

            <div class="pt-20 max-w-[1000px] mx-auto">
                <Breadcrumbs></Breadcrumbs>
            </div>
        </div>

        <!-- Slot for Page-Specific Content -->
        <div class="py-6">
            <slot />
        </div>

        <div class="pt-10 max-w-[1000px] mx-auto">
            <Breadcrumbs></Breadcrumbs>
            <div class="text-xs mt-8 mb-4">
                Copyright © 2025 Manolache Florin<br>
                This project is licensed under the MIT License – see the LICENSE file for details.
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import NewTopics from '@/Components/NewTopics.vue';
import Notifications from '@/Components/Notifications.vue';
import ContentIFollowButton from '@/Components/ContentIFollowButton.vue';
import SearchInput from '@/Components/SearchInput.vue';
import { usePage } from '@inertiajs/vue3';

// Check if the user has SuperAdmin, Admin or Moderator role
const isAdminOrModerator =
    usePage().props.auth.user &&
    (usePage().props.auth.user.roles.includes('SuperAdmin') ||
        usePage().props.auth.user.roles.includes('Admin') ||
        usePage().props.auth.user.roles.includes('Moderator'));

function goToAdminPanel() {
    // This forces a full-page reload to the Filament admin panel
    window.location.href = '/admin';
}
</script>

<style scoped>
.dropdown_link:hover {
    background-color: #dbeafe; /* Equivalent to Tailwind's bg-blue-100 */
}
</style>
