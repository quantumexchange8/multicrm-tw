<script setup>
import { Link, useForm } from '@inertiajs/vue3'
import { MailIcon, LockClosedIcon, LoginIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import Checkbox from '@/Components/Checkbox.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import ToastList from "@/Components/ToastList.vue";

defineProps({
    canResetPassword: Boolean,
    status: String,
})

const form = useForm({
    email: '',
    password: '',
})

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    })
}

function closeDrawer() {
    const drawer = document.getElementById("drawer-bottom-example");
    drawer.style.transform = "translateY(100%)";
    drawer.setAttribute("aria-hidden", "true");
}

</script>

<template>
    <GuestLayout title="Log in">
        <ValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>


        <form @submit.prevent="submit">
            <ToastList />

            <div class="grid gap-6 text-center">
                <div class="space-y-2">
                    <div class="flex justify-center">
                        <img src="/assets/icon/email.png" alt="email_icon"/>
                    </div>
                    <Input id="email" type="email" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1 text-center placeholder:text-center" :placeholder="$t('public.Email')" v-model="form.email" autofocus autocomplete="email" />
                </div>

                <div class="space-y-2">
                    <div class="flex justify-center">
                        <img src="/assets/icon/password.png" alt="password_icon"/>
                    </div>
                    <Input id="password" type="password" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1 text-center placeholder:text-center" :placeholder="$t('public.Password')" v-model="form.password" autocomplete="current-password" />
                </div>

                <div class="flex items-center justify-end">
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-sm text-blue-500 hover:underline">
                        {{ $t('public.Forgot Password') }}
                    </Link>
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{iconSizeClasses}">
                        <LoginIcon aria-hidden="true" :class="iconSizeClasses" />
                        <span>{{ $t('public.Login') }}</span>
                    </Button>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $t("public.Don't have an account") }}
                    <Link :href="route('register')" class="text-blue-500 hover:underline">
                        {{ $t('public.Register') }}
                    </Link>
                </p>
            </div>
        </form>

        <!-- drawer component -->
        <div id="drawer-bottom-example" class="fixed bottom-0 left-0 right-0 z-40 w-full p-4 overflow-y-auto transition-transform bg-dark-eval-2 transform-none" tabindex="-1" aria-labelledby="drawer-bottom-label">
            <h5 id="drawer-bottom-label" class="inline-flex items-center mb-4 text-base font-semibold text-gray-400"><svg class="w-4 h-4 mr-2.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>Risk Warning</h5>
            <button type="button" @click="closeDrawer" data-drawer-hide="drawer-bottom-example" aria-controls="drawer-bottom-example" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 absolute top-2.5 right-2.5 inline-flex items-center justify-center dark:hover:bg-gray-600 dark:hover:text-white" >
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close drawer</span>
            </button>
            <p class="mb-6 text-sm text-gray-400">Our products are traded on margin and carry a high level of risk and it is possible to lose all your capital. These products may not be suitable for everyone and you should ensure that you understand the risks involved. High Risk Trading Warning: Trading foreign exchange on margin carries a high level of risk, and may not be suitable for all investors. The high degree of leverage can work against you as well as for you. Before deciding to trade foreign exchange you should carefully consider your investment objectives, level of experience, and risk appetite. The possibility exists that you could sustain a loss of some or all of your initial investment and therefore you should not invest money that you cannot afford to lose. You should be aware of all the risks associated with foreign exchange trading, and seek advice from an independent financial advisor if you have any doubts.</p>
            <p class="text-sm text-gray-200">© Powered By Current Tech</p>
        </div>

    </GuestLayout>
</template>
