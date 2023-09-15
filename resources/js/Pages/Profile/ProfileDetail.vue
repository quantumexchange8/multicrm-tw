<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from "@/Components/Button.vue";
import Label from "@/Components/Label.vue";
import Input from "@/Components/Input.vue";
import {Link, usePage} from "@inertiajs/vue3";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import UpdatePasswordForm from "@/Pages/Profile/Partials/UpdatePasswordForm.vue";
import BankAccountList from "@/Pages/Profile/Partials/BankAccountList.vue";
import CryptoAccountList from "@/Pages/Profile/Partials/CryptoAccountList.vue";

defineProps({
    status: String,
    countries: Object,
    avatar: String,
    frontIdentity: String,
    backIdentity: String
})
const frontIdentityModal = ref(false);
const backIdentityModal = ref(false);
const resetPasswordModal = ref(false);
const user = usePage().props.auth.user
const openFrontIdentityModal = () => {
    frontIdentityModal.value = true
}

const openBackIdentityModal = () => {
    backIdentityModal.value = true
}

const openResetPasswordModal = () => {
    resetPasswordModal.value = true
}

const closeModal = () => {
    frontIdentityModal.value = false
    backIdentityModal.value = false
    resetPasswordModal.value = false
}

</script>

<template>
    <AuthenticatedLayout title="User Profile">
        <template #header>
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight"
            >
                User Profile
            </h2>
        </template>

        <div class="flex items-center justify-between gap-4">
            <img
                :src="avatar ? avatar : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                alt="Avatar"
                class="w-24 h-24 rounded-full shrink-0 grow-0 object-cover"
            >
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <Link
                    :href="route('profile.edit')"
                    class="inline-flex p-2 px-6 justify-center text-center items-center transition-colors text-sm font-medium rounded-full select-none disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-dark-eval-2 bg-[#007bff33] text-[#007BFF] hover:bg-blue-800 hover:text-white hover:text-white focus:ring-blue-500"
                >
                    Edit Profile
                </Link>
                <Button
                    variant="primary-opacity"
                    class="justify-center"
                    @click="openResetPasswordModal"
                >
                    Reset Portal Password
                </Button>
                <Link
                    :href="route('profile.payment_account')"
                    class="inline-flex p-2 px-6 justify-center text-center items-center transition-colors text-sm font-medium rounded-full select-none disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring focus:ring-offset-2 focus:ring-offset-white dark:focus:ring-offset-dark-eval-2 bg-[#007bff33] text-[#007BFF] hover:bg-blue-800 hover:text-white hover:text-white focus:ring-blue-500"
                >
                    Create Payment Account
                </Link>
            </div>
        </div>

        <div class="mt-8">
            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4"
            >
                Personal Information
            </h2>
            <div class="p-4 sm:p-8 bg-white dark:bg-dark-eval-1 shadow sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <Label for="first_name">Full Name</Label>

                        <Input
                            id="first_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="user.first_name"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="chinese_name">Chinese Name</Label>

                        <Input
                            id="chinese_name"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="user.chinese_name"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="dob">Date Of Birth</Label>

                        <Input
                            id="dob"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="user.dob"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="email">Email</Label>

                        <Input
                            id="email"
                            type="email"
                            class="mt-1 block w-full"
                            v-model="user.email"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="country">Country</Label>

                        <Input
                            id="country"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="user.country"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="phone">Mobile Phone</Label>

                        <Input
                            id="phone"
                            type="text"
                            class="mt-1 block w-full"
                            v-model="user.phone"
                            disabled
                        />

                    </div>
                    <div class="space-y-2">
                        <Label for="front_identity">Proof of Identity (FRONT)</Label>

                        <Input
                            v-if="frontIdentity"
                            id="front_identity"
                            type="text"
                            class="mt-1 block w-full cursor-pointer text-blue-500 dark:text-blue-500 hover:underline bg-white dark:bg-dark-eval-0 dark:border-transparent"
                            value="Click to view"
                            @click="openFrontIdentityModal"
                            readonly
                        />
                        <Input
                            v-else
                            id="front_identity"
                            type="text"
                            class="mt-1 block w-full"
                            value="Pending KYC"
                            disabled
                        />

                        <Modal :show="frontIdentityModal" @close="closeModal">
                            <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white"> Proof of Identity (Front)</h3>
                                    <div class="flex justify-center">
                                        <img class="rounded" :src="frontIdentity" alt="Proof of Identity (Front)">
                                    </div>
                                </div>
                            </div>
                        </Modal>
                    </div>
                    <div class="space-y-2">
                        <Label for="back_identity">Proof of Identity (BACK)</Label>
                        <Input
                            v-if="backIdentity"
                            id="back_identity"
                            type="text"
                            class="mt-1 block w-full cursor-pointer text-blue-500 dark:text-blue-500 hover:underline bg-white dark:bg-dark-eval-0 dark:border-transparent"
                            value="Click to view"
                            @click="openBackIdentityModal"
                            readonly
                        />
                        <Input
                            v-else
                            id="back_identity"
                            type="text"
                            class="mt-1 block w-full"
                            value="Pending KYC"
                            disabled
                        />

                        <Modal :show="backIdentityModal" @close="closeModal">
                            <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-gray-900 dark:text-white"> Proof of Identity (Back)</h3>
                                    <div class="flex justify-center">
                                        <img class="rounded" :src="backIdentity" alt="Proof of Identity (Back)">
                                    </div>
                                </div>
                            </div>
                        </Modal>

                    </div>
                </div>
            </div>
        </div>

        <div class="mt-8 space-y-6">

            <h2
                class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4"
            >
                Payment Account
            </h2>
            <BankAccountList />
            <CryptoAccountList />
        </div>

        <Modal :show="resetPasswordModal" @close="closeModal" max-width="2xl">
            <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">Reset Portal Password</h3>
                    <hr>

                    <UpdatePasswordForm
                        @update:resetPasswordModal="resetPasswordModal = $event"
                    />
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
