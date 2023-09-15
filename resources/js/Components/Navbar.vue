<script setup>
import {computed, onMounted, onUnmounted, ref, watchEffect} from 'vue'
import {Link, usePage} from '@inertiajs/vue3'
import {
    SunIcon,
    MoonIcon,
    SearchIcon,
    MenuIcon,
    XIcon,
    BellIcon,
} from '@heroicons/vue/outline'
import {
    MailIcon,
    MailOpenIcon
} from '@heroicons/vue/solid'
import {
    handleScroll,
    isDark,
    scrolling,
    toggleDarkMode,
    sidebarState,
} from '@/Composables'
import Button from '@/Components/Button.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import Dropdown from '@/Components/Dropdown.vue'
import DropdownLink from '@/Components/DropdownLink.vue'
import { LangIconDark, LangIconWhite } from '@/Components/Icons/outline'
import toast from "@/Composables/toast.js";
import Modal from "@/Components/Modal.vue";
import {loadLanguageAsync} from "laravel-vue-i18n";

onMounted(() => {
    document.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
    document.removeEventListener('scroll', handleScroll)
})

const page = usePage()
const user = computed(() => page.props.auth.user)

function copyReferralCode() {
    const referralCode = document.querySelector('#userReferralCode').textContent;
    const url = window.location.origin + '/register/' + referralCode;

    const tempInput = document.createElement('input');
    tempInput.value = url;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand('copy');
    document.body.removeChild(tempInput);

    toast.add({
        message: "Copy Successful!",
    });
}

const notificationModal = ref(false);
const notificationContent = ref(null);
const clickedNotificationIds = ref([]);

const openNotificationModal = async (notification) => {
    notificationModal.value = true;
    notificationContent.value = notification;

    clickedNotificationIds.value.push(notification.id);

    if (notification.read_at === null) {
        await axios.post('/markAsRead', {id: notification.id})
            .then(response => {
                console.log('marked')
            })
            .catch(error => {
                // Handle the error, if any
                console.error('Error marking notification as read:', error);
            });
    }
}

const closeModal = () => {
    notificationModal.value = false
}

const changeLanguage = async (langVal) => {
    try {
        await loadLanguageAsync(langVal);
        await axios.get(`/locale/${langVal}`);
    } catch (error) {
        console.error('Error changing locale:', error);
    }
};

</script>

<template>
    <nav
        aria-label="secondary"
        :class="[
            'sticky top-0 z-10 px-6 py-4 bg-white flex items-center justify-between transition-transform duration-500 dark:bg-dark-eval-1',
            {
                '-translate-y-full': scrolling.down,
                'translate-y-0': scrolling.up,
            },
        ]"
    >
        <div class="flex items-center gap-2">
            <Button
                iconOnly
                variant="secondary"
                type="button"
                @click="() => { toggleDarkMode() }"
                v-slot="{ iconSizeClasses }"
                class="md:hidden"
                srText="Toggle dark mode"
            >
                <MoonIcon
                    v-show="!isDark"
                    aria-hidden="true"
                    :class="iconSizeClasses"
                />
                <SunIcon
                    v-show="isDark"
                    aria-hidden="true"
                    :class="iconSizeClasses"
                />
            </Button>
        </div>
        <div class="flex items-center gap-2">
            <Button
                iconOnly
                variant="secondary"
                type="button"
                @click="() => { toggleDarkMode() }"
                v-slot="{ iconSizeClasses }"
                class="hidden md:inline-flex"
                srText="Toggle dark mode"
            >
                <MoonIcon
                    v-show="!isDark"
                    aria-hidden="true"
                    class="text-dark-eval-1"
                    :class="iconSizeClasses"
                />
                <SunIcon
                    v-show="isDark"
                    aria-hidden="true"
                    :class="iconSizeClasses"
                />
            </Button>

            <Dropdown align="right">
                <template #trigger>
                    <Button
                        iconOnly
                        variant="secondary"
                        type="button"
                        v-slot="{ iconSizeClasses }"
                        class="hidden md:inline-flex"
                        srText="Toggle dark mode"
                    >
                        <LangIconDark
                            v-show="isDark"
                            aria-hidden="true"
                            :class="iconSizeClasses"
                        />
                        <LangIconWhite
                            v-show="!isDark"
                            aria-hidden="true"
                            :class="iconSizeClasses"
                        />
                    </Button>
                </template>
                <template #content>
                    <DropdownLink @click="changeLanguage('en')">
                        <div class="inline-flex items-center gap-2">
                            <img class="w-5 h-5 rounded-full" src="/assets/flags/gb.png" alt="Rounded Flag">
                            English
                        </div>
                    </DropdownLink>
                    <DropdownLink @click="changeLanguage('tw')">
                        <div class="inline-flex items-center gap-2">
                            <img class="w-5 h-5 rounded-full" src="/assets/flags/tw.png" alt="Rounded Flag">
                            中文 (繁)
                        </div>
                    </DropdownLink>
                </template>
            </Dropdown>

            <Dropdown align="right" width="96">
                <template #trigger>
                    <Button
                        iconOnly
                        variant="secondary"
                        type="button"
                        v-slot="{ iconSizeClasses }"
                        class="text-[#212529] hidden md:inline-flex"
                        srText="Toggle dark mode"
                    >
                        <BellIcon
                            aria-hidden="true"
                            :class="iconSizeClasses"
                        />
                        <span v-show="$page.props.auth.user.unreadNotifications.length !== 0" class="top-2 left-5 absolute w-3 h-3 bg-[#F19828] border-2 border-[#212529] dark:border-dark-eval-2 rounded-full"></span>

                    </Button>
                </template>
                <template #content>
                    <DropdownLink v-for="notification in $page.props.auth.user.unreadNotifications" class="border-b-2 border-dark-eval-3" @click="openNotificationModal(notification)">
                        <div class="inline-flex gap-2 w-full">
                            <MailOpenIcon
                                v-if="clickedNotificationIds.includes(notification.id)"
                                aria-hidden="true"
                                class="w-9 h-9"
                            />
                            <MailIcon
                                v-else
                                aria-hidden="true"
                                class="w-10 h-10 text-[#FF9E23]"
                            />
                            <div class="flex flex-col">
                                <span class="my-auto dark:text-white">{{ notification.data['title'] }}</span>
                                <span class="my-auto" style="font-size: 10px">{{ notification.data['post_date'] }}</span>
                            </div>
                        </div>
                    </DropdownLink>
                    <DropdownLink v-for="readNotification in $page.props.auth.user.readNotifications" class="border-b-2 border-dark-eval-3" @click="openNotificationModal(readNotification)">
                        <div class="inline-flex gap-2">
                            <MailOpenIcon
                                v-show="readNotification && readNotification.read_at"
                                aria-hidden="true"
                                class="w-9 h-9"
                            />
                            <div class="flex flex-col">
                                <span class="my-auto dark:text-white">{{ readNotification.data['title'] }}</span>
                                <span class="my-auto" style="font-size: 10px">{{ readNotification.data['post_date'] }}</span>
                            </div>
                        </div>
                    </DropdownLink>
                    <DropdownLink v-if="$page.props.auth.user.notifications.length === 0" class="border-b-2 border-dark-eval-3">
                        <div class="dark:text-dark-eval-5">No notifications</div>
                    </DropdownLink>
                </template>
            </Dropdown>

            <!-- Dropdown -->
            <Dropdown align="right" width="48">
                <template #trigger>
                    <span class="inline-flex rounded-md">
                        <button
                            type="button"
                            class="inline-flex items-center px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out bg-white border border-transparent rounded-md hover:text-gray-700 focus:outline-none focus:ring focus:ring-gray-500 focus:ring-offset-1 focus:ring-offset-white dark:focus:ring-offset-dark-eval-1 dark:bg-dark-eval-1 dark:text-gray-400 dark:hover:text-gray-200"
                        >
                            <img
                                class="h-10 w-10 rounded-full mr-4"
                                :src="$page.props.auth.user.picture ? $page.props.auth.user.picture : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                                alt="ProfilePic"
                            >
                            <span class="bottom-1 left-9 absolute" v-if="$page.props.auth.user.kyc_approval === 'approve'">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 4.62782C6.99207 3.93274 9.00567 3.36562 12.0043 2C14.7389 3.28721 16.7869 3.75207 21 4.62782V10.0169C21 15.6811 17.3751 20.7097 12.0013 22.5002C6.62605 20.7097 3 15.68 3 10.0143V4.62782Z" fill="#05C46B"/><path d="M8.5 12L11.0923 14.5923C11.3075 14.8075 11.6633 14.7822 11.8459 14.5388L16 9" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <span class="bottom-1 left-9 absolute" v-else>
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3 4.62782C6.99207 3.93274 9.00567 3.36562 12.0043 2C14.7389 3.28721 16.7869 3.75207 21 4.62782V10.0169C21 15.6811 17.3751 20.7097 12.0013 22.5002C6.62605 20.7097 3 15.68 3 10.0143V4.62782Z" fill="#FF3F34"/><path d="M13.0002 17C13.0002 17.5523 12.5524 18 12.0001 18C11.4478 18 11 17.5523 11 17C11 16.4477 11.4478 16 12.0001 16C12.5524 16 13.0002 16.4477 13.0002 17Z" fill="white"/><path d="M12 13V7" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                            </span>
                            <div class="flex flex-col text-left">
                                <span>{{ $page.props.auth.user.first_name }}</span>
                                <span>{{ $page.props.auth.user.email }}</span>
                            </div>
                            <svg
                                class="ml-2 -mr-0.5 h-4 w-4"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </span>
                </template>

                <template #content>
                    <DropdownLink
                        :href="route('profile.edit')"
                    >
                        Profile
                    </DropdownLink>

                    <DropdownLink
                        :href="route('logout')"
                        method="post"
                        as="button"
                    >
                        Log Out
                    </DropdownLink>

                    <hr class="h-px my-1 bg-gray-200 border-0 dark:bg-gray-700">

                    <DropdownLink
                        :href="''"
                        as="button"
                        class="grid grid-cols-1 text-center dark:hover:bg-transparent"
                        @click.stop.prevent="copyReferralCode"
                    >
                        <p class="text-xs my-2 text-gray-500 dark:text-dark-eval-4">Promote Register Code</p>
                        <span id="userReferralCode" class="text-white">{{ user.referral_code }}</span>
                        <Button class="w-full justify-center my-2" @click.stop.prevent="copyReferralCode">Copy</Button>
                    </DropdownLink>
                </template>
            </Dropdown>
        </div>
    </nav>

    <!-- Mobile bottom bar -->
    <div
        :class="[
            'fixed inset-x-0 bottom-0 z-50 flex items-center justify-between px-4 py-4 sm:px-6 transition-transform duration-500 bg-white md:hidden dark:bg-dark-eval-1',
            {
                'translate-y-full': scrolling.down,
                'translate-y-0': scrolling.up,
            },
        ]"
    >
        <Button
            iconOnly
            variant="secondary"
            type="button"
            v-slot="{ iconSizeClasses }"
            srText="Search"
        >
            <SearchIcon aria-hidden="true" :class="iconSizeClasses" />
        </Button>

        <Link :href="route('dashboard')">
            <ApplicationLogo class="w-10 h-10" />
            <span class="sr-only">K UI</span>
        </Link>

        <Button
            iconOnly
            variant="secondary"
            type="button"
            @click="sidebarState.isOpen = !sidebarState.isOpen"
            v-slot="{ iconSizeClasses }"
            class="md:hidden"
            srText="Search"
        >
            <MenuIcon
                v-show="!sidebarState.isOpen"
                aria-hidden="true"
                :class="iconSizeClasses"
            />
            <XIcon
                v-show="sidebarState.isOpen"
                aria-hidden="true"
                :class="iconSizeClasses"
            />
        </Button>
    </div>

    <!-- Action Modal -->
    <Modal :show="notificationModal" @close="closeModal" max-width="2xl">
        <div class="relative bg-white rounded-lg shadow dark:bg-dark-eval-1">
            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" @click="closeModal">
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
                <span class="sr-only">Close modal</span>
            </button>
            <div class="px-6 py-6 lg:px-8 space-y-4 text-gray-500 dark:text-dark-eval-4">
                <h3 class="mb-2 text-xl font-medium text-gray-900 dark:text-white">Notification</h3>
                <hr>
                <div class="dark:text-white">{{ notificationContent.data['title'] }}</div>
                <div class="dark:text-white text-sm" v-html="notificationContent.data['content']"></div>
                <div class="mt-4">
                    <img class="rounded-lg w-full" :src="notificationContent.data['image']" alt="" />
                </div>
            </div>
        </div>
    </Modal>

</template>
