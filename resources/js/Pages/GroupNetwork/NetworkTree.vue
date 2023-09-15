<script setup>
import AuthenticatedLayout from "@/Layouts/Authenticated.vue";
import Input from "@/Components/Input.vue";
import InputIconWrapper from "@/Components/InputIconWrapper.vue";
import Button from "@/Components/Button.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import InputSelect from "@/Components/InputSelect.vue";
import {ref, watch} from "vue";
import debounce from "lodash/debounce.js";
import {router} from "@inertiajs/vue3";
import {library} from "@fortawesome/fontawesome-svg-core";
import {faRotateRight, faSearch, faX} from "@fortawesome/free-solid-svg-icons";
import NetworkChild from "@/Pages/GroupNetwork/Partials/NetworkChild.vue";
import Loading from "@/Components/Loading.vue";
library.add(faSearch,faX,faRotateRight);
const props = defineProps({
    root: Object,
    filters: Object,
})

function nodeWasClicked(node) {
    console.log('Last Node');
}

let search = ref(props.filters.search);
const isLoading = ref(false);

watch(search, debounce(function(value) {
    isLoading.value = true;
    router.get('/group_network/network_tree', { search: value }, { preserveState: true, replace: true, onFinish: visit => { isLoading.value = false } });
}, 300));

function resetField() {
    const url = new URL(window.location.href);
    url.searchParams.delete('search');

    // Navigate to the updated URL without the search parameter
    window.location.href = url.href;
}

function clearField() {
    search.value = '';
}

function handleKeyDown(event) {
    if (event.keyCode === 27) {
        clearField();
    }
}

</script>

<template>
    <AuthenticatedLayout title="Network Tree">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    Member Tree
                </h2>
            </div>
        </template>

        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-2 flex justify-between">
                <div class="relative w-full mr-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <InputIconWrapper>
                        <template #icon>
                            <font-awesome-icon
                                icon="fa-solid fa-search"
                                class="flex-shrink-0 w-5 h-5 cursor-pointer"
                                aria-hidden="true"
                            />
                        </template>
                        <Input withIcon id="name" type="text" placeholder="Name / Email" class="block w-full" v-model="search" @keydown="handleKeyDown" />
                    </InputIconWrapper>
                    <button type="submit" class="absolute right-1 bottom-2 py-2.5 text-gray-500 hover:text-dark-eval-4 font-medium rounded-full w-8 h-8 text-sm"><font-awesome-icon
                        icon="fa-solid fa-x"
                        class="flex-shrink-0 w-3 h-3 cursor-pointer"
                        aria-hidden="true"
                        @click="clearField"
                    /></button>
                </div>
                <Button class="justify-center gap-2 rounded-full" iconOnly v-slot="{ iconSizeClasses }" @click="resetField">
                    <font-awesome-icon
                        icon="fa-solid fa-rotate-right"
                        :class="iconSizeClasses"
                        aria-hidden="true"
                    />
                </Button>
            </div>
        </div>

        <div v-if="search">
            <div v-if="isLoading" class="w-full flex justify-center mt-4">
                <Loading />
            </div>
            <div v-else>
                <NetworkChild
                    v-if="search"
                    :node="root"
                    @onClick="nodeWasClicked"
                />
            </div>
        </div>
        <div v-else>
            <div class="flex items-center p-4 mb-4 text-sm text-blue-800 rounded-lg bg-blue-50 dark:bg-dark-eval-2 dark:text-blue-300" role="alert">
                <font-awesome-icon
                    icon="fa-solid fa-search"
                    class="flex-shrink-0 w-5 h-5 cursor-pointer"
                    aria-hidden="true"
                />
                <span class="sr-only">Info</span>
                <div class="ml-4">
                    <span class="font-medium">Search Name/Email. </span>
                </div>
            </div>
        </div>


    </AuthenticatedLayout>
</template>
