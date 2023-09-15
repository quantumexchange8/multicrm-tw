<script setup>
import { useForm } from '@inertiajs/vue3'
import { MailIcon, LockClosedIcon } from '@heroicons/vue/outline'
import InputIconWrapper from '@/Components/InputIconWrapper.vue'
import Button from '@/Components/Button.vue'
import GuestLayout from '@/Layouts/Guest.vue'
import Input from '@/Components/Input.vue'
import Label from '@/Components/Label.vue'
import ValidationErrors from '@/Components/ValidationErrors.vue'

const props = defineProps({
    email: String,
    token: String,
})

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
})

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    })
}
</script>

<template>
    <GuestLayout title="Reset Password">
        <ValidationErrors class="mb-4" />

        <form @submit.prevent="submit">
            <div class="grid gap-4">
                <div class="space-y-4">
                    <div class="flex justify-center">
                        <img src="/assets/icon/email.png" alt="email_icon"/>
                    </div>
                    <Input id="email" type="email" class="block w-full placeholder:text-center" placeholder="Email" v-model="form.email" autofocus autocomplete="username" />
                </div>

                <div class="space-y-4">
                    <div class="flex justify-center">
                        <img src="/assets/icon/password.png" alt="password_icon"/>
                    </div>
                    <Input id="password" type="password" placeholder="Password" class="block w-full placeholder:text-center" v-model="form.password" autocomplete="new-password" />
                </div>

                <div class="space-y-4">
                    <div class="flex justify-center">
                        <img src="/assets/icon/password.png" alt="password_icon"/>
                    </div>
                    <Input id="password_confirmation" type="password" placeholder="Confirm Password" class="block w-full placeholder:text-center" v-model="form.password_confirmation" autocomplete="new-password" />
                </div>

                <div class="mt-4">
                    <Button class="w-full justify-center" :disabled="form.processing">
                        Reset Password
                    </Button>
                </div>
            </div>
        </form>
    </GuestLayout>
</template>
