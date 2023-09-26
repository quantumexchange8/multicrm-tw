<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import {usePage} from "@inertiajs/vue3";
import {computed, ref} from "vue";
import WalletToAccountForm from "@/Pages/Transaction/Partials/WalletToAccountForm.vue";
import AccountToWallet from "@/Pages/Transaction/Partials/AccountToWallet.vue";
import AccountToAccount from "@/Pages/Transaction/Partials/AccountToAccount.vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import Button from "@/Components/Button.vue";
import Paginator from "@/Components/Paginator.vue";
import TransactionHistoryDTA from "@/Pages/Transaction/Partials/TransactionHistoryDTA.vue";
import TransactionHistoryWFW from "@/Pages/Transaction/Partials/TransactionHistoryWFW.vue";
import TransactionHistoryWTA from "@/Pages/Transaction/Partials/TransactionHistoryWTA.vue";
import TransactionHistoryATW from "@/Pages/Transaction/Partials/TransactionHistoryATW.vue";
import TransactionHistoryATA from "@/Pages/Transaction/Partials/TransactionHistoryATA.vue";
import { usePermission } from '@/Composables/permissions.js'
import TransactionHistoryRTW from "@/Pages/Transaction/Partials/TransactionHistoryRTW.vue";

defineProps({
    tradingUsers: Object,
})

const page = usePage()
const user = computed(() => page.props.auth.user)
const { hasRole } = usePermission();
const transfer_types = [
    { id: 'account_type_2', src: '/assets/finance/wallet-to-account.png', value: 2, title: 'Wallet To Account' },
    { id: 'account_type_3', src: '/assets/finance/account-to-wallet.png', value: 3, title: 'Account To Wallet' },
    { id: 'account_type_4', src: '/assets/finance/account-to-account.png', value: 4, title: 'Account To Account' },
];

const transactionHistories = [
    { id: 'transaction_history_1', src: '/assets/finance/cash-in.png', title: 'Deposit To Account' },
    { id: 'transaction_history_2', src: '/assets/finance/cash-out.png', title: 'Withdrawal From Wallet' },
    { id: 'transaction_history_3', src: '/assets/finance/wallet-to-account.png', title: 'Wallet To Account' },
    { id: 'transaction_history_4', src: '/assets/finance/account-to-wallet.png', title: 'Account To Wallet' },
    { id: 'transaction_history_5', src: '/assets/finance/account-to-account.png', title: 'Account To Account' },
];
const transferType = ref(0);
const transactionHistory = ref(0);
const currentPage = ref(1);

function selectedTransferType(index) {
    transferType.value = index
}

function selectedTransactionHistoryType(index) {
    transactionHistory.value = index
    currentPage.value = index;
}

</script>

<template>
    <AuthenticatedLayout :title="$t('public.Internal Transfer')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Internal Transfer') }}
                </h2>
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <ul class="grid w-full gap-6 md:grid-cols-3">
                <li v-for="(type, index) in transfer_types" :key="index">
                    <input
                        type="radio"
                        :id="type.id"
                        name="account_type"
                        :value="type.value"
                        class="hidden peer"
                        :checked="index === 0"
                        @click="selectedTransferType(index)"
                    >
                    <label :for="type.id" class="inline-flex items-center justify-center w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">
                        <div class="flex flex-col items-center gap-2">
                            <img class="object-cover w-10" :src="type.src" alt="account_type">
                            <p class="text-sm text-gray-500 text-center dark:text-white">{{ $t('public.' + type.title) }}</p>
                        </div>
                    </label>
                </li>
            </ul>

            <div class="my-6 text-center space-y-2" v-if="transferType !== 2">
                <h3 class="text-gray-500 text-sm dark:text-dark-eval-4">{{ $t('public.Current Cash Wallet Balance') }}</h3>
                <h1 class="text-5xl font-extrabold dark:text-white">$ {{ user.cash_wallet }}</h1>
            </div>

            <WalletToAccountForm :tradingUsers="tradingUsers" v-if="transferType === 0"/>
            <AccountToWallet :tradingUsers="tradingUsers" v-else-if="transferType === 1"/>
            <AccountToAccount :tradingUsers="tradingUsers" v-else-if="transferType === 2"/>
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-semibold leading-tight">
                {{ $t('public.sidebar.Transaction History') }}
            </h2>
            <ul class="grid w-full gap-4 grid-cols-1 mt-4" :class="hasRole('ib') ? 'md:grid-cols-6' : 'md:grid-cols-5'">
                <li v-for="(transactionHistory, index) in transactionHistories" :key="index">
                    <input
                        type="radio"
                        :id="transactionHistory.id"
                        name="transactionHistory"
                        class="hidden peer"
                        :checked="index === 0"
                        @click="selectedTransactionHistoryType(index)"
                    >
                    <label :for="transactionHistory.id" class="inline-flex items-center justify-center w-full px-2 py-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">
                        <div class="flex flex-col items-center gap-2">
                            <img class="object-cover w-8 h-8" :src="transactionHistory.src" alt="account_type">
                            <p class="text-[10px] text-gray-500 text-center dark:text-white">{{ $t('public.' + transactionHistory.title) }}</p>
                        </div>
                    </label>
                </li>
                <li v-if="hasRole('ib')">
                    <input
                        type="radio"
                        id="transaction_history_6"
                        name="transactionHistory"
                        class="hidden peer"
                        @click="selectedTransactionHistoryType(5)"
                    >
                    <label for="transaction_history_6" class="inline-flex items-center justify-center w-full px-2 py-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">
                        <div class="flex flex-col items-center gap-2">
                            <img class="object-cover w-8 h-8" src="/assets/finance/rebate-to-wallet.png" alt="account_type">
                            <p class="text-[10px] text-gray-500 text-center dark:text-white">{{ $t('public.Rebate To Wallet') }}</p>
                        </div>
                    </label>
                </li>
            </ul>

            <TransactionHistoryDTA v-if="transactionHistory === 0"/>
            <TransactionHistoryWFW v-else-if="transactionHistory === 1"/>
            <TransactionHistoryWTA v-else-if="transactionHistory === 2"/>
            <TransactionHistoryATW v-else-if="transactionHistory === 3"/>
            <TransactionHistoryATA v-else-if="transactionHistory === 4"/>
            <TransactionHistoryRTW v-else-if="transactionHistory === 5"/>

        </div>

    </AuthenticatedLayout>
</template>
