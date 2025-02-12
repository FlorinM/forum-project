<template>
    <nav class="flex items-center space-x-2">
        <!-- Always show "Home" as the first breadcrumb -->
        <div>
            <Link href="/" class="text-blue-600 hover:text-blue-800">
                Home
            </Link>
            <span class="text-gray-500"> &gt; </span>
        </div>

        <!-- Loop through the rest of the breadcrumbs -->
        <template v-for="(breadcrumb, index) in $page.props.breadcrumbs">
            <!-- If it's the last breadcrumb, display it without a link -->
            <div
                v-if="index === $page.props.breadcrumbs.length - 1"
                class="text-gray-900 font-medium"
            >
                {{ breadcrumb.name }}
            </div>
            <!-- Otherwise, display it as a link -->
            <div v-else>
                <Link
                    :href="`/categories/${breadcrumb.id}/subcategories`"
                    class="text-blue-600 hover:text-blue-800"
                >
                    {{ breadcrumb.name }}
                </Link>
                <span class="text-gray-500"> &gt; </span>
            </div>
        </template>
    </nav>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
</script>
