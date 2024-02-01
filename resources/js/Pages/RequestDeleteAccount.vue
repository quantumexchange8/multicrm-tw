<script setup>
import {Link, useForm, usePage} from '@inertiajs/vue3'
import { MailIcon, PaperAirplaneIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'
import ToastList from "@/Components/ToastList.vue";
import {ref, watchEffect} from "vue";
import {trans} from "laravel-vue-i18n";

const form = useForm({
    email: ''
})

const submit = () => {
    form.post(route('sendDeleteRequest'))
}

const status = ref('');

watchEffect(() => {
    if (usePage().props.toast !== null) {
        status.value = trans('public.Submitted');
    }
});
</script>

<template>
    <GuestLayout title="Forgot Password">

        <ValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div class="grid gap-6">
                <div class="space-y-2">
                    <div class="flex justify-center">
                        <img src="/assets/icon/email.png" alt="email_icon"/>
                    </div>
                    <Input id="email" type="email" class="block w-full px-4 bg-dark-eval-2 border-transparent text-gray-300 focus:ring-offset-dark-eval-1 text-center placeholder:text-center" :placeholder="$t('public.Email')" v-model="form.email" autofocus autocomplete="username" />
                </div>

                <div>
                    <Button class="justify-center gap-2 w-full" :disabled="form.processing" v-slot="{ iconSizeClasses }">
                        <PaperAirplaneIcon aria-hidden="true" :class="iconSizeClasses" />
                        <span>{{ $t('public.Submit') }}</span>
                    </Button>
                </div>
                <div v-if="status" class="text-center font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <p class="text-sm text-center text-gray-600 dark:text-gray-400">
                    <Link :href="route('login')" class="text-blue-500 hover:underline">
                        {{ $t('public.Back to Login Page') }}
                    </Link>
                </p>
            </div>
        </form>

    </GuestLayout>
</template>
