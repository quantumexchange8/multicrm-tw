<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    links: {
        type: Array,
        default: () => []
    }
})
</script>

<template>
    <div class="flex">
        <template v-for="(link, key) in links" :key="key">
            <div v-if="link.url === null"
                 v-html="link.label"
                 class="px-2 py-3 mr-1 text-gray-500 text-sm leading-4 hover:text-"
            ></div>
            <Link v-else
                  :href="link.url"
                  v-html="link.label"
                  class="px-2 py-3 mr-1 text-gray-500 text-sm leading-4 hover:text-[#FF9E23]"
                  :class="{ 'text-[#FF9E23] dark:text-[#FF9E23]': link.active }"
            ></Link>
        </template>
    </div>
</template>
