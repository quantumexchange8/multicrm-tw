<script setup>
import Action from "@/Pages/GroupNetwork/Partials/Action.vue";

const props = defineProps({
    children: Object,
    ib: Object,
});

function formatDate(date) {
    const formattedDate = new Date(date).toISOString().slice(0, 10);
    return formattedDate.replace(/-/g, '/');
}

</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <div
            v-for="child in props.children"
            :key="child.id"
        >
            <div class="w-full bg-white rounded-lg shadow dark:bg-dark-eval-1 p-6">
                <div class="flex flex-col items-center mb-4">
                    <img
                        class="h-12 w-12 rounded-full"
                        :src="child.profile_pic ? child.profile_pic : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                        alt="ProfilePic"
                    >
                    <h5 class="my-1 text-xl font-medium text-gray-900 dark:text-white">{{ child.of_user.first_name }}</h5>
                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ child.of_user.ib_id }}</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-center md:text-left">
                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Account Type') }}:</div>
                    <div class="text-black dark:text-white">{{ child.account_type.name }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Since Date') }} </div>
                    <div class="text-black dark:text-white">{{ formatDate(child.created_at) }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Direct IB') }} </div>
                    <div class="text-black dark:text-white">{{ child.of_user.direct_ib }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Direct Clients') }} </div>
                    <div class="text-black dark:text-white">{{ child.of_user.direct_client }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Total Group IB') }} </div>
                    <div class="text-black dark:text-white">{{ child.of_user.total_ib }}</div>

                    <div class="text-black dark:text-dark-eval-3">{{ $t('public.Total Group Clients') }} </div>
                    <div class="text-black dark:text-white">{{ child.of_user.total_client }}</div>
                </div>
                <Action
                    :ib="child"
                    :currentIb="ib"
                />
            </div>

        </div>
    </div>
</template>
