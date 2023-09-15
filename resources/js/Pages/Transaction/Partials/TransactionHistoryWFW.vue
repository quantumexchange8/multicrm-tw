<script setup>
import {ref} from "vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "@/Components/Button.vue";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import Loading from "@/Components/Loading.vue";
import {TailwindPagination} from "laravel-vue-pagination";
import {transactionFormat} from "@/Composables/index.js";
import Badge from "@/Components/Badge.vue";
library.add(faSearch,faX,faRotateRight);

const buttons = ref([]);
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
const { getChannelName, formatDate, formatAmount, getStatusClass, formatTime } = transactionFormat();
const withdrawals = ref({data: []});
const search = ref('');
const date = ref('');
const isLoading = ref(false);
const currentPage = ref(1);

const getResults = async (page = 1, search = '', dateRange) => {
    isLoading.value = true;
    try {
        let url = `/transaction/getTransaction?page=${page}`;

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
        withdrawals.value = response.data.Withdrawal;
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

const paginationClass = [
    'bg-transparent border-0 text-gray-500'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23]'
];

</script>

<template>
    <form @submit.prevent="submitSearch">
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="space-y-2">
                <Label>Filter By Date</Label>
                <vue-tailwind-datepicker
                    v-model="date"
                    :formatter="formatter"
                    input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                />
            </div>
            <div class="space-y-2">
                <Label>Search By Account No</Label>
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
    <div class="px-6 py-4 mt-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
        <div class="relative overflow-x-auto sm:rounded-lg mt-4">
            <div v-if="isLoading" class="w-full flex justify-center mt-8">
                <Loading />
            </div>
            <table v-else class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
                <tr>
                    <th scope="col" class="px-4 py-3">
                        Date
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Time
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Withdrawal Method
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Amount
                    </th>
                    <th scope="col" class="px-4 py-3">
                        Status
                    </th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="withdrawal in withdrawals.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                    <th scope="row" class="px-2 py-4 font-thin rounded-l-full">
                        {{ formatDate(withdrawal.created_at) }}
                    </th>
                    <th class="px-6 py-4 font-thin">
                        {{ formatTime(withdrawal.created_at) }}
                    </th>
                    <th class="px-2 py-4 font-thin">
                        {{ getChannelName(withdrawal.channel) }}
                    </th>
                    <th class="px-2 py-4 font-thin">
                        $ {{ formatAmount(withdrawal.amount) }}
                    </th>
                    <th class="px-6 py-2 font-thin rounded-r-full">
                        <Badge :status="getStatusClass(withdrawal.status)">{{ withdrawal.status }}</Badge>
                    </th>
                </tr>
                </tbody>
            </table>
            <div class="flex justify-end mt-4">
                <TailwindPagination
                    :item-classes=paginationClass
                    :active-classes=paginationActiveClass
                    :data="withdrawals"
                    @pagination-change-page="handlePageChange"
                />
            </div>
        </div>
    </div>
</template>
