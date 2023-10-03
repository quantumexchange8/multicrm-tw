<script setup>
import {Link, useForm} from '@inertiajs/vue3'
import Button from '@/Components/Button.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import ToastList from "@/Components/ToastList.vue";
import {transactionFormat} from "@/Composables/index.js";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {faCopy} from "@fortawesome/free-solid-svg-icons";
import {library} from "@fortawesome/fontawesome-svg-core";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import toast from "@/Composables/toast.js";
library.add(faCopy);

const { formatDate, formatAmount } = transactionFormat();
const receiptModal = ref(false);
const openReceiptModal = () => {
    receiptModal.value = true
}
const props = defineProps({
    payment: Object,
    receipt: String,
})

const updateStatus = (status) => {
    form.status = status;
};

const form = useForm({
    id: props.payment.id,
    status: ''
})

const submit = () => {
    form.post(route('deposit_approval'))
}

const inputToCopy = ref(null);

const copyAddress = () => {
    if (inputToCopy.value) {
        const textArea = document.createElement('textarea');
        textArea.value = props.payment.TxID;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);

        toast.add({
            message: "Copied",
        });
    }
}

const closeModal = () => {
    receiptModal.value = false
}
</script>

<template>
    <GuestLayout title="Deposit Approval">

        <form @submit.prevent="submit" class="space-y-2">
            <h2 class="text-lg mb-6 font-medium text-gray-900 dark:text-gray-100">{{ $t('public.Deposit') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center md:text-left">
                <div class="text-black dark:text-dark-eval-3">{{ $t('public.Name') }}</div>
                <div class="text-black dark:text-white">{{ payment.of_user.first_name }}</div>

                <div class="text-black dark:text-dark-eval-3">{{ $t('public.Date') }}</div>
                <div class="text-black dark:text-white">{{ formatDate(payment.created_at) }}</div>

                <div class="text-black dark:text-dark-eval-3">{{ $t('public.Payment ID') }}</div>
                <div class="text-black dark:text-white">{{ payment.payment_id }}</div>

                <div class="text-black dark:text-dark-eval-3">{{ $t('public.Amount') }}</div>
                <div class="text-black dark:text-white">$ {{ payment.amount }}</div>

                <div class="text-black dark:text-dark-eval-3">{{ $t('public.TxID')}}</div>
                <div class="inline-flex items-center text-black dark:text-white">
                    <span ref="inputToCopy">{{ payment.TxID }}</span>
                    <button type="button" class="text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm">
                        <font-awesome-icon
                            icon="fa-solid fa-copy"
                            class="flex-shrink-0 w-4 h-4 cursor-pointer"
                            aria-hidden="true"
                            @click.stop.prevent="copyAddress"
                        />
                    </button>
                </div>

                <div class="text-black dark:text-dark-eval-3">{{ $t('public.Payment Receipt')}}</div>
                <div class="text-blue-500 hover:text-blue-600 cursor-pointer" @click="openReceiptModal">
                    {{ $t('public.Click to view') }}
                </div>
            </div>
            <div class="py-6 flex justify-center gap-6">
                <Button
                    v-model="form.status"
                    variant="success"
                    class="text-xs justify-center"
                    @click="updateStatus('approve')"
                >
                    Approve
                </Button>
                <Button
                    v-model="form.status"
                    variant="danger"
                    class="text-xs justify-center"
                    @click="updateStatus('reject')"
                >
                    Reject
                </Button>
            </div>
        </form>

        <Modal :show="receiptModal" @close="closeModal">
            <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white">{{ $t('public.Payment Receipt')}}</h3>
                    <div class="flex justify-center">
                        <img class="rounded" :src="receipt" alt="Payment Receipt">
                    </div>
                </div>
            </div>
        </Modal>

    </GuestLayout>
</template>
