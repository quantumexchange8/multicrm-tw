<script setup>
import PerfectScrollbar from '@/Components/PerfectScrollbar.vue'
import SidebarLink from '@/Components/Sidebar/SidebarLink.vue'
import { DashboardIcon } from '@/Components/Icons/outline'
import SidebarCollapsible from '@/Components/Sidebar/SidebarCollapsible.vue'
import SidebarCollapsibleItem from '@/Components/Sidebar/SidebarCollapsibleItem.vue'
import { TemplateIcon } from '@heroicons/vue/outline'
import { ClipboardListIcon } from '@heroicons/vue/outline'
import { usePermission } from '@/Composables/permissions.js'

import { library } from '@fortawesome/fontawesome-svg-core'
/* import specific icons */
import {
    faGaugeHigh,
    faClipboardUser,
    faMoneyBillTransfer,
    faSitemap,
    faHandshakeSimple,
    faAddressCard, faBookOpen
} from '@fortawesome/free-solid-svg-icons'
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
library.add(faGaugeHigh, faClipboardUser, faMoneyBillTransfer, faSitemap, faHandshakeSimple, faAddressCard, faBookOpen)

const { hasRole } = usePermission();
</script>

<template>
    <PerfectScrollbar
        tagname="nav"
        aria-label="main"
        class="relative flex flex-col flex-1 max-h-full gap-4 px-3"
    >
        <SidebarLink
            title="Dashboard"
            :href="route('dashboard')"
            :active="route().current('dashboard')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-gauge-high"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <SidebarLink
            title="Account Info"
            :href="route('account_info.account_info')"
            :active="route().current('account_info.account_info')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-clipboard-user"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <SidebarCollapsible
            v-if="hasRole('ib')"
            title="Trading"
            :active="route().current('trading.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-handshake-simple"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('trading.rebate_summary')"
                title="Rebate Summary"
                :active="route().current('trading.rebate_summary')"
            />
        </SidebarCollapsible>

        <SidebarCollapsible
            title="Group Network"
            :active="route().current('group_network.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-sitemap"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem
                :href="route('group_network.network_tree')"
                title="Network Tree"
                :active="route().current('group_network.network_tree')"
            />
            <SidebarCollapsibleItem
                v-if="hasRole('ib')"
                :href="route('group_network.rebate_allocation')"
                title="Rebate Allocation"
                :active="route().current('group_network.rebate_allocation')"
            />
        </SidebarCollapsible>

        <SidebarLink
            title="Transaction"
            :href="route('transaction')"
            :active="route().current('transaction')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-money-bill-transfer"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <SidebarLink
            v-if="hasRole('ib')"
            title="Report"
            :href="route('report.listing')"
            :active="route().current('report.listing')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-book-open"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>

        <SidebarLink
            title="User Profile"
            :href="route('profile.detail')"
            :active="route().current('profile.*')"
        >
            <template #icon>
                <font-awesome-icon
                    icon="fa-solid fa-address-card"
                    class="flex-shrink-0 w-5 h-5"
                    aria-hidden="true"
                />
            </template>
        </SidebarLink>
<!--        <SidebarCollapsible-->
<!--            title="Components"-->
<!--            :active="route().current('components.*')"-->
<!--        >-->
<!--            <template #icon>-->
<!--                <TemplateIcon-->
<!--                    class="flex-shrink-0 w-6 h-6"-->
<!--                    aria-hidden="true"-->
<!--                />-->
<!--            </template>-->

<!--            <SidebarCollapsibleItem-->
<!--                :href="route('components.buttons')"-->
<!--                title="Buttons"-->
<!--                :active="route().current('components.buttons')"-->
<!--            />-->
<!--        </SidebarCollapsible>-->

        <!-- Examples -->
        <!--
        => External link example
        <SidebarLink
            title="Github"
            href="https://github.com/kamona-wd/kui-laravel-breeze"
            external
            target="_blank"
        >
        </SidebarLink>

        => Collapsible examples
        <SidebarCollapsible title="Users" :active="$page.url.startsWith('/users')">
            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="$page.url === '/users/index'" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="$page.url === '/users/create'" />
        </SidebarCollapsible>

        <SidebarCollapsible title="Users" :active="usePage().url.value.startsWith('/users')">
            <template #icon>
                <UserIcon
                    class="flex-shrink-0 w-6 h-6"
                    aria-hidden="true"
                />
            </template>

            <SidebarCollapsibleItem :href="route('users.index')" title="List" :active="route().current('users.index')" />
            <SidebarCollapsibleItem :href="route('users.create')" title="Create" :active="route().current('users.create')" />
        </SidebarCollapsible>-->
    </PerfectScrollbar>
</template>
