<template>
  <ForumLayout>
    <div class="max-w-5xl mx-auto p-5 bg-gray-100 rounded-lg shadow-md">
      <h2 v-if="subcategories.length > 0" class="text-center text-2xl font-bold text-gray-800 mb-6">
        Subcategories of {{ category.name }}
      </h2>

      <!-- Display Subcategories -->
      <ul class="list-none p-0">
        <li
          v-for="(subcategory, index) in subcategories"
          :key="subcategory.id"
          class="w-full mb-1 grid grid-cols-10 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
        >
          <!-- Link to subcategory page -->
          <div class="col-span-7">
            <Link
              :href="route('categories.subcategories', subcategory.id)"
              class="block text-sm text-blue-600 w-full text-left p-5"
            >
              {{ subcategory.name }}
            </Link>
          </div>

            <div class="col-span-1 mt-5 text-xs text-blue-500">
                {{ threadCounts[index] }} threads
            </div>

          <!-- Display Latest Post for Each Subcategory -->
          <div v-if="latestPosts[index]" class="col-span-2 text-xs text-blue-500">
            <div class="hover:underline">
              <Link :href="route('threads.show', [subcategory.id, latestPosts[index]?.thread?.id])">
                {{ latestPosts[index]?.thread?.title }}
              </Link>
            </div>
            <div class="hover:underline">
              <Link :href="route('find.post', latestPosts[index].id)">
                {{ new Date(latestPosts[index]?.created_at).toLocaleString() }}
              </Link>
            </div>
            <div v-if="$page.props.auth.user" class="hover:underline">
              <Link :href="route('visited.user.show', latestPosts[index]?.user?.id)">
                By {{ latestPosts[index]?.user?.nickname }}
              </Link>
            </div>
          </div>

          <!-- Render first-level subcategories using CategoryItem component -->
          <ul v-if="subcategory.subcategories && subcategory.subcategories.length" class="pl-5">
            <CategoryItem
              v-for="subcat in subcategory.subcategories"
              :key="subcat.id"
              :category="subcat"
            />
          </ul>
        </li>
      </ul>

      <!-- New Thread Button (only visible to authenticated users) -->
      <Link
        v-if="$page.props.auth.user"
        :href="route('threads.create', { category: category.id })"
        class="inline-block mb-4 px-4 py-2 text-white bg-blue-600 rounded-md transition duration-200 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
      >
        Start New Thread
      </Link>

      <!-- Display Threads for the Current Category -->
      <div v-if="threads.length" class="mt-6">
        <h2 v-if="threads.length > 0" class="text-center text-2xl font-bold text-gray-800 mb-4">
          Threads in {{ category.name }}
        </h2>

        <ul class="list-none p-0">
          <li
            v-for="(thread, index) in threads"
            :key="thread.id"
            class="w-full grid grid-cols-10 mb-1 bg-white border border-gray-300 rounded-md transition duration-200 hover:bg-gray-200"
          >
            <div class="col-span-7">
              <Link
                :href="route('threads.show', [category.id, thread.id])"
                class="block text-sm text-blue-600 w-full text-left pl-5 pt-2 pb-0"
              >
                {{ thread.title }}
              </Link>

              <div class="col-span-1 mt-1 text-xs text-blue-500 pl-5 pt-0">
                    Started by {{ thread.user.nickname }} at {{ useFormatDate(thread.created_at) }}
              </div>
            </div>

            <div class="col-span-1 mt-5 text-xs text-blue-500">
                {{ postCounts[index] }} replies
            </div>

            <!-- Display Latest Post for Each Subcategory -->
            <div v-if="latestPostInThreads[index]" class="col-span-2 mt-3 text-xs text-blue-500">
              <div v-if="$page.props.auth.user" class="hover:underline">
                <Link :href="route('visited.user.show', latestPostInThreads[index]?.user?.id)">
                  {{ latestPostInThreads[index]?.user?.nickname }}
                </Link>
              </div>

              <div class="hover:underline">
                <Link :href="route('find.post', latestPostInThreads[index]?.id)">
                  {{ new Date(latestPostInThreads[index]?.created_at).toLocaleString() }}
                </Link>
              </div>
            </div>

          </li>
        </ul>
      </div>
    </div>
  </ForumLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'; // Importing Link from Inertia
import CategoryItem from '@/Components/CategoryItem.vue'; // Import the CategoryItem component
import ForumLayout from '@/Layouts/ForumLayout.vue';
import { useFormatDate } from '@/Composables/useFormatDate';

const props = defineProps({
  category: Object, // The current category passed to this component
  subcategories: Array, // The first-level subcategories passed to this component
  threads: Array, // The threads for the current category passed to the component
  latestPosts: Array, // The latest posts for each subcategory
  latestPostInThreads: Array, // The latest posts for each thread in current category
  postCounts: Array, // The number of posts for each thread in threads
  threadCounts: Array, // The number of threads for each subcategory in subcategories
});

console.log('POSTS', props.latestPosts);
</script>
