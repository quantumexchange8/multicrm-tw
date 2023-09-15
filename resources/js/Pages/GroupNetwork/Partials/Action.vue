<script setup>
import Button from "@/Components/Button.vue";
import {ref} from "vue";
import Modal from "@/Components/Modal.vue";
import Input from "@/Components/Input.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";

const props = defineProps({
    ib: Object,
    currentIb: Object
})

const IbRebateModal = ref(false);
const showForm = ref(false);

const openRebateAllocationModal = () => {
    IbRebateModal.value = true;
}

const ibGroupRates = {};
props.ib.symbol_groups.forEach((ibRebateInfo) => {
    ibGroupRates[ibRebateInfo.symbol_group.id] = ibRebateInfo.amount;
});

const form = useForm({
    user_id: props.ib.id,
    ibGroupRates
});

const submitForm = () => {
    form.post(route('updateRebate.update'), {
        onSuccess: () => {
            showForm.value = false;
            closeModal();
        },
    });
}

const closeModal = () => {
    IbRebateModal.value = false
    showForm.value = false;
}

const cancel = () => {
    showForm.value = false;
}
</script>

<template>
    <Button
        ref="buttonRef"
        class="w-full justify-center mt-6"
        @click="openRebateAllocationModal()"
    >
        View More Details
    </Button>

    <!-- Action Modal -->
    <Modal :show="IbRebateModal" @close="closeModal">
        <div class="p-6">
            <h2 class="text-lg mb-2 font-medium text-gray-900 dark:text-gray-100">View More Details</h2>
            <hr>
            <div class="grid grid-cols-1 md:grid-cols-2 mt-6">
                <div>
                    <p class="text-black dark:text-white mb-5">My Rebate Info</p>
                    <div v-for="uplineRebate in currentIb.symbol_groups">
                        <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                            <span class="text-black dark:text-dark-eval-3 uppercase">{{ uplineRebate.symbol_group.name }} (USD)/LOT</span>
                            <span class="text-black dark:text-white text-right md:text-center py-2">{{ uplineRebate.amount }}</span>
                        </div>
                    </div>
                </div>

                <div v-if="showForm">
                    <p class="text-black dark:text-white mb-5">IB Name: {{ ib.of_user.first_name }}</p>

                    <form @submit.prevent="submitForm">
                        <div v-for="ibRebateInfo in ib.symbol_groups" :key="ibRebateInfo.id">
                            <div class="grid grid-cols-2 text-black dark:text-white md:pr-4 mb-5 items-center">
                                <span class="text-black dark:text-dark-eval-3 uppercase">{{ ibRebateInfo.symbol_group.name }} (USD)/LOT</span>
                                <Input
                                    :id="`group_${ibRebateInfo.id}`"
                                    :model-value="ibRebateInfo.amount"
                                    v-model="form.ibGroupRates[ibRebateInfo.symbol_group.id]"
                                    type="number"
                                    step="0.01" min="0.00"
                                />
                            </div>

                            <InputError :message="form.errors[`ibGroupRates.${ibRebateInfo.symbol_group.id}`]" class="mb-4" />

                        </div>
                        <div class="grid grid-cols-2 gap-4 w-full md:w-1/2 md:float-right">
                            <Button variant="secondary" class="justify-center" @click="cancel">
                                Cancel
                            </Button>
                            <Button class="px-6 justify-center" :disabled="form.processing">Save</Button>
                        </div>
                    </form>
                </div>

                <div v-else>
                    <p class="text-black dark:text-white mb-5">IB Name: {{ ib.of_user.first_name }}</p>
                    <div v-for="ibRebateInfo in ib.symbol_groups" :key="ibRebateInfo.id">
                        <div class="grid grid-cols-2 text-black dark:text-white pr-4 mb-5 items-center">
                            <span class="text-black dark:text-dark-eval-3 uppercase">{{ ibRebateInfo.symbol_group.name }} (USD)/LOT</span>
                            <span class="text-black dark:text-white text-right md:text-center py-2">{{ ibRebateInfo.amount }}</span>
                        </div>

                    </div>

                    <Button @click="showForm = !showForm" class="px-6 float-right">Edit</Button>
                </div>
            </div>
        </div>
    </Modal>
</template>
