<script setup>
import InputError from '@/Components/InputError.vue'
import Label from '@/Components/Label.vue'
import Button from '@/Components/Button.vue'
import Input from '@/Components/Input.vue'
import InputSelect from '@/Components/InputSelect.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3'
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import {onMounted, ref, watch} from "vue";
import Modal from "@/Components/Modal.vue";
import AvatarInput from "@/Pages/Profile/Partials/AvatarInput.vue";
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';

const props = defineProps({
    mustVerifyEmail: Boolean,
    status: String,
    countries: Object,
    avatar: String,
    frontIdentity: String,
    backIdentity: String
})
const frontIdentityModal = ref(false);
const backIdentityModal = ref(false);
const user = usePage().props.auth.user

const form = useForm({
    avatar: null,
    first_name: user.first_name,
    chinese_name: user.chinese_name,
    email: user.email,
    country: user.country,
    dob: user.dob,
    phone: user.phone,
    front_identity: null,
    back_identity: null,
});

const selectedCountry = ref(form.country);

function onchangeDropdown() {
    const selectedCountryName = selectedCountry.value;
    const country = props.countries.find((country) => country.name_en === selectedCountryName);

    if (country) {
        form.phone = `${country.phone_code}`;
        form.country = selectedCountry;
    }
}

const openFrontIdentityModal = () => {
    frontIdentityModal.value = true
}

const openBackIdentityModal = () => {
    backIdentityModal.value = true
}

const closeModal = () => {
    frontIdentityModal.value = false
    backIdentityModal.value = false
}

const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});

watch(selectedCountry, () => {
    onchangeDropdown();
});

const submit = () => {
    form.post(route('profile.update'));
};

</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Update your account's profile information and email address.
            </p>
        </header>

        <form>
            <div class="flex justify-center items-center my-4">
                <AvatarInput class="h-24 w-24 rounded-full" v-model="form.avatar" :default-src="avatar ? avatar : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" />
            </div>

            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-2">
                    <Label for="first_name" value="Name" />

                    <Input
                        id="first_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.first_name"
                        autocomplete="name"
                    />

                    <InputError class="mt-2" :message="form.errors.first_name" />
                </div>
                <div class="space-y-2">
                    <Label for="chinese_name" >Chinese Name</Label>

                    <Input
                        id="chinese_name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.chinese_name"
                        autocomplete="chinese_name"
                    />

                    <InputError class="mt-2" :message="form.errors.chinese_name" />
                </div>


                <div class="space-y-2">
                    <Label for="dob" value="Date Of Birth" />
                    <vue-tailwind-datepicker :formatter="formatter" as-single v-model="form.dob" input-classes="py-2 border-gray-400 w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-white dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1" />

                    <InputError class="mt-2" :message="form.errors.dob" />
                </div>

                <div class="space-y-2">
                    <Label for="email" value="Email" />

                    <Input
                        id="email"
                        type="email"
                        class="mt-1 block w-full"
                        v-model="form.email"
                        autocomplete="email"
                        readonly
                    />

                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div
                    v-if="props.mustVerifyEmail && user.email_verified_at === null"
                >
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Your email address is unverified.
                        <Link
                            :href="route('verification.send')"
                            method="post"
                            as="button"
                            class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                        >
                            Click here to re-send the verification email.
                        </Link>
                    </p>

                    <div
                        v-show="props.status === 'verification-link-sent'"
                        class="mt-2 font-medium text-sm text-green-600 dark:text-green-400"
                    >
                        A new verification link has been sent to your email address.
                    </div>
                </div>

                <div class="space-y-2">
                    <Label for="country" value="Country" />

                    <InputSelect v-model="selectedCountry" class="block w-full text-sm" placeholder="Choose Country">
                        <option v-for="country in props.countries" :value="country.name_en" :key="country.id">{{ country.name_en }}</option>
                    </InputSelect>

                    <InputError class="mt-2" :message="form.errors.country" />
                </div>


                <div class="space-y-2">
                    <Label for="phone" value="Mobile Phone" />

                    <Input
                        id="phone"
                        type="text"
                        class="mt-1 block w-full"
                        autocomplete="phone"
                        v-model="form.phone"
                    />

                    <InputError class="mt-2" :message="form.errors.phone" />
                </div>

                <div class="space-y-2">
                    <Label for="front_identity">
                        Proof of Identity (FRONT)
                        <a v-if="frontIdentity" href="javascript:void(0);" @click.prevent="openFrontIdentityModal" class="text-blue-500 hover:underline ml-2">Click to view</a>
                    </Label>
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
                    <input
                        type="file"
                        id="front_identity"
                        @input="form.front_identity = $event.target.files[0]"
                        class="block border border-gray-400 w-full rounded-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        :disabled="frontIdentity !== ''"
                    />
                    <InputError :message="form.errors.front_identity"/>
                </div>

                <div class="space-y-2">
                    <Label for="back_identity">
                        Proof of Identity (BACK)
                        <a v-if="backIdentity" href="javascript:void(0);" @click.prevent="openBackIdentityModal" class="text-blue-500 hover:underline ml-2">Click to view</a>
                    </Label>
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
                    <input
                        type="file"
                        id="back_identity"
                        @input="form.back_identity = $event.target.files[0]"
                        class="block border border-gray-400 w-full rounded-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border-0 file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 dark:border-gray-600 dark:bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1 disabled:dark:bg-dark-eval-0 disabled:dark:text-dark-eval-4"
                        :disabled="backIdentity !== ''"
                    />
                    <InputError :message="form.errors.back_identity"/>
                </div>
            </div>

            <div class="flex items-center gap-4 mt-6">
                <Button @click="submit" :disabled="form.processing">Save</Button>
            </div>
        </form>
    </section>
</template>
