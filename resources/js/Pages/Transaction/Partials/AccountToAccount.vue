<script setup>
import InputError from "@/Components/InputError.vue";
import InputSelect from "@/Components/InputSelect.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

defineProps({
    tradingUsers: Object
})

const form = useForm({
    account_no_1: '',
    account_no_2: '',
    amount: '',
});

const submit = () => {
    form.post(route('account_to_account'), {
        onSuccess: () => {
            form.reset();
        },
    });
};

</script>

<template>
    <form @submit.prevent="submit">
        <div class="grid gap-6 my-6 md:grid-cols-1">
            <div class="space-y-2">
                <Label for="account_no_1" value="Account To Transfer" />
                <InputSelect class="w-full" id="account_no_1" v-model="form.account_no_1" placeholder="Select Account No." >
                    <option v-for="paymentAccount in tradingUsers" :value="paymentAccount.meta_login" :key="paymentAccount.id">{{ paymentAccount.meta_login }} ( $ {{ paymentAccount.balance }} )</option>
                </InputSelect>
                <InputError :message="form.errors.account_no_1"/>
            </div>
            <div class="space-y-2">
                <Label for="account_no_2" value="Account To Receive" />
                <InputSelect class="w-full" id="account_no_2" v-model="form.account_no_2" placeholder="Select Account No." >
                    <option v-for="paymentAccount in tradingUsers" :value="paymentAccount.meta_login" :key="paymentAccount.id">{{ paymentAccount.meta_login }} ( $ {{ paymentAccount.balance }} )</option>
                </InputSelect>
                <InputError :message="form.errors.account_no_2"/>
            </div>
            <div class="space-y-2">
                <Label for="amount" value="Amount ($)" />
                <Input id="amount" type="number" step=".01" class="block w-full px-4" placeholder="0.00" v-model="form.amount" />
                <InputError :message="form.errors.amount"/>
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <Button :disabled="form.processing">
                Submit
            </Button>
        </div>
    </form>
</template>
