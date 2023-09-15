<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from "@/Components/Button.vue";
import {PlusCircleIcon} from "@heroicons/vue/solid";
import TradingAccountTable from "@/Pages/AccountInfo/partials/TradingAccountTable.vue";
import {nextTick, ref} from "vue";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";
import ToastList from "@/Components/ToastList.vue";
import Checkbox from "@/Components/Checkbox.vue";
import CreateTradingAccount from "@/Pages/AccountInfo/partials/CreateTradingAccount.vue";

defineProps({
    accountTypes: Object,
    leverages: Object,
});

</script>

<template>
    <AuthenticatedLayout title="Account Info">
        <template #header>
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Account Info
                </h2>

                <CreateTradingAccount
                    :accountTypes="accountTypes"
                    :leverages="leverages"
                />
            </div>
        </template>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            <img src="/assets/platform/icon/ctrader.png" style="width: 160px" alt="Ctrader">
            <TradingAccountTable
                :leverages="leverages"
            />
        </div>

    </AuthenticatedLayout>
</template>
