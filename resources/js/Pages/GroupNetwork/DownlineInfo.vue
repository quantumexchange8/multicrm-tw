<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import { faSearch,faX,faRotateRight } from '@fortawesome/free-solid-svg-icons';
import { library } from "@fortawesome/fontawesome-svg-core";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { ref, watch } from "vue";
import {Link, router} from '@inertiajs/vue3'
import debounce from "lodash/debounce.js";
import Paginator from "@/Components/Paginator.vue";
import Button from "@/Components/Button.vue";
import InputSelect from "@/Components/InputSelect.vue";

library.add(faSearch,faX,faRotateRight);

const props = defineProps({
    members: Object,
    countries: Object,
    accountTypes: Object,
    filters: Object,
});

let search = ref(props.filters.search);
let role = ref(props.filters.role);

watch(search, debounce(function  (value) {
    router.get('/group_network/downline_info', { search: value, role: role.value }, { preserveState:true, replace:true });
}, 300));

function resetField() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');
    url.searchParams.delete('role');

    // Navigate to the updated URL without the search parameter
    window.location.href = url.href;
}

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

function formatDate(date) {
    const formattedDate = new Date(date).toISOString().slice(0, 10);
    return formattedDate.replace(/-/g, '/');
}

function getRole() {
    router.get('/group_network/downline_info', { role: role.value, search: search.value }, { preserveState:true, replace:true });
}

</script>

<template>

    <AuthenticatedLayout :title="$t('public.Downline Info')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Downline Info') }}
                </h2>
            </div>
        </template>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <InputSelect
                    v-model="role"
                    @change="getRole"
                    class="block w-full text-sm"
                    placeholder="Choose Role"
                >
                    <option value="">All</option>
                    <option value="ib">IB</option>
                    <option value="member">Member</option>
                </InputSelect>
            </div>
            <div class="col-span-2 flex justify-between">
                <div class="relative w-full mr-4">
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
                        <Input withIcon id="name" type="text" placeholder="Name / Email" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                    </InputIconWrapper>
                    <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                        icon="fa-solid fa-x"
                        class="flex-shrink-0 w-3 h-3 cursor-pointer"
                        aria-hidden="true"
                        @click="clearField"
                    /></button>
                </div>
                <Button class="justify-center gap-2 rounded-full" iconOnly v-slot="{ iconSizeClasses }" @click="resetField">
                    <font-awesome-icon
                        icon="fa-solid fa-rotate-right"
                        :class="iconSizeClasses"
                        aria-hidden="true"
                    />
                </Button>
            </div>

        </div>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <div class="relative overflow-x-auto sm:rounded-lg mt-4">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr class="uppercase">
                        <th scope="col" class="px-6 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Register Date
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Wallet Balance
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Role
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Upline Email
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total Account
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Country
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="member in props.members.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <td class="px-4 py-4 font-thin rounded-l-full">
                            {{ member.first_name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ member.email }}
                        </td>
                        <td>
                            {{ formatDate(member.created_at) }}
                        </td>
                        <td>
                            $ {{ member.cash_wallet }}
                        </td>
                        <td>
                            <span v-if="member.role === 'member'" class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-[#007BFF] dark:text-dark-eval-1 uppercase">{{ member.role }}</span>
                            <span v-if="member.role === 'ib'" class="bg-orange-100 text-orange-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-[#FF9E23] dark:text-dark-eval-1 uppercase">{{ member.role }}</span>
                        </td>
                        <td class="px-6">
                            {{ member.upline ? member.upline.email : '' }}
                            <span v-if="!member.upline" class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-purple-500 dark:text-purple-100 uppercase">No Upline</span>
                        </td>
                        <td>
                            {{ member.trading_accounts.length }}
                        </td>
                        <td>
                            {{ member.country }}
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="flex justify-end mt-4">
                <Paginator :links="props.members.links" />
            </div>
        </div>


    </AuthenticatedLayout>

</template>
