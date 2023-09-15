<!-- Select.vue -->

<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number],
    },
    keyIndex: String,
    valueIndex: String,
    labelIndex: String,
    data: Array,
    placeholder: {
        type: String,
        default: 'Select an option'
    }
});

defineEmits(['update:modelValue']);

const input = ref(null);

const focus = () => input.value?.focus();

defineExpose({
    input,
    focus
});

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});
</script>

<template>
    <select
        :class="[
      'py-2 border-gray-400 rounded-full text-sm placeholder:text-sm',
      'focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white',
      'dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
    ]"
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        ref="input"
    >
        <option class="text-sm" value="" disabled selected>{{ placeholder }}</option>
        <slot></slot>
    </select>
</template>
