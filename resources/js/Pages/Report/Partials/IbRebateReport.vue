<script setup>
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import {ref} from "vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {useForm} from "@inertiajs/vue3";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import {transactionFormat} from "@/Composables/index.js";
import Loading from "@/Components/Loading.vue";
import Label from "@/Components/Label.vue";
import {TailwindPagination} from "laravel-vue-pagination";

library.add(faSearch,faX,faRotateRight);

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const { formatDate, formatAmount } = transactionFormat();
function refreshTable() {
    getResults();
}

const submitSearch = async () => {
    const dateRange = date.value.split(' ~ ');

    await getResults(1, dateRange, search.value);
};

const ibRebateReport = ref({data: []});
const ibTotalRebate = ref();
const date = ref('');
const search = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1,  dateRange, search = '') => {
    isLoading.value = true;
    try {
        let url = `/report/getRevenueReport?page=${page}`;

        if (dateRange) {
            if (dateRange.length === 2) {
                const formattedDates = dateRange.map(date => `date[]=${date}`).join('&');
                url += `&${formattedDates}`;
            }
        }

        if (search) {
            url += `&search=${search}`;
        }

        const response = await axios.get(url);
        ibRebateReport.value = response.data.ibRebateReport;
        ibTotalRebate.value = response.data.ibTotalRebate;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}
getResults();

const reset = () => {
    getResults();
    date.value = '';
    search.value = '';
}

function clearField() {
    form.search = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

const handlePageChange = (newPage) => {
    if (newPage >= 1) {
        currentPage.value = newPage;
        const dateRange = date.value.split(' ~ ');
        getResults(currentPage.value, dateRange, search.value);
    }
};

const exportDeposit = () => {
    const dateRange = date.value.split(' ~ ');

    let url = `/report/getRevenueReport?export=yes`;

    if (dateRange.length === 2) {
        const formattedDates = dateRange.map(date => `date[]=${date}`).join('&');
        url += `&${formattedDates}`;
    }

    if (search) {
        url += `&search=${search.value}`;
    }

    window.location.href = url;
}

const paginationClass = [
    'bg-transparent border-0 text-gray-500 text-xs'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23] text-xs'
];
</script>

<template>
    <form @submit.prevent="submitSearch">
        <div class="my-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="space-y-2">
                <Label>Filter By</Label>
                <vue-tailwind-datepicker
                    :formatter="formatter"
                    v-model="date"
                    input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                />
            </div>
            <div class="space-y-2">
                <Label>Search By Client Name / Acc No</Label>
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
                        <Input withIcon id="name" type="text" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                    </InputIconWrapper>
                    <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                        icon="fa-solid fa-x"
                        class="flex-shrink-0 w-3 h-3 cursor-pointer"
                        aria-hidden="true"
                        @click="clearField"
                    /></button>
                </div>
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
            <div class="md:col-span-4">
                <Button
                    variant="primary"
                    class="justify-center w-full md:w-1/4 py-3"
                    @click.prevent="exportDeposit"
                >
                    Export
                </Button>
            </div>
        </div>
    </form>

    <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1 mt-6">
        <div class="flex justify-end">
            <font-awesome-icon
                icon="fa-solid fa-rotate-right"
                class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-dark-eval-4"
                aria-hidden="true"
                @click="refreshTable"
            />
        </div>
        <div class="relative overflow-x-auto sm:rounded-lg">
            <!-- Withdrawal Pending -->
            <div v-if="isLoading" class="w-full flex justify-center my-12">
                <Loading />
            </div>
            <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr>
                    <th scope="col" class="px-4 py-3">
                        Acc No.
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Acc Type
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Client Name
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Symbol
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Volume (Lot)
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Rebate / Lot
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Ticket No.
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Rebate
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-if="ibRebateReport.data.length === 0">
                    <th colspan="8" class="py-4 text-lg text-center">
                        No Rebate History
                    </th>
                </tr>
                <tr v-for="rebate in ibRebateReport.data" :key="rebate.id" class="bg-white even:dark:bg-transparent odd:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                        {{ rebate.meta_login }}
                    </th>
                    <th class="px-6 py-4">
                        {{ rebate.of_account_type.name }}
                    </th>
                    <th>
                        {{ rebate.of_ticket_user.first_name }}
                    </th>
                    <th>
                        {{ rebate.symbol }}
                    </th>
                    <th>
                        {{ rebate.volume }}
                    </th>
                    <th>
                        $ {{ formatAmount(rebate.final_net_rebate) }}
                    </th>
                    <th>
                        {{ rebate.ticket }}
                    </th>
                    <th class="py-2 font-thin rounded-r-full">
                        $ {{ formatAmount(rebate.revenue) }}
                    </th>
                </tr>
                </tbody>
            </table>
            <div class="ml-1 my-4 text-xl text-right" v-if="!isLoading">
                Total Rebate: $ {{ formatAmount(ibTotalRebate) }}
            </div>
            <div class="flex justify-end mt-4" v-if="!isLoading">
                <TailwindPagination
                    :item-classes=paginationClass
                    :active-classes=paginationActiveClass
                    :data="ibRebateReport"
                    :limit=1
                    :keepLength="true"
                    @pagination-change-page="handlePageChange"
                />
            </div>
        </div>
    </div>

</template>
