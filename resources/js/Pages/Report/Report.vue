<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import {ref} from "vue";
import Button from "@/Components/Button.vue";
import IbRebateReport from "@/Pages/Report/Partials/IbRebateReport.vue";

const activeComponent = ref("ib_rebate"); // 'pending' is initially active

const setActiveComponent = (component) => {
    activeComponent.value = component;
};
</script>

<template>
    <AuthenticatedLayout title="Report">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Report
                </h2>
            </div>
        </template>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <Button
                variant="primary-opacity"
                class="px-6 border border-blue-600 justify-center mt-4 focus:ring-0"
                :class="{ 'bg-transparent': activeComponent !== 'ib_rebate', 'dark:bg-[#007BFF] dark:text-white': activeComponent === 'ib_rebate' }"
                @click="setActiveComponent('ib_rebate')"
            >
                IB Rebate Report
            </Button>
        </div>

        <IbRebateReport
            v-if="activeComponent === 'ib_rebate'"
        />
    </AuthenticatedLayout>
</template>
