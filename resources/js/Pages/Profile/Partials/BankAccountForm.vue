<script setup>
import Input from "@/Components/Input.vue";
import Label from "@/Components/Label.vue";
import InputSelect from "@/Components/InputSelect.vue";
import InputError from "@/Components/InputError.vue";
import {router, useForm} from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";

const props = defineProps({
    countries: Object
})

const form = useForm({
    payment_platform: 'bank',
    payment_platform_name: '',
    bank_branch_address: '',
    payment_account_name: '',
    account_no: '',
    bank_swift_code: '',
    bank_code_type: '',
    bank_code: '',
    country: '',
    currency: '',
    proof_of_bank: null,
})

const submit = () => {
    form.post(route('profile.create_payment_account'));
};

const back = () => {
    router.get('/profile/detail')
}

</script>

<template>
    <form>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-2">
                <Label for="payment_platform_name" value="Bank Name" />

                <Input
                    id="payment_platform_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.payment_platform_name"
                    autocomplete="bank_name"
                />

                <InputError class="mt-2" :message="form.errors.payment_platform_name" />
            </div>
            <div class="space-y-2">
                <Label for="bank_branch_address" value="Bank Branch Address" />

                <Input
                    id="bank_branch_address"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_branch_address"
                    autocomplete="bank_branch_address"
                />

                <InputError class="mt-2" :message="form.errors.bank_branch_address" />
            </div>
            <div class="space-y-2">
                <Label for="payment_account_name" value="Bank Account Holder Name" />

                <Input
                    id="payment_account_name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.payment_account_name"
                    autocomplete="payment_account_name"
                />

                <InputError class="mt-2" :message="form.errors.payment_account_name" />
            </div>
            <div class="space-y-2">
                <Label for="account_no" value="Account No." />

                <Input
                    id="account_no"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.account_no"
                    autocomplete="account_no"
                />

                <InputError class="mt-2" :message="form.errors.account_no" />
            </div>
            <div class="space-y-2">
                <Label for="bank_swift_code" value="Bank Swift Code" />

                <Input
                    id="bank_swift_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_swift_code"
                    autocomplete="bank_swift_code"
                />

                <InputError class="mt-2" :message="form.errors.bank_swift_code" />
            </div>
            <div class="space-y-2">
                <Label for="bank_code">Bank Code</Label>

                <Input
                    id="bank_code"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_code"
                    autocomplete="bank_code"
                />

                <InputError class="mt-2" :message="form.errors.bank_code" />
            </div>
            <div class="space-y-2">
                <Label for="bank_code_type">ABA / IBAN</Label>

                <Input
                    id="bank_code_type"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.bank_code_type"
                    autocomplete="bank_code_type"
                />

                <InputError class="mt-2" :message="form.errors.bank_code_type" />
            </div>
            <div class="space-y-2">
                <Label for="country" value="Country" />

                <InputSelect v-model="form.country" class="block w-full text-sm" placeholder="Select a country">
                    <option v-for="country in props.countries" :value="country.name_en" :key="country.id">{{ country.name_en }}</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.country" />
            </div>
            <div class="space-y-2">
                <Label for="currency" value="Your Country Currency" />

                <InputSelect v-model="form.currency" class="block w-full text-sm" placeholder="Select a currency">
                    <option value="VND">VND</option>
                    <option value="MYR">MYR</option>
                </InputSelect>

                <InputError class="mt-2" :message="form.errors.currency" />
            </div>
            <div class="space-y-2">
                <Label for="proof_of_bank" value="Proof of Bank Account" />

                <input
                    type="file"
                    id="proof_of_bank"
                    accept="image/*,.pdf"
                    @input="form.proof_of_bank = $event.target.files[0]"
                    class="block border border-gray-400 w-full rounded-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                />

                <InputError class="mt-2" :message="form.errors.proof_of_bank" />
            </div>
        </div>
        <div class="flex justify-end gap-4 mt-6">
            <div class="grid grid-cols-2 gap-4 float-right">
                <Button variant="danger" class="px-6 justify-center" @click.prevent="back">
                    Cancel
                </Button>
                <Button class="justify-center" @click="submit" :disabled="form.processing">Save</Button>
            </div>
        </div>
    </form>
</template>
