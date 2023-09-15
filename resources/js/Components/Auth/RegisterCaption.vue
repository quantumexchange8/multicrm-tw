<script setup>
defineProps({
    title: String,
    caption: String,
})
</script>

<template>
    <h2 class="text-white font-semibold text-lg" style="font-family: 'Montserrat SemiBold', sans-serif">{{ title }}</h2>
    <p class="text-sm text-[#989898]">{{ caption }}</p>
</template>
