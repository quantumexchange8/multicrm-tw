<script setup>
import Button from "@/Components/Button.vue";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import Label from "@/Components/Label.vue";
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import {onMounted, ref} from "vue";
import { useForm } from 'laravel-precognition-vue-inertia';
import QrcodeVue from 'qrcode.vue';
import {DuplicateIcon} from "@heroicons/vue/outline";
import toast from "@/Composables/toast.js";
import {usePage} from "@inertiajs/vue3";

const submitDeposit = ref(false)
const cryptoWallets = ref([]);
const isLoading = ref(false);
const error = ref(null);
const selectedAccountPlatform = ref(null);

const page = usePage();
const getPaymentAccount = page.props.getPaymentAccount;

const depositMethods = [
    { id: 'deposit_method', src: '/assets/finance/bank.png', value: 'bank', name: 'Bank' },
    { id: 'deposit_method', src: '/assets/finance/cryptocurrency.png', value: 'crypto', name: 'Cryptocurrency' },
    { id: 'deposit_method', src: '/assets/finance/fpx-gateway.png', value: 'fpx', name: 'FPX' },
];

const platforms = [
    { id: 'account_platform_2', src: '/assets/platform/icon/metatrader5.png', value: 2 },
    { id: 'account_platform_3', src: '/assets/platform/icon/ctrader.png', value: 3 },
    { id: 'account_platform_4', src: '/assets/platform/icon/match_trade.png', value: 4 },
];

const getCryptoMediaByCollection = (media, collectionName) => {
    return media.filter((cryptoMedia) => cryptoMedia.collection_name === collectionName);
};

const handlePaymentReceipt = (event) => {
    form.front_identity = event.target.files[0];
};

const form = useForm('post', route('payment.deposit'), {
    deposit_method: '',
    account_no: '',
    currency: '',
    amount: '',
    txid: '',
    payment_receipt: '',
    description: '',
});

function copyTestingCode () {
    let walletAddressCopy = document.querySelector('#cryptoWalletAddress')
    walletAddressCopy.setAttribute('type', 'text');
    walletAddressCopy.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            toast.add({
                message: "Copy Successfully!",
            });
        } else {
            alert('Try again later')
        }

    } catch (err) {
        alert('Oops, unable to copy');
    }

    /* unselect the range */
    walletAddressCopy.setAttribute('type', 'hidden')
    window.getSelection().removeAllRanges()
}

const openDepositModal = () => {
    submitDeposit.value = true
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
    submitDeposit.value = false
    form.reset()
}
</script>

<template>
    <Button class="w-full justify-center" variant="success-opacity" @click="openDepositModal">
        {{ $t('public.Deposit') }}
    </Button>

    <Modal :show="submitDeposit" @close="closeModal">

        <form class="p-6">
            <div v-if="!form.deposit_method">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.Deposit Method') }}</h2>
                <hr>
                <p class="my-4 text-sm text-gray-600 dark:text-gray-400">
                    <span class="text-red-500">*</span> {{ $t('public.Select a deposit method') }}
                </p>
                <ul class="my-4 grid w-full gap-6" :class="{'md:grid-cols-3': depositMethods.length >= 3, 'md:grid-cols-2': depositMethods.length === 2}">
                    <li v-for="(depositMethod, index) in depositMethods" :key="index">
                        <input
                            type="radio"
                            :id="`deposit_method_${index}`"
                            name="deposit_method"
                            :value="depositMethod.value"
                            class="hidden peer"
                            v-model="form.deposit_method"
                            :required="index === 1"
                        >
                        <label
                            :for="`deposit_method_${index}`"
                            class="inline-flex items-center justify-center w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600"
                        >
                            <div class="flex flex-col items-center gap-2">
                                <img class="object-cover" :src="depositMethod.src" alt="account_platform">
                                <p class="dark:text-white">{{ $t('public.' + depositMethod.name) }}</p>
                            </div>
                        </label>
                    </li>
                </ul>
            </div>
            <!-- Bank -->
            <div v-if="form.deposit_method === 'bank'">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.Bank') }}</h2>
                <hr>
                <div class="grid gap-6 my-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="account_no" :value="$t('public.Account No')" />
                        <InputSelect class="w-full" id="account_no" v-model="form.account_no" :placeholder="$t('public.Select Account No')" >
                            <option v-for="paymentAccount in getPaymentAccount" :value="paymentAccount.meta_login" :key="paymentAccount.id">{{ paymentAccount.meta_login }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.account_no"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="currency" :value="$t('public.Currency')" />
                        <InputSelect v-model="form.currency" class="w-full" id="currency" :placeholder="$t('public.Select Currency')">
                            <option value="MYR">MYR</option>
                            <option value="VND">VND</option>
                        </InputSelect>
                        <InputError :message="form.errors.currency"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="amount" :value="$t('public.Deposit Amount') + ' (USD)'" />
                        <Input id="amount" type="number" min="30" class="block w-full px-4" :placeholder="$t('public.Deposit Amount')" v-model="form.amount" @change="form.validate('amount')" />
                        <InputError :message="form.errors.amount"/>
                    </div>
                </div>
            </div>

            <!-- Crypto -->
            <div v-if="form.deposit_method === 'crypto'">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.Cryptocurrency') }}</h2>
                <hr>
                <div class="grid gap-6 my-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="account_no" :value="$t('public.Account No')" />
                        <InputSelect class="w-full" id="account_no" v-model="form.account_no" :placeholder="$t('public.Select Account No')" >
                            <option v-for="paymentAccount in getPaymentAccount" :value="paymentAccount.meta_login" :key="paymentAccount.id">{{ paymentAccount.meta_login }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.account_no"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="currency" :value="$t('public.Currency')" />
                        <InputSelect v-model="form.currency" class="w-full" id="currency" :placeholder="$t('public.Select Currency')">
                            <option value="USDT">USDT</option>
                        </InputSelect>
                        <InputError :message="form.errors.currency"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="amount" :value="$t('public.Deposit Amount') + ' (USD)'" />
                        <Input id="amount" type="number" min="30" class="block w-full px-4" :placeholder="$t('public.Deposit Amount')" v-model="form.amount" @change="form.validate('amount')" />
                        <InputError :message="form.errors.amount"/>
                    </div>
                </div>
            </div>

            <!-- FPX -->
            <div v-if="form.deposit_method === 'fpx'">
                <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.FPX') }}</h2>
                <hr>
                <div class="grid gap-6 my-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <Label for="account_no" :value="$t('public.Account No')" />
                        <InputSelect class="w-full" id="account_no" v-model="form.account_no" :placeholder="$t('public.Select Account No')" >
                            <option v-for="paymentAccount in getPaymentAccount" :value="paymentAccount.meta_login" :key="paymentAccount.id">{{ paymentAccount.meta_login }}</option>
                        </InputSelect>
                        <InputError :message="form.errors.account_no"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="currency" :value="$t('public.Currency')" />
                        <InputSelect v-model="form.currency" class="w-full" id="currency" :placeholder="$t('public.Select Currency')">
                            <option value="MYR">MYR</option>
                        </InputSelect>
                        <InputError :message="form.errors.currency"/>
                    </div>
                    <div class="space-y-2">
                        <Label for="amount" :value="$t('public.Deposit Amount') + ' (USD)'" />
                        <Input id="amount" type="number" min="30" class="block w-full px-4" :placeholder="$t('public.Deposit Amount')" v-model="form.amount" @change="form.validate('amount')" />
                        <InputError :message="form.errors.amount"/>
                    </div>
                </div>
            </div>

<!--            <div v-if="form.deposit_method === 2">-->
<!--                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Cryptocurrency</h2>-->
<!--                <hr>-->
<!--                <p class="my-4 text-sm text-gray-600 dark:text-gray-400">-->
<!--                    <span class="text-red-500">*</span> Select a coin-->
<!--                </p>-->
<!--                <ul class="my-4 grid w-full gap-6 md:grid-cols-3">-->
<!--                    <li v-for="(cryptoWallet, index) in cryptoWallets" :key="index">-->
<!--                        <input-->
<!--                            type="radio"-->
<!--                            :id="`account_platform_${cryptoWallet.id}`"-->
<!--                            name="account_platform"-->
<!--                            :value="cryptoWallet.id"-->
<!--                            class="hidden peer"-->
<!--                            v-model="form.account_platform"-->
<!--                            :required="index === 1"-->
<!--                            @change="selectedAccountPlatform = form.account_platform"-->
<!--                        >-->
<!--                        <label-->
<!--                            :for="`account_platform_${cryptoWallet.id}`"-->
<!--                            class="inline-flex items-center justify-center w-full p-4 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600"-->
<!--                        >-->
<!--                            <div class="flex flex-col items-center gap-2" v-for="cryptoMedia in getCryptoMediaByCollection(cryptoWallet.media, 'setting_crypto_wallet')" :key="cryptoMedia.id">-->
<!--                                <img class="object-cover" :src="cryptoMedia.original_url" :alt="cryptoMedia.alt">-->
<!--                                <p class="text-sm dark:text-white">{{ cryptoWallet.name }} ({{ cryptoWallet.symbol }})</p>-->
<!--                            </div>-->
<!--                        </label>-->
<!--                    </li>-->
<!--                </ul>-->
<!--                <div v-if="isLoading">Loading...</div>-->
<!--                <div v-if="error">{{ error }}</div>-->
<!--                <div v-for="(cryptoWallet, index) in cryptoWallets" :key="index">-->
<!--                    <div v-if="selectedAccountPlatform === cryptoWallet.id">-->
<!--                        <div class="flex flex-col items-center gap-4 my-4">-->
<!--                            <qrcode-vue :class="['border-4 border-white']" :value="cryptoWallet.wallet_address" :size="100"></qrcode-vue>-->
<!--                            <p class="flex gap-3 text-sm dark:text-gray-400">-->
<!--                                {{ cryptoWallet.wallet_address }}-->
<!--                                <input type="hidden" id="cryptoWalletAddress" :value="cryptoWallet.wallet_address">-->
<!--                                <DuplicateIcon aria-hidden="true" :class="['w-5 dark:text-white']" @click.stop.prevent="copyTestingCode" style="cursor: pointer" />-->
<!--                            </p>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->

<!--                <div class="grid gap-6 mb-6 md:grid-cols-2">-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="account_platform" value="Account Platform" />-->
<!--                        <InputSelect class="w-full" id="account_platform" placeholder="Select Account Platform"/>-->
<!--                    </div>-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="account_no" value="Account No." />-->
<!--                        <InputSelect class="w-full" id="account_no" placeholder="Select Account No." />-->
<!--                    </div>-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="amount" value="Deposit Amount" />-->
<!--                        <Input id="amount" type="text" class="block w-full px-4" placeholder="Deposit Amount" v-model="form.amount" @change="form.validate('amount')" />-->
<!--                        <InputError :message="form.errors.amount"/>-->
<!--                    </div>-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="txid" value="TxID" />-->
<!--                        <Input id="txid" type="text" class="block w-full px-4" placeholder="Paste TxID from Payment Receipt" v-model="form.txid" />-->
<!--                        <InputError :message="form.errors.txid"/>-->
<!--                    </div>-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="payment_receipt" value="Payment Receipt" />-->
<!--                        <input type="file" id="payment_receipt" @change="handlePaymentReceipt" class="block border border-gray-400 w-full rounded-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1"/>-->
<!--                        <InputError :message="form.errors.payment_receipt"/>-->
<!--                    </div>-->
<!--                    <div class="space-y-2">-->
<!--                        <Label for="description" value="Description" />-->
<!--                        <Input id="description" type="text" class="block w-full px-4" placeholder="Description" v-model="form.description" />-->
<!--                        <InputError :message="form.errors.description"/>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->

            <div class="mt-6 flex justify-end" v-if="form.deposit_method">
                <Button variant="secondary" @click="closeModal">
                    {{ $t('public.Cancel') }}
                </Button>

                <Button
                    variant="primary"
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="submit"
                >
                    {{ $t('public.Process') }}
                </Button>
            </div>
        </form>
    </Modal>
</template>
