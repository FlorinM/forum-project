<template>
  <div>
    <nav v-if="$page.props.canLogin" class="flex justify-end gap-4">
      <!-- Authenticated User Dropdown -->
      <div v-if="$page.props.auth.user" class="relative ms-3">
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
            <DropdownLink :href="route('logout')" method="post" as="button">
              Log Out
            </DropdownLink>
          </template>
        </Dropdown>
      </div>

      <!-- Guest Links for Login and Register -->
      <template v-else>
        <Link :href="route('login')" class="px-3 py-2">Log in</Link>
        <Link v-if="$page.props.canRegister" :href="route('register')" class="px-3 py-2">
          Register
        </Link>
      </template>
    </nav>

    <!-- Slot for Page-Specific Content -->
    <slot />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
</script>

