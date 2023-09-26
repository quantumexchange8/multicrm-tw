<script setup>
import {ref, watchEffect} from "vue";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import Action from "@/Pages/Profile/Partials/Action.vue";
import {usePage} from "@inertiajs/vue3";

const cryptoAccounts = ref({data: []});
const isLoading = ref(false);
const getResults = async (page = 1) => {
    isLoading.value = true;
    try {
        let url = `/profile/getPaymentAccount?page=${page}`;

        const response = await axios.get(url);
        cryptoAccounts.value = response.data.cryptoAccounts;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

watchEffect(() => {
    if (usePage().props.toast !== null) {
        getResults();
    }
});

const paginationClass = [
    'bg-transparent border-0 text-gray-500'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23]'
];
</script>

<template>
    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
        <h3
            class="font-semibold text-lg text-gray-800 dark:text-gray-200 leading-tight mb-4"
        >
            {{ $t('public.Cryptocurrency Wallet') }}
        </h3>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <div v-if="isLoading" class="w-full flex justify-center mt-8">
                <Loading />
            </div>
            <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr>
                    <th scope="col" class="px-4 py-3">
                        {{ $t('public.USDT e-Wallet Name') }}
                    </th>
                    <th scope="col" class="px-4 py-3">
                        {{ $t('public.USDT protocol type') }}
                    </th>
                    <th scope="col" class="px-4 py-3">
                        {{ $t('public.Token Address') }}
                    </th>
                    <th scope="col" class="px-4 py-3 w-56">
                        {{ $t('public.Action') }}
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="cryptoAccounts.data.length === 0">
                    <th colspan="8" class="py-4 text-lg text-center">
                        {{ $t('public.No Wallet') }}
                    </th>
                </tr>
                <tr v-for="crypto in cryptoAccounts.data" :key="crypto.id" class="bg-white even:dark:bg-transparent odd:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                        {{ crypto.payment_account_name }}
                    </th>
                    <th class="px-6 py-4">
                        {{ crypto.payment_platform_name }}
                    </th>
                    <th>
                        {{ crypto.account_no }}
                    </th>
                    <th class="py-2 font-thin rounded-r-full">
                        <Action
                            :bank="crypto"
                        />
                    </th>
                </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <TailwindPagination
                    :item-classes=paginationClass
                    :active-classes=paginationActiveClass
                    :data="cryptoAccounts"
                    @pagination-change-page="getResults"
                />
            </div>
        </div>
    </div>
</template>
