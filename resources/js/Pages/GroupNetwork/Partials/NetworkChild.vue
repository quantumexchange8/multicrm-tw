<script>
import {PlusCircleIcon, MinusCircleIcon} from "@heroicons/vue/solid";
export default {
    name: 'Tree',
    components: {PlusCircleIcon, MinusCircleIcon},
    props: {
        node: Object,
        depth: {
            type: Number,
            default: 0,
        }
    },
    data() {
        return {
            expanded: false,
            treeState: false
        }
    },
    methods: {
        nodeClicked() {
            this.expanded = !this.expanded
            if (!this.hasChildren) {
                this.$emit('onClick', this.node)
            }
        }
    },
    computed: {
        hasChildren() {
            const node = this.node;

            if (node.children && node.children.length > 0) {
                for (const child of node.children) {
                    if (child.name === node.name) {
                        return false;
                    }
                }
            }

            return this.node.children;
        },
        iconSizeClasses() {
            return this.node.role === 'ib' ? 'text-[#FF9E23]' : 'text-[#007BFF]';
        },
        bgColorClass() {
            return this.node.role === 'ib' ? 'bg-[#FF9E23] text-dark-eval-2' : 'bg-[#007BFF] text-dark-eval-2';
        },
    },
    mounted() {
        if (window.innerWidth < 1024) {
            this.treeState = true
        }
    },
    emits: ['onClick']
}
</script>

<template>
    <div v-if="node.name === 'No Records'">
        <div class="flex items-center p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 dark:bg-dark-eval-2 dark:text-yellow-300" role="alert">
            <svg class="flex-shrink-0 inline w-4 h-4 mr-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
            </svg>
            <span class="sr-only">Info</span>
            <div>
                <span class="font-medium">No Records!</span> Try another Name/Email.
            </div>
        </div>
    </div>
    <div v-else>
        <div
            :style="{'margin-left': `${depth * 10}px`}"
        >
            <div class="flex items-center mb-6 gap-2" v-if="!treeState">
                <div class="flex-none">
                    <div v-if="hasChildren">
                        <template v-if="expanded">
                            <!-- Show the MinusCircleIcon if expanded -->
                            <MinusCircleIcon
                                aria-hidden="true"
                                @click="nodeClicked"
                                :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                            />
                        </template>
                        <template v-else>
                            <!-- Show the PlusCircleIcon if not expanded -->
                            <PlusCircleIcon
                                aria-hidden="true"
                                @click="nodeClicked"
                                :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                            />
                        </template>
                    </div>
                </div>
                <div class="flex-auto w-1/2">
                    <div class="flex items-center p-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-dark-eval-1 dark:hover:bg-dark-eval-2 overflow-x-auto">
                        <div
                            class="flex items-center space-x-4 text-base font-bold text-gray-900 dark:text-white w-80"
                        >
                            <img
                                class="object-cover w-14 h-14 rounded-full"
                                :src="node.profile_photo ? node.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                                alt="userPic"
                            />
                            <div class="flex-col ml-3">
                                <span :class="[
                                      'text-xs text-center font-bold uppercase mr-2 px-2 py-0.5 rounded',
                                      bgColorClass
                                    ]"
                                >
                                    {{ node.role }}
                                </span>
                                <p>{{ node.name }}</p>
                                <p class="text-xs dark:text-dark-eval-4">{{ node.email }}</p>
                            </div>
                        </div>
                        <div class="inline-block h-auto min-h-[3em] w-0.5 self-stretch bg-dark-eval-4 dark:bg-[#202020] opacity-100 mx-3 my-1"></div>
                        <div class="flex items-center justify-between w-full gap-2 text-lg dark:text-white">
                            <div class="flex flex-col text-center ml-6">
                                <span>{{ node.level }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Level</span>
                            </div>
                            <div class="flex flex-col text-center">
                                <span>$ {{ node.total_group_deposit }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Group Deposit</span>
                            </div>
                            <div class="flex flex-col text-center">
                                <span>$ {{ node.total_group_withdrawal }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Group Withdrawal</span>
                            </div>
                            <div class="flex flex-col text-center">
                                <span>{{ node.total_ib }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total IB</span>
                            </div>
                            <div class="flex flex-col text-center">
                                <span>{{ node.total_member }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Client</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="flex items-center mb-6 gap-2" v-if="treeState">
                <div class="flex-none">
                    <div v-if="hasChildren">
                        <template v-if="expanded">
                            <!-- Show the MinusCircleIcon if expanded -->
                            <MinusCircleIcon
                                aria-hidden="true"
                                @click="nodeClicked"
                                :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                            />
                        </template>
                        <template v-else>
                            <!-- Show the PlusCircleIcon if not expanded -->
                            <PlusCircleIcon
                                aria-hidden="true"
                                @click="nodeClicked"
                                :class="['w-8 h-8 cursor-pointer', iconSizeClasses]"
                            />
                        </template>
                    </div>
                </div>
                <div class="flex-auto w-1/2">
                    <div class="grid grid-cols-2 p-4 text-sm text-gray-800 rounded-lg bg-gray-50 dark:bg-dark-eval-1 dark:hover:bg-dark-eval-2 overflow-x-auto">
                        <div class="flex flex-col items-center justify-center text-center text-base font-bold text-gray-900 dark:text-white">
                            <img
                                class="object-cover w-14 h-14 rounded-full"
                                :src="node.profile_photo ? node.profile_photo : 'https://img.freepik.com/free-icon/user_318-159711.jpg'"
                                alt="userPic"
                            />
                            <div class="flex-col">
                                <span
                                    :class="[
                                    'text-xs text-center font-bold uppercase px-2 py-0.5 rounded',
                                    bgColorClass
                                  ]"
                                >{{ node.role }}</span>
                                <p>{{ node.name }}</p>
                                <p class="text-xs dark:text-dark-eval-4">{{ node.email }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col w-full gap-2 text-lg dark:text-white">
                            <div class="flex flex-col text-center text-sm dark:text-white">
                                <span>Level: {{ node.level }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Group Deposit:</span><span> $ {{ node.total_group_deposit }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Group Withdrawal:</span><span> $ {{ node.total_group_withdrawal }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total IB:</span><span> {{ node.total_ib }}</span>
                                <span class="text-xs dark:text-dark-eval-4">Total Client:</span><span> {{ node.total_member }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <Tree
            v-if="expanded"
            v-for="child in node.children"
            :key="child.name"
            :node="child"
            :depth="depth + 1"
            @onClick="(node) => $emit('onClick', node)"
        />
    </div>
</template>
