<template>
  <div class="min-h-screen bg-blue-50">
    <nav class="flex justify-between items-center bg-blue-600 text-white shadow-md p-4">
      <div class="flex items-center">
        <h1 class="text-lg font-bold">My Forum</h1>
      </div>
      <div class="flex items-center">
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
              <DropdownLink :href="route('profile.edit')" class="text-gray-700 hover:bg-gray-200">
                Profile
              </DropdownLink>
              <DropdownLink :href="route('logout')" method="post" as="button" class="text-gray-700 hover:bg-gray-200">
                Log Out
              </DropdownLink>
            </template>
          </Dropdown>
        </div>

        <!-- Guest Links for Login and Register -->
        <template v-else>
          <div class="flex space-x-4">
            <Link :href="route('login')" class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded-md transition">Log in</Link>
            <Link v-if="$page.props.canRegister" :href="route('register')" class="px-4 py-2 bg-blue-500 text-white hover:bg-blue-600 rounded-md transition">Register</Link>
          </div>
        </template>
      </div>
    </nav>

    <!-- Slot for Page-Specific Content -->
    <div class="py-6">
      <slot />
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>
