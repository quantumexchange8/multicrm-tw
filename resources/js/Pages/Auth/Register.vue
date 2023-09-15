<script setup>
import {Link, useForm} from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/Guest.vue';
import Input from '@/Components/Input.vue';
import InputSelect from '@/Components/InputSelect.vue';
import Label from '@/Components/Label.vue';
import Button from '@/Components/Button.vue';
import {onMounted, ref} from 'vue';
import intlTelInput from 'intl-tel-input';
import 'intl-tel-input/build/css/intlTelInput.css';
import VueTailwindDatepicker from 'vue-tailwind-datepicker'
import RegisterCaption from "@/Components/Auth/RegisterCaption.vue";
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";

const dateValue = ref([])
const formatter = ref({
    date: 'YYYY-MM-DD',
    month: 'MM'
})
const props = defineProps({
    countries: Object,
    leverages: Object,
    referral: String,
})

const form = useForm({
    form_step: 1,
    first_name: '',
    chinese_name: '',
    email: '',
    phone: '',
    password: '',
    password_confirmation: '',
    dob: '',
    country: '',
    leverage: '',
    account_platform: '',
    account_type: 1,
    front_identity: null,
    back_identity: null,
    verification_via: 'email',
    verification_code: '',
    referral_code: props.referral,
    terms: '',
});

const submit = () => {
    // Get the selected country code
    const countryCode = phoneInputInstance.value.getSelectedCountryData().dialCode;
    // Concatenate the country code with the phone number
    form.phone = `+${countryCode}${form.phone}`;

    // Submit the form
    form.post(route('register'), {
        onSuccess: () => {
            form.reset('password', 'password_confirmation');
            formStep.value = 1;
        },
    });
};

const formStep = ref(1);
const phoneInput = ref(null);
const isButtonDisabled = ref(false)
const buttonText = ref('Send OTP')
const phoneInputInstance = ref(null);

onMounted(() => {
    phoneInputInstance.value = intlTelInput(phoneInput.value, {
        initialCountry: 'my',
        separateDialCode: true,
        preferredCountries: ['my', 'vn'],
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.js',
        dropdownContainer: document.body,
        customPlaceholder: function (selectedCountryPlaceholder, selectedCountryData) {
            return selectedCountryPlaceholder.replace('+' + selectedCountryData.dialCode + ' ', '');
        },
        customDropdownItem: function (country, sanitizedCountry, selectedCountryData) {
            return '<div class="iti__country iti__standard"><div class="iti__flag-box"><div class="iti__flag iti__' + sanitizedCountry + '"></div></div><span class="iti__country-name">' + country + '</span><span class="iti__dial-code">+' + selectedCountryData.dialCode + '</span></div>';
        },
    });
});

const platforms = [
    // { id: 'account_platform_2', src: '/assets/platform/icon/metatrader5.png', value: 2 },
    { id: 'account_platform_3', src: '/assets/platform/icon/ctrader.png', value: 3 },
    // { id: 'account_platform_4', src: '/assets/platform/icon/match_trade.png', value: 4 },
];

const acc_types = [
    { id: 'account_type_2', src: '/assets/account_type/ecn.png', value: 2 },
    { id: 'account_type_3', src: '/assets/account_type/standard.png', value: 3 },
    { id: 'account_type_4', src: '/assets/account_type/standard_islam.png', value: 4 },
];

const handleFrontIdentity = (event) => {
    form.front_identity = event.target.files[0];
};

const handleBackIdentity = (event) => {
    form.back_identity = event.target.files[0];
};

function nextStep() {
    form.post(route('register.first.step'), {
        onSuccess: () => {
            formStep.value++;
            form.form_step++;
        },
    });
}
// function nextStep() {
//     formStep.value++;
//     form.form_step++;
// }
function prevStep() {
    formStep.value--;
    form.form_step--;
}

function generateOTP() {
    const otp = Math.floor(100000 + Math.random() * 900000);
    return otp.toString();
}

function startCountdown() {
    isButtonDisabled.value = true;
    buttonText.value = '60';

    let count = 60;
    const countdownInterval = setInterval(() => {
        count--;
        buttonText.value = count.toString();

        if (count === 0) {
            clearInterval(countdownInterval);
            isButtonDisabled.value = false;
            buttonText.value = 'Button';
        }

        // Generate OTP
        if (count === 59) {
            const otp = generateOTP();
            const email = form.email;
            let url = 'register/send-otp';
            if (props.referral_code) {
                url = `register/${props.referral_code}/send-otp`;
            }
            axios
                .post(url, { otp, email })
                .then(response => {

                    console.log(response.data);
                })
                .catch(error => {
                    // Handle the error if needed
                    console.log(error);
                });
        }
    }, 1000);
}


</script>

<template>
    <GuestLayout title="Register">

        <form>
            <div class="grid gap-6">
                <!-- Progress Bar -->
                <div class="w-full py-6">
                    <div class="flex">
                        <div class="w-1/4">
                            <div class="relative mb-2">
                                <div class="w-10 h-10 mx-auto rounded-full text-lg text-white flex items-center" :class="{'bg-[#FF9E23]': formStep === 1, 'bg-[#202020]': formStep !== 1}">
                                    <span class="text-center text-white w-full font-montserrat font-semibold">1</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-1/4">
                            <div class="relative mb-2">
                                <div class="absolute flex align-center items-center align-middle content-center" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
                                    <div class="w-full rounded items-center align-middle align-center flex-1">
                                        <div class="w-0 pt-1 rounded" :class="{'bg-[#FF9E23]': formStep >= 2, 'bg-gray-200': formStep < 2}" style="width: 100%"></div>
                                    </div>
                                </div>

                                <div class="w-10 h-10 mx-auto rounded-full text-lg text-white flex items-center" :class="{'bg-[#FF9E23]': formStep === 2, 'bg-[#202020]': formStep !== 2}">
                                    <span class="text-center text-white w-full font-montserrat font-semibold">2</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-1/4">
                            <div class="relative mb-2">
                                <div class="absolute flex align-center items-center align-middle content-center" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
                                    <div class="w-full rounded items-center align-middle align-center flex-1">
                                        <div class="w-0 pt-1 rounded" :class="{'bg-[#FF9E23]': formStep >= 3, 'bg-gray-200': formStep < 3}" style="width: 100%"></div>
                                    </div>
                                </div>

                                <div class="w-10 h-10 mx-auto rounded-full text-lg text-white flex items-center" :class="{'bg-[#FF9E23]': formStep === 3, 'bg-[#202020]': formStep !== 3}">
                                    <span class="text-center text-white w-full font-montserrat font-semibold">3</span>
                                </div>
                            </div>
                        </div>

                        <div class="w-1/4">
                            <div class="relative mb-2">
                                <div class="absolute flex align-center items-center align-middle content-center" style="width: calc(100% - 2.5rem - 1rem); top: 50%; transform: translate(-50%, -50%)">
                                    <div class="w-full rounded items-center align-middle align-center flex-1">
                                        <div class="w-0 pt-1 rounded" :class="{'bg-[#FF9E23]': formStep === 4, 'bg-gray-200': formStep !== 4}" style="width: 100%"></div>
                                    </div>
                                </div>

                                <div class="w-10 h-10 mx-auto rounded-full text-lg text-white flex items-center" :class="{'bg-[#FF9E23]': formStep === 4, 'bg-[#202020]': formStep !== 4}">
                                    <span class="text-center text-white w-full font-montserrat font-semibold">4</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page 1 -->
                <div v-show="formStep === 1" class="space-y-5">
                    <div class="text-center">
                        <RegisterCaption :title="$t('public.Welcome! First things first...')" :caption="$t('public.Let’s set up your own account.')" />
                    </div>
                    <Input id="form_step" type="hidden" :modelValue="form.form_step.toString()"/>

                    <Label for="email" class="text-white" :value="$t('public.Email')" />
                    <Input id="email" type="email" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1" :placeholder="$t('public.Email')" v-model="form.email" autocomplete="email" autofocus />
                    <InputError :message="form.errors.email"/>

                    <Label for="phone" class="text-white" :value="$t('public.Mobile Phone')" />
                    <input
                        ref="phoneInput"
                        type="tel"
                        id="phone"
                        name="phone"
                        :class="[
                          'py-2 border-transparent rounded-full placeholder:text-sm',
                          'focus:border-gray-400 focus:ring text-sm text-gray-300 focus:ring-blue-500',
                          'dark:border-gray-600 bg-[#202020] dark:text-gray-300 dark:focus:ring-offset-dark-eval-1',
                          'w-[342px] sm:w-[624px]'
                        ]"
                        v-model="form.phone"
                        autocomplete="tel"
                        class="block"
                    />
                    <InputError :message="form.errors.phone"/>

                    <Label class="text-white" for="password" :value="$t('public.Password')" />
                    <Input id="password" type="password" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1" :placeholder="$t('public.Password')" v-model="form.password" autocomplete="new-password" />
                    <InputError :message="form.errors.password"/>

                    <ul class="list-disc ml-4 text-dark-eval-4 text-sm">
                        <li>{{ $t('public.Password must be at least 6 characters.') }}</li>
                        <li>{{ $t('public.Contains at least one capital letter.') }}</li>
                        <li>{{ $t('public.Contains at least one number.') }}</li>
                        <li>{{ $t('public.Contains at least one letter.') }}</li>
                    </ul>
                    <Label class="text-white" for="password_confirmation" :value="$t('public.Confirm Password')" />
                    <Input id="password_confirmation" type="password" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1" :placeholder="$t('public.Confirm Password')" v-model="form.password_confirmation" autocomplete="new-password" />
                </div>

                <!-- Page 2 -->
                <div class="space-y-5" v-if="formStep === 2">
                    <Input id="form_step" type="hidden" :modelValue="form.form_step.toString()"/>

                    <div class="text-center">
                        <RegisterCaption :title="$t('public.Let us know more about you')" :caption="$t('public.Please note that the information below cannot be changed later.')" />
                    </div>

                    <Label for="full_name" class="text-white" :value="$t('public.Full Name')" />
                    <Input id="full_name" type="text" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1" :placeholder="$t('public.Full Name')" v-model="form.first_name" autocomplete="full_name" />
                    <InputError :message="form.errors.first_name"/>

                    <Label for="chinese_name" class="text-white">{{ $t('public.Chinese Name (Optional)') }}</Label>
                    <Input id="chinese_name" type="text" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1" :placeholder="$t('public.Chinese Name')" v-model="form.chinese_name" autocomplete="chinese_name" />
                    <InputError :message="form.errors.chinese_name"/>

                    <Label for="dob" class="text-white" :value="$t('public.Date of Birth')" />
                    <vue-tailwind-datepicker :formatter="formatter" as-single v-model="form.dob" input-classes="py-2 border-transparent bg-[#202020] w-full rounded-full text-sm placeholder:text-sm focus:border-gray-400 focus:ring focus:ring-blue-500 focus:ring-offset-dark-eval-1 text-gray-300" />
                    <InputError :message="form.errors.dob"/>

                    <Label for="religion" class="text-white" :value="$t('public.Country')" />
                    <InputSelect v-model="form.country" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1 text-sm" :placeholder="$t('public.Choose Country')">
                        <option v-for="country in countries" :value="country.name_en" :key="country.id">{{ country.name_en }}</option>
                    </InputSelect>
                    <InputError :message="form.errors.country"/>

                </div>

                <!-- Page 3 -->
                <div class="space-y-5" v-if="formStep === 3">
                    <Input id="form_step" type="hidden" :modelValue="form.form_step.toString()"/>

                    <div class="text-center">
                        <RegisterCaption :title="$t('public.Create a trading account to join us!')" :caption="$t('public.You can always add another trading account later.')" />
                    </div>

                    <Label for="account_platform" class="text-white" :value="$t('public.Select Account Platform')" />
                    <ul class="grid w-full gap-6 md:grid-cols-3">
                        <li v-for="(platform, index) in platforms" :key="index">
                            <input type="radio" :id="platform.id" name="account_platform" :value="platform.value" class="hidden peer" v-model="form.account_platform" :required="platform.required">
                            <label :for="platform.id" class="inline-flex items-center justify-between w-full p-5 border rounded-lg cursor-pointer hover:text-gray-300 border-[#007BFF] peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 text-gray-400 bg-transparent shadow-lg hover:shadow-blue-600">
                                <div class="block">
                                    <img class="object-cover" :src="platform.src" alt="account_platform">
                                </div>
                            </label>
                        </li>
                    </ul>
                    <InputError :message="form.errors.account_platform"/>

<!--                    <Label for="account_type" value="Select Account Type" />-->
<!--                    <ul class="grid w-full gap-6 md:grid-cols-3">-->
<!--                        <li v-for="(type, index) in acc_types" :key="index">-->
<!--                            <input type="radio" :id="type.id" name="account_type" :value="type.value" class="hidden peer" v-model="form.account_type" :required="type.required">-->
<!--                            <label :for="type.id" class="inline-flex items-center justify-between w-full p-5 text-gray-500 bg-white border border-gray-200 rounded-lg cursor-pointer dark:hover:text-gray-300 dark:border-[#007BFF] dark:peer-checked:bg-[#007BFF] peer-checked:border-blue-600 peer-checked:text-blue-600 hover:text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:bg-transparent dark:shadow-lg dark:hover:shadow-blue-600">-->
<!--                                <div class="block">-->
<!--                                    <img class="object-cover" :src="type.src" alt="account_type">-->
<!--                                </div>-->
<!--                            </label>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                    <InputError :message="form.errors.account_type"/>-->
                    <Input type="hidden" v-model="form.account_type"/>

                    <Label for="leverage" class="text-white" :value="$t('public.Trading Account Leverage')" />
                    <InputSelect v-model="form.leverage" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1 text-sm" :placeholder="$t('public.Choose Leverage')">
                        <option v-for="leverage in leverages" :value="leverage.value" :key="leverage.id">{{ leverage.leverage }}</option>
                    </InputSelect>
                    <InputError :message="form.errors.leverage"/>

                </div>

                <!-- Page 4 -->
                <div class="space-y-5" v-if="formStep === 4">
                    <div class="text-center">
                        <RegisterCaption :title="$t('public.One last step to verify your identity')" :caption="$t('public.You’re about to start using the QCG.')" />
                    </div>

                    <div class="grid w-full gap-6 md:grid-cols-2">
                        <div class="space-y-5">
                            <Label for="front_identity" class="text-white">{{ $t('public.Proof of Identity (FRONT)') }}</Label>
                            <input type="file" id="front_identity" @change="handleFrontIdentity" class="block border border-transparent bg-dark-eval-2 w-full rounded-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:border-transparent file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 focus:ring-offset-dark-eval-1"/>
                            <InputError :message="form.errors.front_identity"/>
                        </div>
                        <div class="space-y-5">
                            <Label for="back_identity" class="text-white">{{ $t('public.Proof of Identity (BACK)') }}</Label>
                            <input type="file" id="back_identity" @change="handleBackIdentity" class="block border border-transparent bg-dark-eval-2 w-full rounded-full text-sm text-gray-300 file:mr-4 file:py-2 file:px-4 file:border-transparent file:text-sm file:font-semibold file:bg-blue-500 file:text-white hover:file:bg-blue-600 focus:ring-offset-dark-eval-1"/>
                            <InputError :message="form.errors.back_identity"/>
                        </div>
                        <div class="space-y-5">
                            <Label for="full_name" class="text-white" :value="$t('Verification Via')" />
                            <div class="flex gap-x-12">
                                <div class="flex">
                                    <input type="radio" name="verification_via" v-model="form.verification_via" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-radio-group-1" value="email" checked>
                                    <label for="hs-radio-group-1" class="text-sm text-gray-300 ml-2">{{ $t('public.Email') }}</label>
                                </div>

<!--                                <div class="flex">-->
<!--                                    <input type="radio" name="verification_via" v-model="form.verification_via" class="shrink-0 mt-0.5 border-gray-200 rounded-full text-blue-600 focus:ring-blue-500 dark:bg-gray-800 dark:border-gray-700 dark:checked:bg-blue-500 dark:checked:border-blue-500 dark:focus:ring-offset-gray-800" id="hs-radio-group-2" value="phone">-->
<!--                                    <label for="hs-radio-group-2" class="text-sm text-gray-500 ml-2 dark:text-gray-400">Phone</label>-->
<!--                                </div>-->
                            </div>
                            <InputError :message="form.errors.verification_via"/>
                        </div>
                        <div class="space-y-5">
                            <Label for="full_name" class="text-white" :value="$t('public.Verification Code')" />
                            <div class="flex rounded-md shadow-sm">
                                <button type="button" class="py-2 px-4 inline-flex flex-shrink-0 justify-center items-center gap-2 rounded-l-full border border-transparent font-semibold bg-blue-500 text-white hover:bg-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all text-sm"
                                        :disabled="isButtonDisabled"
                                        @click="startCountdown">
                                    {{ buttonText }}
                                </button>
                                <input type="text" id="hs-leading-button-add-on" name="hs-leading-button-add-on" class="py-2 px-4 block w-full border-transparent shadow-sm rounded-r-full text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 bg-[#202020] text-gray-300 focus:ring-offset-dark-eval-1" v-model="form.verification_code">
                            </div>
                            <InputError :message="form.errors.verification_code"/>
                        </div>

                        <Input id="referral" type="hidden" v-model="form.referral_code" />
                    </div>

                    <h3 class="list-decimal list-inside text-xl text-dark-eval-4">{{ $t('public.Terms and Conditions') }}</h3>
                    <ol class="list-decimal list-inside text-sm text-dark-eval-4">
                        <li>{{ $t("public.I confirm that I've read the") }} <a class="text-blue-500" target="_blank" href="/assets/register-tnc/Client Agreement 2023.pdf">{{ $t('public.Terms and Conditions') }}</a></li>
                        <li>{{ $t("public.I confirm that I've read the") }} <a class="text-blue-500" target="_blank" href="/assets/register-tnc/Privacy Policy 2023.pdf">{{ $t('public.Privacy Policy') }}</a></li>
                        <li>{{ $t("public.I confirm that I've read the") }} <a class="text-blue-500" target="_blank" href="/assets/register-tnc/Risk Disclosure Notice 2023.pdf">{{ $t('public.Risk Disclosure Document') }}</a></li>
                        <li>{{ $t('public.I hereby acknowledge and consent that company shall provide me with the information only through a durable medium, (i.e., any instrument which enables a client to store information for future reference and adequate period, and allows the unchanged reproduction of the information stored.') }}</li>
                        <li>{{ $t('public.You agree that company may collect, use and disclose your personal data which you have provided in this form,for providing marketing material that you have agreed to receive, in accordance with Privacy Policy.') }}</li>
                    </ol>

                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <Checkbox v-model="form.terms"/>
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-light text-dark-eval-4">{{ $t('public.I acknowledge that I have read, and do hereby accept the terms and conditions stated as above.') }}</label>
                        </div>
                    </div>
                    <InputError :message="form.errors.terms"/>

                </div>

                <div class="flex items-center justify-center gap-8 mt-4">
                    <Button type="button" :disabled="formStep === 1" @click="prevStep" class="px-12">
                        <span>{{ $t('pagination.Back') }}</span>
                    </Button>

                    <Button type="button" v-if="formStep !== 4" @click="nextStep" class="px-12">
                        <span>{{ $t('pagination.Next') }}</span>
                    </Button>

                    <Button type="button" v-else @click="submit" class="px-12">
                        <span>{{ $t('public.Register') }}</span>
                    </Button>

                </div>

                <p class="text-sm text-dark-eval-4">
                    {{ $t('public.Already have an account?') }}
                    <Link :href="route('login')" class="text-blue-500 hover:underline">
                        {{ $t('public.Login') }}
                    </Link>
                </p>
            </div>

        </form>
    </GuestLayout>
</template>
