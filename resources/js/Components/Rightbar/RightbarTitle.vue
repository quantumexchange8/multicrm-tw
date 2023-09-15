<script setup>
const props = defineProps({
    title: {
        type: String,
    },
});
</script>

<template>
    <div class="max-w-sm p-6 mx-auto md:ml-auto md:mr-0 bg-white border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-[#0F0F0F] dark:border-transparent dark:hover:bg-opacity-60">
        <h5 class="mb-2 font-bold tracking-tight text-gray-900 dark:text-white text-lg">{{ title }}</h5>
        <slot></slot>
    </div>
</template>
