<script setup>
import {TrashIcon} from "@/Components/Icons/outline.jsx";
import Button from "@/Components/Button.vue";
import {nextTick, ref} from "vue";
import Modal from "@/Components/Modal.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    bank: Object
})

const deleteAccountConfirm = ref(false);

const confirmDeletion = () => {
    deleteAccountConfirm.value = true
}

const form = useForm({
    id: props.bank.id,
})

const deleteAccount = () => {
    form.delete(route('profile.payment_delete'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
        },
    })
}

const closeModal = () => {
    deleteAccountConfirm.value = false
}
</script>

<template>
    <Button
        class="justify-center px-4 pt-2 mx-1 mt-1 rounded-full w-8 h-8 focus:outline-none"
        variant="danger-opacity"
        @click="confirmDeletion"
    >
        <TrashIcon aria-hidden="true" class="w-6 h-6 absolute" />
        <span class="sr-only">Delete</span>
    </Button>

    <Modal :show="deleteAccountConfirm" @close="closeModal" max-width="2xl">
        <div class="p-6">
            <div class="flex justify-center">
                <svg width="70" height="94" viewBox="0 0 70 94" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M63.2543 26.0568C54.0617 26.0098 44.8692 26.0474 35.6861 26.0474C25.965 26.0474 16.2345 25.9816 6.51339 26.085C1.66229 26.132 0.510863 27.4009 1.16208 32.1192C3.7292 50.739 6.40958 69.3399 9.06164 87.9408C9.76948 92.9223 11.0059 93.975 16.0646 93.9844C29.1173 94.0032 42.1699 94.0032 55.2226 93.9938C60.8287 93.9938 62.0934 92.6121 62.7541 87.1136C64.8871 69.2365 67.105 51.3687 69.4361 33.5197C70.2478 27.3445 69.4361 26.0944 63.2543 26.0568ZM20.4438 84.1623C17.2726 84.5477 16.0835 82.0099 15.7626 79.3406C14.2242 66.4731 12.7802 53.5869 11.289 40.7195C10.9964 38.1629 11.7514 36.0011 14.4224 35.5593C17.367 35.0706 18.8016 37.1572 19.1508 39.7607C20.1607 47.3647 21.0384 54.9874 21.9444 62.6007C22.5673 67.8548 23.1619 73.1089 23.7659 78.363C24.0491 81.1264 23.6527 83.777 20.4438 84.1623ZM39.4046 79.303C39.3952 81.9253 38.3004 84.3033 35.4029 84.2563C32.2884 84.1999 31.439 81.7655 31.4673 78.9176C31.5334 72.5732 31.4862 66.2193 31.4862 59.8749H31.4201C31.4201 53.5305 31.4484 47.1767 31.4107 40.8323C31.3918 38.1065 31.8637 35.4653 35.1009 35.2962C38.357 35.127 39.3008 37.7305 39.3197 40.4281C39.4235 53.3801 39.4329 66.3415 39.4046 79.303ZM59.8755 41.0766C58.3183 53.9441 56.6855 66.7927 55.015 79.6507C54.6752 82.2449 53.2878 84.4725 50.4187 84.0589C47.3325 83.6078 46.804 81.0888 47.1909 78.2691C48.0592 71.981 48.7576 65.6742 49.5693 59.3768C50.3809 53.0793 51.2115 46.7913 51.976 40.4845C52.3063 37.7775 53.1085 35.221 56.3457 35.4559C59.5924 35.6909 60.1964 38.3979 59.8755 41.0766ZM27.8431 8.13262C29.4287 0.246737 30.2498 -0.570988 35.5634 0.256137C44.6049 1.66601 44.8409 1.95738 43.6611 10.7268C50.6452 11.9581 57.6387 13.2928 64.67 14.3925C67.7184 14.8718 70.4554 15.5767 69.9364 19.3364C69.3795 23.3592 66.1046 22.8047 63.3676 22.3535C47.1343 19.6466 30.9105 16.8268 14.6867 14.0447C11.1569 13.4337 7.63651 12.7288 4.08784 12.2025C1.23758 11.7795 -0.404618 10.3038 0.0861547 7.41829C0.595803 4.45755 2.87035 3.79022 5.55072 4.25077C12.9878 5.51966 20.4155 6.83554 27.8431 8.13262Z" fill="#FF3F34"/>
                </svg>

            </div>
            <div class="mt-6 text-center">
                <h1 class="mb-4 text-2xl font-bold leading-none tracking-tight text-gray-900 md:text-2xl lg:text-4xl dark:text-white" style="font-family: Montserrat,sans-serif">
                    Delete Payment Account?
                </h1>
                <p class="dark:text-dark-eval-3">
                    Please note that your payment account will be deleted permanently. Are you sure to delete the payment account?
                </p>
            </div>
            <div class="mt-6 flex gap-4 justify-center">
                <Button variant="primary" class="px-6" @click="closeModal">
                    Cancel
                </Button>
                <Button class="px-6" variant="danger" @click.prevent="deleteAccount">Delete</Button>
            </div>
        </div>
    </Modal>
</template>
