<script setup>
import Button from "@/Components/Button.vue";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {onMounted, ref, watchEffect} from "vue";
import { useForm } from 'laravel-precognition-vue-inertia';
import QrcodeVue from 'qrcode.vue';
import {DuplicateIcon} from "@heroicons/vue/outline";
import {Link, usePage} from "@inertiajs/vue3";
import Checkbox from "@/Components/Checkbox.vue";

const submitWithdrawal = ref(false)
const cryptoWallets = ref([]);
const paymentAccounts = ref([]);
const isLoading = ref(false);
const error = ref(null);
const page = usePage();
const getAccountWallets = page.props.getAccountWallets;

const channels = [
    { id: 'channel', src: '/assets/finance/bank.png', value: 'bank', name: 'Bank Account' },
    { id: 'channel', src: '/assets/finance/cryptocurrency.png', value: 'crypto', name: 'Cryptocurrency' },
];


const form = useForm('post', route('payment.requestWithdrawal'), {
    account_no: '',
    account_type: '',
    amount: '',
    channel: '',
    terms: '',
});

watchEffect(() => {
    const selectedCryptoAccount = getAccountWallets.original.cryptoAccounts.find(account => account.account_no === form.account_no);
    if (selectedCryptoAccount) {
        form.account_type = selectedCryptoAccount.payment_platform_name;
    }

    const selectedBankAccount = getAccountWallets.original.bankAccounts.find(account => account.account_no === form.account_no);
    if (selectedBankAccount) {
        form.account_type = selectedBankAccount.payment_platform_name;
    }
});

const openWithdrawalModal = () => {
    submitWithdrawal.value = true
}

const submit = () => {
    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    })
}

const closeModal = () => {
    submitWithdrawal.value = false
    form.reset()
}
</script>

<template>
    <Button class="w-full justify-center" variant="danger-opacity" @click="openWithdrawalModal">
        Withdrawal
    </Button>

    <Modal :show="submitWithdrawal" @close="closeModal">

        <form class="p-6">
            <div v-if="!form.channel">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">Withdrawal Method</h2>
                <hr>
                <p class="my-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="text-red-500">*</span> Select a withdrawal method
                </p>
                <ul class="my-4 grid w-full gap-6" :class="{'md:grid-cols-3': channels.length >= 3, 'md:grid-cols-2': channels.length === 2}">
                    <li v-for="(channel, index) in channels" :key="index">
                        <input
                            type="radio"
                            :id="`channel_${index}`"
                            name="channel"
                            :value="channel.value"
                            class="hidden peer"
                            v-model="form.channel"
                            :required="index === 1"
                        >
                        <label
                            :for="`channel_${index}`"
                            class="inline-flex items-center justify-center w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600"
                        >
                            <div class="flex flex-col items-center gap-2">
                                <img class="object-cover" :src="channel.src" alt="account_platform">
                                <p class="dark:text-white">{{ channel.name }}</p>
                            </div>
                        </label>
                    </li>
                </ul>
                <InputError :message="form.errors.channel"/>

            </div>
            <!-- Bank -->
            <div v-if="form.channel === 'bank'">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Withdrawal - Bank Account</h2>
                <hr>
                <div class="grid grid-cols-1 my-8 gap-2 w-full text-center">
                    <p class="text-base dark:text-gray-400">Cash Wallet Balance</p>
                    <p class="text-4xl font-bold dark:text-white">$ {{ $page.props.auth.user.cash_wallet }}</p>
                </div>
                <div class="grid grid-cols-1 gap-6" v-if="getAccountWallets.original.bankAccounts.length > 0">
                    <div class="space-y-2">
                        <Label for="account_no" value="Withdraw to Cryptocurrency Account " />

                        <InputSelect v-model="form.account_no" class="block w-full text-sm" placeholder="Select bank account">
                            <option v-for="bankAccount in getAccountWallets.original.bankAccounts" :value="bankAccount.account_no" :key="bankAccount.id">{{ bankAccount.payment_platform_name }} - {{ bankAccount.account_no }}</option>
                        </InputSelect>

                        <InputError :message="form.errors.account_no"/>

                    </div>
                    <div class="space-y-2">
                        <Input id="amount" type="number" min="30" class="block w-full px-4" placeholder="At least $ 30.00" v-model="form.amount" @change="form.validate('amount')" autocomplete="off" />
                        <InputError :message="form.errors.amount"/>

                    </div>
                </div>
                <div v-else class="mt-4 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Don't have an account?
                        <Link :href="route('profile.detail')" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            Click to Create
                            <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </p>
                </div>

            </div>

            <!-- Crypto -->
            <div v-if="form.channel === 'crypto'">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Withdrawal - Cryptocurrency</h2>
                <hr>
                <div class="grid grid-cols-1 my-8 gap-2 w-full text-center">
                    <p class="text-base dark:text-gray-400">Cash Wallet Balance</p>
                    <p class="text-4xl font-bold dark:text-white">$ {{ $page.props.auth.user.cash_wallet }}</p>
                </div>
                <div class="grid grid-cols-1 gap-6" v-if="getAccountWallets.original.cryptoAccounts.length > 0">
                    <div class="space-y-2">

                        <Label for="account_no" value="Withdraw to Cryptocurrency Account " />

                        <InputSelect v-model="form.account_no" class="block w-full text-sm" placeholder="Select cryptocurrency account">
                            <option v-for="cryptoAccount in getAccountWallets.original.cryptoAccounts" :value="cryptoAccount.account_no" :key="cryptoAccount.id">{{ cryptoAccount.payment_platform_name }} - {{ cryptoAccount.account_no }}</option>
                        </InputSelect>

                        <InputError :message="form.errors.account_no"/>

                    </div>

                    <div class="space-y-2">
                        <Label for="amount" value="Withdrawal Amount" />

                        <Input id="amount" type="number" min="30" class="block w-full px-4" placeholder="At least $ 30.00" v-model="form.amount" @change="form.validate('amount')" autocomplete="off" />
                        <InputError :message="form.errors.amount"/>

                    </div>
                </div>
                <div v-else class="mt-4 text-center">
                    <p class="text-gray-500 dark:text-gray-400">Don't have an account?
                        <Link :href="route('profile.detail')" class="inline-flex items-center font-medium text-blue-600 dark:text-blue-500 hover:underline">
                            Click to Create
                            <svg class="w-4 h-4 ml-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                            </svg>
                        </Link>
                    </p>
                </div>
            </div>
            <div class="mt-6" v-if="form.channel">
                <h3 class="list-decimal list-inside text-xl text-dark-eval-4">{{ $t('public.Terms and Conditions') }}</h3>
                <ol class="list-decimal list-inside text-sm text-dark-eval-4">
                    <li>
                        {{ $t("public.Withdrawal Acknowledgement 1", { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t("public.Withdrawal Acknowledgement 2", { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t("public.Withdrawal Acknowledgement 3") }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 4') }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 5') }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 6', { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 7', { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 8', { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 9', { company: 'QCG'}) }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 10') }}
                    </li>
                    <li>
                        {{ $t('public.Withdrawal Acknowledgement 11', { company: 'QCG'}) }}
                    </li>
                </ol>
            </div>
            <div class="flex items-start my-6" v-if="form.channel">
                <div class="flex items-center h-5">
                    <Checkbox v-model="form.terms"/>
                </div>
                <div class="ml-3 text-sm">
                    <Label for="terms" class="font-light dark:text-dark-eval-4" :value="$t('public.Withdrawal Acknowledgement')" />
                </div>
            </div>
            <InputError :message="form.errors.terms"/>
            <div class="mt-6 flex justify-end" v-if="form.channel">
                <Button variant="secondary" @click="closeModal">
                    Cancel
                </Button>

                <Button
                    variant="primary"
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    Submit
                </Button>
            </div>
        </form>
    </Modal>
</template>
