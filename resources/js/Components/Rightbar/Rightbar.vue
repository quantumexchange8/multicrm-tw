<script setup>
import RightbarTitle from "@/Components/Rightbar/RightbarTitle.vue";
import RightbarContent from "@/Components/Rightbar/RightbarContent.vue";
import DepositForm from "@/Components/Rightbar/DepositForm.vue";
import WithdrawalForm from "@/Components/Rightbar/WithdrawalForm.vue";
import {usePage} from "@inertiajs/vue3";
import {computed, ref, watch} from "vue";
import Button from "@/Components/Button.vue";
import { usePermission } from '@/Composables/permissions.js'
import Swal from "sweetalert2";

const page = usePage()
const monthlyDeposit = page.props.monthlyDeposit;
const monthlyWithdrawal = page.props.monthlyWithdrawal;
const IBAccountTypes = page.props.IBAccountTypes;
const user = computed(() => page.props.auth.user)
const { hasRole } = usePermission();
const cashWalletComponent = ref(formatAmount(page.props.auth.user.cash_wallet));

// Watch for changes in page.props.auth.user.cash_wallet
watch(() => page.props.auth.user.cash_wallet, (newCashWallet) => {
    cashWalletComponent.value = formatAmount(newCashWallet);
});

const rebateEarnedComponent = ref({
    title: 'Rebate Earned ($)',
    amount: IBAccountTypes[0] && IBAccountTypes[0].rebate_wallet
        ? formatAmount(IBAccountTypes[0].rebate_wallet)
        : '0.00',
});

function formatAmount(amount) {
    return amount.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

async function applyRebate() {
    const account_type = 1;

    try {
        const response = await axios.post('/applyRebate', {
            account_type,
        });
        if (response.data.success) {
            await Swal.fire({
                title: 'Success',
                text: response.data.message,
                icon: 'success',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
            // Update cashWalletComponent with new value
            cashWalletComponent.value = formatAmount(response.data.cash_wallet)
            if (hasRole('ib')) {
                // Update rebateEarnedComponent with new value
                rebateEarnedComponent.value = {
                    title: 'Rebate Earned ($)',
                    amount: formatAmount(response.data.rebate_wallet),
                };
            }
        } else {
            console.log(response.data.message);
        }
    } catch (error) {
        if (error.response && error.response.status === 422) {
            await Swal.fire({
                title: 'Error',
                text: error.response.data.message,
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        } else {
            await Swal.fire({
                title: 'Error',
                text: 'An error occurred while applying the rebate.',
                icon: 'error',
                background: '#000000',
                iconColor: '#ffffff',
                color: '#ffffff',
                confirmButtonText: 'OK',
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
                },
            });
        }
    }
}

async function confirmApplyRebate() {
    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500 mx-2',
            cancelButton: 'bg-red-500 py-2 px-6 rounded-full text-white hover:bg-red-600 focus:ring-red-500 mx-2',
        },
        buttonsStyling: false,
        background: '#000000',
        iconColor: '#ffffff',
        color: '#ffffff',
    });

    const result = await swalWithBootstrapButtons.fire({
        title: 'Are you sure?',
        text: "You are applying rebate.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        reverseButtons: true,
    });

    if (result.isConfirmed) {
        await applyRebate();
    } else if (result.dismiss === Swal.DismissReason.cancel) {
        swalWithBootstrapButtons.fire({
            title: 'Cancelled',
            text: "You have cancelled the action",
            icon: 'error',
            confirmButtonText: 'OK',
            customClass: {
                confirmButton: 'bg-blue-500 py-2 px-6 rounded-full text-white hover:bg-blue-600 focus:ring-blue-500',
            },
        });
    }
}

</script>

<template>
    <aside class="w-full md:w-auto space-y-4">
        <RightbarTitle title="Personal Finances">
            <RightbarContent :title="$t('public.Cash Wallet') + ' ($)'" :amount="cashWalletComponent" />
            <RightbarContent v-if="hasRole('ib')" :title="$t('public.Rebate Earn') + ' ($)'" :amount="rebateEarnedComponent.amount" />
<!--            <RightbarContent title="Reward Point (RP)" amount="999,999" />-->

            <div class="grid grid-cols-2 gap-4 mt-8">
                <DepositForm />
                <WithdrawalForm />
            </div>
            <div v-if="hasRole('ib')">
                <Button variant="secondary-opacity" @click.prevent="confirmApplyRebate()" class="w-full justify-center mt-4">
                    {{ $t('public.Apply Rebate') }}</Button>
            </div>
            <Button :href="route('transaction')" variant="primary-opacity" class="w-full bg-transparent border border-blue-800 justify-center mt-4">
                {{ $t('public.sidebar.Internal Transfer') }}</Button>

        </RightbarTitle>

        <RightbarTitle title="Monthly Performance">
            <RightbarContent title="Current Month Deposit ($)" :amount="monthlyDeposit" />
            <RightbarContent title="Current Month Withdrawal ($)" :amount="monthlyWithdrawal" />
        </RightbarTitle>
    </aside>
</template>
