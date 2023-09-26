<script setup>
import InputSelect from "@/Components/InputSelect.vue";
import Checkbox from "@/Components/Checkbox.vue";
import Button from "@/Components/Button.vue";
import {PlusCircleIcon} from "@heroicons/vue/solid";
import Modal from "@/Components/Modal.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import {ref} from "vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    accountTypes: Object,
    leverages: Object,
})

const platforms = [
    { id: 'account_platform_2', src: '/assets/platform/icon/metatrader5.png', value: 2 },
    { id: 'account_platform_3', src: '/assets/platform/icon/ctrader.png', value: 3 },
    { id: 'account_platform_4', src: '/assets/platform/icon/match_trade.png', value: 4 },
];

const acc_types = [
    // { id: 'account_type_2', src: '/assets/account_type/ecn.png', value: 2 },
    { id: 'account_type_3', src: '/assets/account_type/standard.png', value: 1 },
    // { id: 'account_type_4', src: '/assets/account_type/standard_islam.png', value: 4 },
];

const addingTradingAccount = ref(false)
const passwordInput = ref(null)

const form = useForm({
    group: '',
    // account_platform: '',
    leverage: '',
    currency: '',
    terms: '',
})

const addTradingAccount = () => {
    addingTradingAccount.value = true
}

const addNewAccount = () => {
    form.post(route('account_info.add_trading_account'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            form.reset();
        },
    })
}

const closeModal = () => {
    addingTradingAccount.value = false

    form.reset()
}
</script>

<template>
    <Button
        external
        variant="primary-opacity"
        target="_blank"
        class="items-center gap-2 max-w-xs"
        v-slot="{ iconSizeClasses }"
        @click="addTradingAccount"
    >
        <PlusCircleIcon aria-hidden="true" :class="iconSizeClasses" />
        <span>{{ $t('public.Add Trading Account') }}</span>
    </Button>

    <Modal :show="addingTradingAccount" @close="closeModal">
        <div class="p-6">
            <h2
                class="text-lg font-medium mb-2 text-gray-900 dark:text-gray-100"
            >
                {{ $t('public.Add Trading Account') }}
            </h2>
            <hr>
            <div class="mt-6 space-y-2">
                <Label for="group" :value="$t('public.Select Account Type')" />
                <ul class="grid w-full gap-6 md:grid-cols-3">
                    <li v-for="(type, index) in acc_types" :key="index">
                        <input type="radio" :id="type.id" name="group" :value="accountTypes.id" class="hidden peer" v-model="form.group" :required="type.required">
                        <label :for="type.id" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">
                            <div class="block">
                                <img class="object-cover" :src="type.src" alt="account_type">
                            </div>
                        </label>
                    </li>
                </ul>
                <InputError :message="form.errors.group"/>
            </div>

            <!--                        <div class="mt-6 space-y-2">-->
            <!--                            <Label for="account_platform" value="Select Account Platform" />-->
            <!--                            <ul class="grid w-full gap-6 md:grid-cols-3">-->
            <!--                                <li v-for="(platform, index) in platforms" :key="index">-->
            <!--                                    <input type="radio" :id="platform.id" name="account_platform" :value="platform.value" class="hidden peer" v-model="form.account_platform" :required="platform.required">-->
            <!--                                    <label :for="platform.id" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">-->
            <!--                                        <div class="block">-->
            <!--                                            <img class="object-cover" :src="platform.src" alt="account_platform">-->
            <!--                                        </div>-->
            <!--                                    </label>-->
            <!--                                </li>-->
            <!--                            </ul>-->
            <!--                            <InputError :message="form.errors.account_platform"/>-->
            <!--                        </div>-->

            <div class="mt-6 space-y-2">
                <Label for="leverage" :value="$t('public.Trading Account Leverage')" />
                <InputSelect v-model="form.leverage" class="block w-full text-sm" :placeholder="$t('public.Choose Leverage')">
                    <option v-for="leverage in leverages" :value="leverage.value" :key="leverage.id">{{ leverage.leverage }}</option>
                </InputSelect>
                <InputError :message="form.errors.leverage"/>
            </div>

            <div class="mt-6 space-y-2">
                <Label for="currency" value="Currency" />
                <InputSelect v-model="form.currency" class="block w-full text-sm" :placeholder="$t('public.Choose Currency')">
                    <option value="USD">USD</option>
                </InputSelect>
                <InputError :message="form.errors.leverage"/>
            </div>

            <div class="mt-6 space-y-4">
                <h3 class="text-[#989898] font-bold">{{ $t('public.Terms & Conditions') }}</h3>
                <ol class="text-[#989898] text-sm list-decimal text-justify pl-6 mt-2">
                    <li>{{ $t('public.Terms 1') }}</li>
                    <li>{{ $t('public.Terms 2') }}</li>
                    <li>{{ $t('public.Terms 3') }}</li>
                    <li>{{ $t('public.Terms 4') }}</li>
                    <li>{{ $t('public.Terms 5') }}</li>
                    <li>{{ $t('public.Terms 6') }}</li>
                    <li>{{ $t('public.Terms 7') }}</li>
                    <li>{{ $t('public.Terms 8') }}</li>
                </ol>

                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <Checkbox v-model="form.terms"/>
                    </div>
                    <div class="ml-3">
                        <label for="terms" class="text-gray-500 dark:text-dark-eval-4">{{ $t('public.I acknowledge that I have read, and do hereby accept the terms and conditions stated as above.') }}</label>
                    </div>
                </div>
                <InputError :message="form.errors.terms"/>

            </div>

            <div class="mt-6 flex justify-end">
                <Button variant="secondary" @click="closeModal">
                    {{ $t('public.Cancel') }}
                </Button>

                <Button
                    variant="primary"
                    class="ml-3"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    @click="addNewAccount"
                >
                    {{ $t('public.Process') }}
                </Button>
            </div>
        </div>
    </Modal>
</template>
