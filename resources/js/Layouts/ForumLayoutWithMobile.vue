<template>
    <div>
        <nav v-if="$page.props.canLogin" class="flex justify-end gap-4">
            <!-- Desktop View: Authenticated User Dropdown -->
            <div
                v-if="$page.props.auth.user"
                class="relative ms-3 hidden sm:flex"
            >
                <Dropdown align="right" width="48">
                    <template #trigger>
                        <span class="inline-flex rounded-md">
                            <button
                                type="button"
                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                            >
                                {{ $page.props.auth.user.name }}
                                <svg
                                    class="-me-0.5 ms-2 h-4 w-4"
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
                        </span>
                    </template>

                    <template #content>
                        <DropdownLink :href="route('profile.edit')">
                            Profile
                        </DropdownLink>
                        <DropdownLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            Log Out
                        </DropdownLink>
                    </template>
                </Dropdown>
            </div>

            <!-- Mobile View: Hamburger Menu Toggle -->
            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="
                        showingNavigationDropdown = !showingNavigationDropdown
                    "
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                >
                    <svg
                        class="h-6 w-6"
                        stroke="currentColor"
                        fill="none"
                        viewBox="0 0 24 24"
                    >
                        <path
                            :class="{
                                hidden: showingNavigationDropdown,
                                'inline-flex': !showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                        <path
                            :class="{
                                hidden: !showingNavigationDropdown,
                                'inline-flex': showingNavigationDropdown,
                            }"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M6 18L18 6M6 6l12 12"
                        />
                    </svg>
                </button>
            </div>
        </nav>

        <!-- Mobile View: Dropdown Content -->
        <div v-if="showingNavigationDropdown" class="sm:hidden">
            <div class="space-y-1 pb-3 pt-2">
                <Link
                    :href="route('dashboard')"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Dashboard
                </Link>
            </div>

            <div
                v-if="$page.props.auth.user"
                class="border-t border-gray-200 pb-1 pt-4"
            >
                <div class="px-4">
                    <div class="text-base font-medium text-gray-800">
                        {{ $page.props.auth.user.name }}
                    </div>
                    <div class="text-sm font-medium text-gray-500">
                        {{ $page.props.auth.user.email }}
                    </div>
                </div>
                <div class="mt-3 space-y-1">
                    <Link
                        :href="route('profile.edit')"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Profile
                    </Link>
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                    >
                        Log Out
                    </Link>
                </div>
            </div>

            <!-- Mobile Guest Links for Login and Register -->
            <div v-else class="pb-3">
                <Link
                    :href="route('login')"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Log in
                </Link>
                <Link
                    v-if="$page.props.canRegister"
                    :href="route('register')"
                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                >
                    Register
                </Link>
            </div>
        </div>

        <!-- Slot for Page-Specific Content -->
        <slot />
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

// State to manage mobile dropdown visibility
const showingNavigationDropdown = ref(false);
</script>
