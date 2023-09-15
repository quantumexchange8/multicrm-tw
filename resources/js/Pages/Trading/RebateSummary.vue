<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Label from "@/Components/Label.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import Input from "@/Components/Input.vue";
import {onMounted, ref} from "vue";
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import {TailwindPagination} from "laravel-vue-pagination";
import Loading from "@/Components/Loading.vue";
library.add(faSearch,faX,faRotateRight);

const { formatDate, formatAmount } = transactionFormat();
const props = defineProps({
    symbolGroups: Object
})

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

const totalBySymbolGroup = ref([]);
const tradingSummary = ref({data: []});
const search = ref('');
const date = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1, search = '', dateRange) => {
    isLoading.value = true;
    try {
        let url = `/trading/getRebateSummary?page=${page}`;

        if (search) {
            url += `&search=${search}`;
        }

        if (dateRange) {
            if (dateRange.length === 2) {
                const formattedDates = dateRange.map(date => `date[]=${date}`).join('&');
                url += `&${formattedDates}`;
            }
        }

        const response = await axios.get(url);
        tradingSummary.value = response.data.tradingSummary;
        totalBySymbolGroup.value = response.data.totalBySymbolGroup;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults();

const submitSearch = async () => {
    const dateRange = date.value.split(' ~ ');

    await getResults(1, search.value, dateRange);
};

const reset = () => {
    getResults();
    date.value = '';
    search.value = '';
}

function clearField() {
    search.value = '';
}

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        const dateRange = date.value.split(' ~ ');
        getResults(currentPage.value, search.value, dateRange, );
    }
};

const findTotalBySymbolGroupId = (symbolGroupId) => {
    const matchingTotal = totalBySymbolGroup.value.find(item => item.symbol_group === symbolGroupId);
    return matchingTotal
        ? { totalRebate: formatAmount(matchingTotal.total_rebate), totalVolume: formatAmount(matchingTotal.total_volume) }
        : { totalRebate: '0.00', totalVolume: '0.00' }; // Change to the appropriate fields
};

const isLastCell = (index) => {
    return index === props.symbolGroups.length - 1;
}

const paginationClass = [
    'bg-transparent border-0 text-gray-500'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23]'
];
</script>

<template>
    <AuthenticatedLayout title="Rebate Summary">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Rebate Summary
                </h2>
            </div>
        </template>

        <form @submit.prevent="submitSearch">
            <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <Label>Search By Name / Account No</Label>
                    <div class="relative w-full">
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
                            <Input withIcon id="name" type="text" class="block w-full" v-model="search" />
                        </InputIconWrapper>
                        <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                            icon="fa-solid fa-x"
                            class="flex-shrink-0 w-3 h-3 cursor-pointer"
                            aria-hidden="true"
                            @click.prevent="clearField"
                        /></button>
                    </div>
                </div>

                <div class="space-y-2">
                    <Label>Filter By Date</Label>
                    <vue-tailwind-datepicker
                        v-model="date"
                        :formatter="formatter"
                        input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                    />
                </div>

                <div class="mt-6">
                    <div class="grid grid-cols-2 gap-4 mt-2 md:mt-0">
                        <Button
                            variant="primary-opacity"
                            class="justify-center py-3"
                        >
                            Search
                        </Button>
                        <Button
                            variant="danger-opacity"
                            class="justify-center py-3"
                            @click.prevent="reset"
                        >
                            Reset
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <div class="my-8 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div
                v-for="symbolGroup in props.symbolGroups"
                :key="symbolGroup.id"
                class="block py-2 px-6 bg-white text-center border border-gray-200 rounded-lg shadow hover:bg-gray-100 dark:bg-dark-eval-2 dark:border-gray-700 dark:hover:bg-dark-eval-1"
            >
                <h5 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">{{ symbolGroup.display }}</h5>
                <div class="font-normal text-gray-700 dark:text-gray-400 text-sm">
                    <div v-if="isLoading" class="w-full flex justify-center my-4">
                        <Loading />
                    </div>
                    <div v-else class="flex flex-col">
                        <div>
                            <span class="text-xs">Total (USD):</span> $ {{ findTotalBySymbolGroupId(symbolGroup.id).totalRebate }}
                        </div>
                        <div>
                            <span class="text-xs">Total Lots:</span> {{ findTotalBySymbolGroupId(symbolGroup.id).totalVolume }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
            <div class="relative overflow-x-auto sm:rounded-lg">
                <div v-if="isLoading" class="w-full flex justify-center mt-8">
                    <Loading />
                </div>
                <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                    <tr>
                        <th scope="col" class="px-4 py-3">
                            Name
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Account Number
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Account Type
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Date
                        </th>
                        <th scope="col" class="px-4 py-3">
                            Total Rebate (USD)
                        </th>
                        <th scope="col" class="px-4 py-3" v-for="symbolGroup in symbolGroups">
                            {{ symbolGroup.display}} <br> (USD / Lot)
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="summary in tradingSummary.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                        <th scope="row" class="px-2 py-4 font-thin rounded-l-full">
                            {{ summary.user.first_name }}
                        </th>
                        <th class="px-6 py-4 font-thin">
                            {{ summary.meta_login }}
                        </th>
                        <th class="px-2 py-4 font-thin">
                            {{ summary.account_type.name }}
                        </th>
                        <th class="px-2 py-4 font-thin">
                            {{ formatDate(summary.closed_time) }}
                        </th>
                        <th class="px-2 py-4">
                            $ {{ formatAmount(summary.total_rebate) }}
                        </th>
                        <td v-for="(symbolGroup, index) in symbolGroups" :key="symbolGroup.id" class="px-4 py-3" :class="{'font-thin rounded-r-full': isLastCell(index)}">
                            $ {{ formatAmount(summary['total_rebate_' + symbolGroup.id]) }} / {{ formatAmount(summary['total_volume_' + symbolGroup.id]) }}
                        </td>
                    </tr>
                    </tbody>
                </table>
                <div class="flex justify-end mt-4">
                    <TailwindPagination
                        :item-classes=paginationClass
                        :active-classes=paginationActiveClass
                        :data="tradingSummary"
                        @pagination-change-page="handlePageChange"
                    />
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
