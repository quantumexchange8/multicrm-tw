<script setup>
defineProps({
    value: {
        type: String,
        default: ''
    }
});
</script>

<template>
    <label class="block text-sm font-medium text-gray-700 dark:text-white">
    <span v-if="value">
      <span class="text-red-500">*</span> {{ $t(value) }}
    </span>
        <span v-else>
      <slot />
    </span>
    </label>
</template>
