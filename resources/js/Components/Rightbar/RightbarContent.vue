<script setup>
const props = defineProps({
    title: {
        type: String,
    },
    amount: {
        type: String,
    },
});
</script>

<template>
    <div class="text-gray-700 dark:text-gray-500 space-y-1 mb-4">
        <p class="text-sm font-medium text-left">{{ title }}</p>
        <p class="text-right font-bold dark:text-white">{{ amount }}</p>
    </div>
</template>
