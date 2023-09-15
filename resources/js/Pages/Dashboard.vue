<script setup>
import AuthenticatedLayout from '@/Layouts/Authenticated.vue'
import Button from '@/Components/Button.vue'
import { GithubIcon } from '@/Components/Icons/brands'
import {computed, defineComponent, onMounted, ref} from 'vue'
import { Carousel, Pagination, Slide } from 'vue3-carousel'
import { TickerTape } from "vue-tradingview-widgets";

import 'vue3-carousel/dist/carousel.css'
import Modal from "@/Components/Modal.vue";

const announcementModal = ref(false);
const props = defineProps({
    firstTimeLogin: Number,
    announcements: Object,
    highlights: Object,
});
const firstTimeLogin = ref(props.firstTimeLogin);

const closeModal = () => {
    announcementModal.value = false;
    firstTimeLogin.value = 0; // Set the value of firstTimeLogin to 0 when closing the modal
    setValueInSession(0); // Update the session value to 0
};

const setValueInSession = async (value) => {
    await axios.post('/update-session', {firstTimeLogin: value})
        .then(response => {
            // Session value has been updated successfully
            console.log('Session value updated:', value);
        })
        .catch(error => {
            // Handle the error, if any
            console.error('Error updating session value:', error);
        });
};

// Execute this code when the component is mounted
onMounted(() => {
    // Check if the modal has been shown already
    if (firstTimeLogin.value === 1) {
        announcementModal.value = true;
    }
});

const getMediaUrlByCollection = (announcement, collectionName) => {
    const media = announcement.media;
    const foundMedia = media.find((m) => m.collection_name === collectionName);
    return foundMedia ? foundMedia.original_url : '';
};

</script>

<template>
    <AuthenticatedLayout :title="$t('public.Dashboard')">
        <template #header>
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <h2 class="text-xl font-semibold leading-tight">
                    {{ $t('public.Highlights') }}
                </h2>
            </div>
        </template>

        <Modal :show="announcementModal" v-if="announcements" @close="closeModal" max-width="2xl">
            <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
                <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                    </svg>
                    <span class="sr-only">Close modal</span>
                </button>
                <div class="px-6 py-6 lg:px-8 space-y-4 text-gray-500 dark:text-dark-eval-4">
                    <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">{{ announcements.title }}</h3>
                    <hr>
                    <div v-html="announcements.content"></div>
                    <div class="mt-4" v-if="announcements.media">
                        <img class="rounded-lg w-full" :src="getMediaUrlByCollection(announcements, 'announcement_image')" alt="" />
                    </div>
                </div>
            </div>
        </Modal>

        <div class="mb-6">
            <Carousel :autoplay="2000" :items-to-show="2.5" :wrap-around="true" class="my-carousel">
                <Slide v-for="(highlight, index) in highlights" :key="index" class="carousel-slide">
                    <img class="object-cover rounded-lg w-full h-56" :src="highlight.original_url" alt="">
                </Slide>

                <template #addons>
                    <Pagination class="my-carousel-pagination" />
                </template>
            </Carousel>
        </div>
        <div class="mb-6">
            <iframe scrolling="no" allowtransparency="true" frameborder="0" src="https://s.tradingview.com/embed-widget/ticker-tape/?locale=en#%7B%22symbols%22%3A%5B%7B%22proName%22%3A%22FOREXCOM%3ASPXUSD%22%2C%22title%22%3A%22S%26P%20500%22%7D%2C%7B%22proName%22%3A%22FOREXCOM%3ANSXUSD%22%2C%22title%22%3A%22US%20100%22%7D%2C%7B%22proName%22%3A%22FX_IDC%3AEURUSD%22%2C%22title%22%3A%22EUR%2FUSD%22%7D%2C%7B%22proName%22%3A%22BITSTAMP%3ABTCUSD%22%2C%22title%22%3A%22Bitcoin%22%7D%2C%7B%22proName%22%3A%22BITSTAMP%3AETHUSD%22%2C%22title%22%3A%22Ethereum%22%7D%5D%2C%22showSymbolLogo%22%3Atrue%2C%22colorTheme%22%3A%22dark%22%2C%22isTransparent%22%3Atrue%2C%22displayMode%22%3A%22adaptive%22%2C%22width%22%3A%22100%25%22%2C%22height%22%3A44%2C%22utm_source%22%3A%22currenttech.pro%22%2C%22utm_medium%22%3A%22widget%22%2C%22utm_campaign%22%3A%22ticker-tape%22%2C%22page-uri%22%3A%22currenttech.pro%2Fdemocrm%2Fpublic%2Fmember%2Fdashboard%22%7D" style="box-sizing: border-box; display: block; height: 50px; width: 100%;"></iframe>
        </div>

        <div class="p-6 overflow-hidden bg-white rounded-md shadow-md dark:bg-dark-eval-1">
            Welcome back! {{ $page.props.auth.user.first_name }}
        </div>

    </AuthenticatedLayout>
</template>

<style scoped>
.my-carousel {
    gap: 20px;
}

.carousel-slide {
    margin-right: 20px;
}

.my-carousel-pagination {
    --vc-pgn-width: 5px;
    --vc-pgn-height: 5px;
    --vc-pgn-margin: 4px;
    --vc-pgn-border-radius: 50%;
    --vc-pgn-background-color: #ffffff61;
    --vc-pgn-active-color: #fff;
}
</style>
