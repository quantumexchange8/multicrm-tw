<script setup>
import {DepositIcon, ResetIcon, SettingIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import {nextTick, ref} from "vue";
import {useForm} from "@inertiajs/vue3";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Checkbox from "@/Components/Checkbox.vue";
import { TailwindPagination } from 'laravel-vue-pagination';
import {library} from "@fortawesome/fontawesome-svg-core";
import {faRotateRight} from "@fortawesome/free-solid-svg-icons";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import Loading from "@/Components/Loading.vue";
library.add(faRotateRight);

const settingLeverage = ref(false);
const passwordInput = ref(null);
const id = ref('');

const props = defineProps({
    leverages: Object,
});

const form = useForm({
    account_no: '',
    leverage: '',
    terms: '',
});

const openSettingModal = (account_no, leverage, tradingAccount) => {
    id.value = tradingAccount;

    form.account_no = account_no;
    form.leverage = leverage;
    settingLeverage.value = true
}

const addNewAccount = () => {
    form.post(route('change_leverage'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    })
}

const closeModal = () => {
    settingLeverage.value = false

    form.reset()
}

const tradingAccounts = ref({data: []});
const isLoading = ref(false);
const getResults = async (page = 1) => {
    isLoading.value = true;
    try {
        let url = `/account_info/getTradingAccounts?page=${page}`;

        const response = await axios.get(url);
        tradingAccounts.value = response.data;
    } catch (error) {
        console.error(error);
    } finally {
        isLoading.value = false;
    }
}

getResults()

function refreshTable() {
    getResults()
}

const paginationClass = [
    'bg-transparent border-0 text-gray-500'
];

const paginationActiveClass = [
    'dark:bg-transparent border-0 text-[#FF9E23] dark:text-[#FF9E23]'
];
</script>
<template>
    <div class="flex justify-end">
        <font-awesome-icon
            icon="fa-solid fa-rotate-right"
            class="flex-shrink-0 w-5 h-5 cursor-pointer dark:text-dark-eval-4"
            aria-hidden="true"
            @click="refreshTable"
        />
    </div>
    <div v-if="isLoading" class="w-full flex justify-center mt-8">
        <Loading />
    </div>
    <div v-else class="relative overflow-x-auto sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs font-bold text-gray-700 uppercase bg-gray-50 dark:bg-transparent dark:text-white text-center">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Account No.
                </th>
                <th scope="col" class="px-6 py-3">
                    Account Type
                </th>
                <th scope="col" class="px-6 py-3">
                    Balance
                </th>
                <th scope="col" class="px-6 py-3">
                    Equity
                </th>
                <th scope="col" class="px-6 py-3">
                    Credit
                </th>
                <th scope="col" class="px-6 py-3">
                    Leverage
                </th>
<!--                <th scope="col" class="px-6 py-3">-->
<!--                    Actions-->
<!--                </th>-->
            </tr>
            </thead>
            <tbody>
            <tr v-for="tradingAccount in tradingAccounts.data" class="bg-white odd:dark:bg-transparent even:dark:bg-dark-eval-0 text-xs font-thin text-gray-900 dark:text-white text-center">
                <th scope="row" class="px-6 py-4 font-thin rounded-l-full">
                    {{ tradingAccount.meta_login }}
                </th>
                <td class="px-6 py-4">
                    {{ tradingAccount.account_type.name }}
                </td>
                <td class="px-6 py-4">
                    {{ tradingAccount.balance }}
                </td>
                <td class="px-6 py-4">
                    {{ tradingAccount.equity }}
                </td>
                <td class="px-6 py-4">
                    {{ tradingAccount.credit }}
                </td>
                <td class="px-6 py-4 rounded-r-full">
                    1:{{ tradingAccount.margin_leverage }}
                </td>
<!--                <td class="px-6 py-4 flex justify-center gap-2">-->
<!--                    <Button class="justify-center pl-4 pr-3 pt-1 rounded-full w-8 h-8 focus:outline-none" variant="primary-opacity" @click="openDepositModal">-->
<!--                        <DepositIcon aria-hidden="true" class="w-6 h-6 absolute" />-->
<!--                        <span class="sr-only">Deposit</span>-->
<!--                    </Button>-->
<!--                    <Button class="justify-center px-4 pt-2 rounded-full w-8 h-8 focus:outline-none" variant="primary-opacity" @click="openResetModal">-->
<!--                        <ResetIcon aria-hidden="true" class="w-6 h-6 absolute" />-->
<!--                        <span class="sr-only">Reset</span>-->
<!--                    </Button>-->
<!--                    <Button class="justify-center px-4 pt-2 rounded-full w-8 h-8 focus:outline-none" variant="primary-opacity" @click="openSettingModal(tradingAccount.meta_login, tradingAccount.margin_leverage, tradingAccount.id)">-->
<!--                        <SettingIcon aria-hidden="true" class="w-6 h-6 absolute" />-->
<!--                        <span class="sr-only">Setting</span>-->
<!--                    </Button>-->
<!--                </td>-->
            </tr>
            </tbody>
        </table>
        <div class="flex justify-end mt-4">
            <TailwindPagination
                :item-classes=paginationClass
                :active-classes=paginationActiveClass
                :data="tradingAccounts"
                @pagination-change-page="getResults"
            />
        </div>
    </div>

    <Modal :show="settingLeverage" @close="closeModal">
        <div class="p-6">
            <h2
                class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
            >
                Change Leverage
            </h2>
            <hr>

            <div class="mt-6 space-y-2">
                <Label for="account_no" value="Account No" />
                <Input id="account_no" type="text" class="block w-full px-4" readonly placeholder="Account No" v-model="form.account_no" autofocus />
                <InputError :message="form.errors.account_no"/>
            </div>

            <div class="mt-6 space-y-2">
                <Label for="leverage" value="New Leverage" />
                <InputSelect v-model="form.leverage" class="block w-full text-sm" placeholder="Choose Leverages">
                    <option v-for="leverage in leverages" :value="leverage.value" :key="leverage.id">{{ leverage.leverage }}</option>
                </InputSelect>
                <InputError :message="form.errors.leverage"/>
            </div>

            <div class="mt-6 space-y-4">
                <h3 class="text-[#989898] font-bold">Terms & Conditions</h3>
                <p class="text-[#989898] text-justify">
                    By submitting this request I acknowledge that I am aware that high leverage carries a large amount of risk to my capital and there is a possibility I could sustain a loss greater than and not limited to the capital I have deposited.
                </p>
                <p class="text-[#989898] text-justify">
                    I agree that the products issuer is entitled to review my trading activities and adjust the leverage levels on my trading account at its discretion and without any prior warnings or notifications.
                </p>
                <p class="text-[#989898] text-justify">
                    I understand that by choosing high leverage, I can trade with bigger margin but potentially incur significant losses.
                </p>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <Checkbox v-model="form.terms"/>
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-gray-500 dark:text-dark-eval-4">I acknowledge that I have read, and do hereby accept the terms and conditions stated as above.</label>
                    </div>
                </div>
                <InputError :message="form.errors.terms"/>

            </div>

            <div class="mt-6 flex justify-end">
                <Button variant="secondary" @click="closeModal">
                    Cancel
                </Button>

                <Button
                    variant="primary"
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="addNewAccount"
                >
                    Process
                </Button>
            </div>
        </div>
    </Modal>
</template>
