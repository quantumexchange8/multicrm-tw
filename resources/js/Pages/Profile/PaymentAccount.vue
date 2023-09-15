<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {Link} from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import BankAccountForm from "@/Pages/Profile/Partials/BankAccountForm.vue";
import CryptoAccountForm from "@/Pages/Profile/Partials/CryptoAccountForm.vue";
import {ref} from "vue";

const props = defineProps({
    countries: Object
})

const channels = [
    { id: 'channel', src: '/assets/finance/bank.png', value: 'bank', name: 'Bank Account' },
    { id: 'channel', src: '/assets/finance/cryptocurrency.png', value: 'crypto', name: 'Cryptocurrency' },
];

const paymentPlatform = ref('bank');
</script>

<template>
    <AuthenticatedLayout title="Profile">
        <template #header>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <div class="flex items-center">
                            <Link :href="route('profile.detail')" class="font-semibold text-xl text-gray-700 hover:text-[#FF9E23] dark:text-gray-200 leading-tight dark:hover:text-[#FF9E23]">User Profile</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 dark:text-white mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 mt-1 font-semibold text-xl text-[#FF9E23] md:ml-2 dark:text-[#FF9E23]">Create Payment Account</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </template>

        <div class="p-4 sm:p-8 bg-white dark:bg-dark-eval-1 shadow sm:rounded-lg">
            <ul class="mb-8 grid w-full gap-6 grid-cols-2">
                <li v-for="(channel, index) in channels" :key="index">
                    <input
                        type="radio"
                        :id="`channel_${index}`"
                        name="channel"
                        :value="channel.value"
                        class="hidden peer"
                        v-model="paymentPlatform"
                    >
                    <label
                        :for="`channel_${index}`"
                        class="inline-flex items-center justify-center w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600"
                    >
                        <div class="flex flex-col items-center gap-2">
                            <img class="object-cover" :src="channel.src" alt="account_platform">
                            <p class="text-xs dark:text-white">{{ channel.name }}</p>
                        </div>
                    </label>
                </li>
            </ul>

            <!-- Bank -->
            <div v-if="paymentPlatform === 'bank'">

                <BankAccountForm
                    :countries="props.countries"
                />

            </div>

            <!-- Crypto -->
            <div v-if="paymentPlatform === 'crypto'">

                <CryptoAccountForm />

            </div>
        </div>
    </AuthenticatedLayout>
</template>
