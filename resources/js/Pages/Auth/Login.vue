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

</script>

<template>
    <GuestLayout title="Log in">
        <ValidationErrors class="mb-4" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>


        <form @submit.prevent="submit">

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

    </GuestLayout>
</template>
