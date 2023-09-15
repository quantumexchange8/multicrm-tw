<script setup>
import { Link } from '@inertiajs/vue3'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Button from '@/Components/Button.vue'
import { MenuFoldLineLeftIcon, MenuFoldLineRightIcon } from '@/Components/Icons/outline'
import { XIcon } from '@heroicons/vue/outline'
import { sidebarState } from '@/Composables'
</script>

<template>
    <div class="flex items-center justify-between flex-shrink-0 px-3">
        <Link :href="route('dashboard')" class="inline-flex items-center gap-2" v-if="!sidebarState.isOpen">
            <span class="sr-only">QCG</span>
            <ApplicationLogo aria-hidden="true" class="ml-1 w-8 h-auto" />
        </Link>
        <Link :href="route('dashboard')" class="inline-flex items-center gap-2" v-else>
            <span class="sr-only">QCG</span>
            <img src="/assets/logo.png" class="object-cover w-4/5 h-auto" alt="">
        </Link>

        <Button
            iconOnly
            variant="secondary"
            type="button"
            v-slot="{ iconSizeClasses }"
            v-show="sidebarState.isOpen || sidebarState.isHovered"
            @click="sidebarState.isOpen = !sidebarState.isOpen"
            :srText="sidebarState.isOpen ? 'Close sidebar' : 'Open sidebar'"
        >
            <MenuFoldLineLeftIcon
                aria-hidden="true"
                v-show="sidebarState.isOpen"
                :class="['hidden lg:block', iconSizeClasses]" />

            <MenuFoldLineRightIcon
                aria-hidden="true"
                v-show="!sidebarState.isOpen"
                :class="['hidden lg:block', iconSizeClasses]" />

            <XIcon
                aria-hidden="true"
                :class="['lg:hidden', iconSizeClasses]" />
        </Button>
    </div>
</template>
