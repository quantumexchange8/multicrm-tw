<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import RebateChild from './Partials/RebateChild.vue'
import {computed, ref, watch} from "vue";
import {usePage} from "@inertiajs/vue3";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import { faSearch,faX,faRotateRight } from '@fortawesome/free-solid-svg-icons';
import { library } from "@fortawesome/fontawesome-svg-core";
import { router } from '@inertiajs/vue3'
import debounce from "lodash/debounce.js";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";

library.add(faSearch,faX,faRotateRight);
const page = usePage()
const user = computed(() => page.props.auth.user)

const props = defineProps({
    children: Object,
    ib: Object,
    filters: Object,
});

// const children = props.children;
const ib = props.ib;

let search = ref(props.filters.search);

watch(search, debounce(function  (value) {
    router.get('/group_network/rebate_allocation', { search: value }, {
        preserveState:true,
        preserveScroll:true,
        replace:true,
    });
}, 300));

function formatDate(date) {
    const formattedDate = new Date(date).toISOString().slice(0, 10);
    return formattedDate.replace(/-/g, '/');
}

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

</script>

<template>
    <AuthenticatedLayout :title="$t('public.Rebate Allocation')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Rebate Allocation') }}
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
                <div class="flex flex-col items-center mb-4">
                    <img
                        class="h-12 w-12 rounded-full"
                        :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                        alt="ProfilePic"
                    >
                    <h5 class="my-1 text-xl font-medium text-gray-900 dark:text-white">{{ ib.of_user.first_name }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ ib.of_user.ib_id }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center md:text-left">
                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Account Type') }}:</div>
                    <div class="text-black dark:text-white">{{ ib.account_type.name }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Since Date') }} </div>
                    <div class="text-black dark:text-white">{{ formatDate(ib.created_at) }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Direct IB') }} </div>
                    <div class="text-black dark:text-white">{{ ib.of_user.direct_ib }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Direct Clients') }} </div>
                    <div class="text-black dark:text-white">{{ ib.of_user.direct_client }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Total Group IB') }} </div>
                    <div class="text-black dark:text-white">{{ ib.of_user.total_ib }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Total Group Clients') }} </div>
                    <div class="text-black dark:text-white">{{ ib.of_user.total_client }}</div>
                </div>
            </div>
            <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
                <div class="flex flex-col text-center md:text-left">
                    <h5 class="mb-6 text-xl font-medium text-gray-900 dark:text-white">{{ $t('public.My Rebate Info') }}</h5>
                    <div
                        v-for="account in ib.symbol_groups"
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4"
                    >
                        <div class="dark:text-dark-eval-3 uppercase">
                            {{ account.symbol_group.name }} (USD) / LOT
                        </div>
                        <div class="text-center">
                            {{ account.amount }}
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="w-full my-6 flex justify-end gap-4">
            <div class="relative w-full md:w-2/3">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
                <InputIconWrapper>
                    <template #icon>
                        <font-awesome-icon
                            icon="fa-solid fa-search"
                            class="flex-shrink-0 w-5 h-5 cursor-pointer"
                            aria-hidden="true"
                        />
                    </template>
                    <Input withIcon id="name" type="text" :placeholder="$t('public.Name / Email')" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                </InputIconWrapper>
                <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                    icon="fa-solid fa-x"
                    class="flex-shrink-0 w-3 h-3 cursor-pointer"
                    aria-hidden="true"
                    @click="clearField"
                /></button>
            </div>
            <Button class="justify-center gap-2 rounded-full" iconOnly v-slot="{ iconSizeClasses }">
                <font-awesome-icon
                    icon="fa-solid fa-rotate-right"
                    :class="iconSizeClasses"
                    aria-hidden="true"
                    @click="clearField"
                />
            </Button>
        </div>

        <RebateChild :children="props.children" :ib="ib" />

    </AuthenticatedLayout>
</template>
