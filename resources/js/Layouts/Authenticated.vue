<script setup>
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar/Sidebar.vue'
import Navbar from '@/Components/Navbar.vue'
import PageFooter from '@/Components/PageFooter.vue'
import { sidebarState } from '@/Composables'
import Rightbar from "@/Components/Rightbar/Rightbar.vue";
import ToastList from "@/Components/ToastList.vue";

defineProps({
    title: String,
})
</script>

<template>
    <Head :title="title"></Head>

    <div
        class="min-h-screen text-gray-900 bg-gray-100 dark:bg-dark-eval-0 dark:text-gray-100"
    >
        <!-- Sidebar -->
        <Sidebar />

        <div
            style="transition-property: margin; transition-duration: 150ms"
            :class="[
                'min-h-screen flex flex-col',
                {
                    'lg:ml-64': sidebarState.isOpen,
                    'md:ml-16': !sidebarState.isOpen,
                },
            ]"
        >
            <!-- Navbar -->
            <Navbar />

            <!-- Page Heading -->
            <header v-if="$slots.header">
                <div class="p-4 sm:p-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1 px-4 sm:px-6">
                <div class="flex flex-col md:flex-row gap-4">
                    <main class="flex-1">
                        <ToastList />
                        <slot />
                    </main>

                    <!-- Right Content Section -->
                    <Rightbar />
                </div>
            </div>

            <PageFooter />
        </div>
    </div>
</template>
