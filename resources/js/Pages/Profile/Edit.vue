<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue'
import {Link, router, useForm, usePage} from "@inertiajs/vue3";
import AvatarInput from "@/Pages/Profile/Partials/AvatarInput.vue";
import {ref, watch} from "vue";
import VueTailwindDatepicker from "vue-tailwind-datepicker";
import Label from "@/Components/Label.vue";
import Modal from "@/Components/Modal.vue";
import Button from "@/Components/Button.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import InputSelect from "@/Components/InputSelect.vue";

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
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
});
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

const submit = () => {
    form.post(route('profile.update'));
};

const back = () => {
    router.get('/profile/detail')
}

const selectedCountry = ref(form.country);

function onchangeDropdown() {
    const selectedCountryName = selectedCountry.value;
    const country = props.countries.find((country) => country.name_en === selectedCountryName);

    if (country) {
        form.phone = `${country.phone_code}`;
        form.country = selectedCountry;
    }
}

watch(selectedCountry, () => {
    onchangeDropdown();
});

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

</script>

<template>
    <AuthenticatedLayout title="Profile">
        <template #header>
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="inline-flex items-center space-x-1 md:space-x-3">
                    <li>
                        <div class="flex items-center">
                            <Link :href="route('profile.detail')" class="font-semibold text-xl text-gray-700 hover:text-[#FF9E23] dark:text-gray-200 leading-tight dark:hover:text-[#FF9E23]">User Profile</Link>
                        </div>
                    </li>
                    <li aria-current="page">
                        <div class="flex items-center">
                            <svg class="w-3 h-3 text-gray-400 dark:text-white mx-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
                            </svg>
                            <span class="ml-1 mt-1 font-semibold text-xl text-[#FF9E23] md:ml-2 dark:text-[#FF9E23]">Edit Profile</span>
                        </div>
                    </li>
                </ol>
            </nav>
        </template>

        <div class="space-y-6">
            <div class="flex justify-center items-center my-4">
                <AvatarInput class="h-24 w-24 rounded-full" v-model="form.avatar" :default-src="avatar ? avatar : 'https://img.freepik.com/free-icon/user_318-159711.jpg'" />
            </div>
            <h2 class="text-xl font-medium text-gray-900 dark:text-gray-100">
                Profile Information
            </h2>
            <div
                class="p-4 sm:p-8 bg-white dark:bg-dark-eval-1 shadow sm:rounded-lg"
            >
                <form>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                                disabled
                            />

                            <InputError class="mt-2" :message="form.errors.email" />
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

                        <div class="space-y-2" v-if="user.kyc_approval !== 'approve'">
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
                            />
                            <InputError :message="form.errors.front_identity"/>
                        </div>

                        <div class="space-y-2" v-if="user.kyc_approval !== 'approve'">
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
                            />
                            <InputError :message="form.errors.back_identity"/>
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
            </div>

<!--            <div-->
<!--                class="p-4 sm:p-8 bg-white dark:bg-dark-eval-1 shadow sm:rounded-lg"-->
<!--            >-->
<!--                <DeleteUserForm class="max-w-xl" />-->
<!--            </div>-->
        </div>
    </AuthenticatedLayout>
</template>
