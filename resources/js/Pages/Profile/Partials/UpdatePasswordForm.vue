<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import Label from '@/Components/Label.vue'
import Input from '@/Components/Input.vue'
import InputError from '@/Components/InputError.vue'
import Button from '@/Components/Button.vue'

const passwordInput = ref(null)
const currentPasswordInput = ref(null)

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
})

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal()
            form.reset()
        },
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation')
                passwordInput.value.focus()
            }
            if (form.errors.current_password) {
                form.reset('current_password')
                currentPasswordInput.value.focus()
            }
        },
    })
}

const emit = defineEmits(['update:resetPasswordModal']);
const closeModal = () => {
    emit('update:resetPasswordModal', false);
}
</script>

<template>
    <form class="mt-6 space-y-6">
        <div>
            <Label for="current_password" value="Current Password" />

            <Input
                id="current_password"
                ref="currentPasswordInput"
                v-model="form.current_password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="current-password"
            />

            <InputError
                :message="form.errors.current_password"
                class="mt-2"
            />
        </div>

        <div>
            <Label for="password" value="New Password" />

            <Input
                id="password"
                ref="passwordInput"
                v-model="form.password"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <InputError :message="form.errors.password" class="mt-2" />
        </div>

        <ul class="space-y-1 text-xs text-gray-500 list-disc list-inside dark:text-gray-400">
            <li>
                Password must be at least 6 characters.
            </li>
            <li>
                Contains at least one capital letter.
            </li>
            <li>
                Contains at least one number.
            </li>
            <li>
                Contains at least one letter.
            </li>
        </ul>

        <div>
            <Label for="password_confirmation" value="Confirm Password" />

            <Input
                id="password_confirmation"
                v-model="form.password_confirmation"
                type="password"
                class="mt-1 block w-full"
                autocomplete="new-password"
            />

            <InputError
                :message="form.errors.password_confirmation"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-end gap-2">
            <div class="grid grid-cols-2 gap-2 float-right">
                <Button variant="danger" class="px-6 justify-center" @click.prevent="closeModal">
                    Cancel
                </Button>
                <Button class="justify-center" @click.prevent="updatePassword" :disabled="form.processing">Save</Button>
            </div>
        </div>
    </form>
</template>
