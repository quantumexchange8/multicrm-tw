<script setup>
import { onMounted, ref } from 'vue'

defineProps({
    modelValue: {
        type: [String, Number],
    },
    withIcon: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['update:modelValue'])

const input = ref(null)

const focus = () => input.value?.focus()

defineExpose({
    input,
    focus
})

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus()
    }
})
</script>

<template>
    <input
        :class="[
            'py-2 border-gray-400 bg-gray-100 rounded-full text-sm placeholder:text-sm',
            'focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white',
            'dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
            'disabled:bg-white disabled:text-dark-eval-4',
            'disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4 disabled:dark:border-transparent',
            {
                'px-4': !withIcon,
                'pl-11 pr-4': withIcon,
            },
        ]"
        :value="modelValue"
        @input="$emit('update:modelValue', $event.target.value)"
        ref="input"
    />
</template>
