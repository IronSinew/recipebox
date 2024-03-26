<script setup>
import {ref, onMounted, watchEffect} from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import Menubar from 'primevue/menubar';
import Button from "primevue/button";
import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import Message from 'primevue/message';

import ApplicationMark from '@/Components/ApplicationMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';

defineProps({
    title: String,
});

const page = usePage()

const menuItems = ref([
    {
        label: 'Categories',
        icon: 'pi pi-star',
        route: "category.index",
        routeGroup: "category.*",
        items: [
            {
                label: 'All Categories',
                route: 'category.index'
            }
        ],
    },
    {
        label: 'Labels',
        icon: 'pi pi-envelope',
        route: 'label.index',
    }
]);

onMounted(() => {
    axios.get(route("category.list")).then((res) => {
        res.data.forEach((row) => {
            menuItems.value[0].items.push({
                label: row.name,
                route: "category.show",
                routeObject: {category: row.slug},
                url: route("category.show", {category: row.slug})
            })
        });
    })

    if (page.props.can_access_admin_area) {
        menuItems.value.push({
            label: 'Admin',
            icon: 'pi pi-lock',
            routeGroup: "admin.*",
            items: [
                {
                    label: 'Labels',
                    route: 'admin.labels.index'
                },
                {
                    label: 'Categories',
                    route: 'admin.categories.index'
                },
                {
                    label: 'Recipes',
                    route: 'admin.recipes.index'
                },
                {
                    label: 'Users',
                    route: 'admin.users.index'
                }
            ],
        })
    }
});

const selectedRecipe = ref();
const filteredRecipes = ref();
const searchRecipes = async (event) => {
    await axios.post(route("search.simple"), { search: event.query }).then(function (response) {
        if (response.data.length > 0) {
            filteredRecipes.value = response.data;
        } else {
            filteredRecipes.value = [
                {
                    name: "No Results Found",
                },
            ];
        }
    });
};

const bannerStyle = ref('success');
const bannerMessage = ref('');

watchEffect(async () => {
    bannerStyle.value = page.props.jetstream.flash?.bannerStyle || 'success';
    bannerMessage.value = page.props.jetstream.flash?.banner || '';
});

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div>
        <Head :title="title" />

        <div class="min-h-screen bg-gray-100 dark:bg-surface-800">
            <nav class="bg-white dark:bg-gray-700">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto print:hidden">
                    <Menubar :model="menuItems">
                        <template #start>
                            <Link :href="route('home')">
                                <ApplicationMark class="block h-9 w-auto mr-2" />
                            </Link>
                        </template>
                        <template #item="{ item, props, hasSubmenu, root }">
                            <Link v-if="item.route && typeof item.items === 'undefined'" :href="route(item.route, item.routeObject || null)">
                                <a v-ripple :href="route(item.route, item.routeObject || null)" v-bind="props.action" :class="{'dark:bg-gray-800':route().current(item.route, item.routeObject || null)}">
                                    <span :class="item.icon" />
                                    <span class="ml-2">{{ item.label }}</span>
                                </a>
                            </Link>
                            <a v-else v-ripple class="flex items-center" v-bind="props.action" :class="{'dark:bg-gray-800':route().current(item.routeGroup, item.routeObject || null)}">
                                <span :class="item.icon" />
                                <span class="ml-2">{{ item.label }}</span>
                                <Badge v-if="item.badge" :class="{ 'ml-auto': !root, 'ml-2': root }" :value="item.badge" />
                                <i v-if="hasSubmenu" :class="['pi pi-angle-down text-primary-500 dark:text-primary-400-500 dark:text-primary-400', { 'pi-angle-down ml-2': root, 'pi-angle-right ml-auto': !root }]"></i>
                            </a>
                        </template>
                        <template #end>
                            <div class="flex items-center gap-2">
                                <div class="w-full max-w-xl flex-1">
                                    <div class="simple-search relative">
                                        <div class="absolute inset-y-0 z-10 flex items-center pl-2">
                                            <i class="pi pi-search text-surface-400" />
                                        </div>
                                        <AutoComplete
                                            v-model="selectedRecipe"
                                            :suggestions="filteredRecipes"
                                            field="name"
                                            placeholder="Search Recipes..."
                                            class="w-full autocomplete-search"
                                            scroll-height="350px"
                                            id="mainSearch"
                                            @item-select="selectedRecipe = ''"
                                            @complete="searchRecipes($event)"
                                        >
                                            <template #item="slotProps">
                                                <div v-if="slotProps.item.slug" class="ml-2">
                                                    <p class="text-sm font-semibold leading-none text-primary">
                                                        {{ slotProps.item.name }}
                                                    </p>
                                                    <Link
                                                        :href="route('recipe.show', {recipe:slotProps.item.slug})"
                                                        class="absolute inset-0"
                                                    >
                                                    </Link>
                                                </div>
                                                <div v-else class="h-full">
                                                    No Result
                                                </div>
                                            </template>
                                        </AutoComplete>
                                    </div>
                                </div>
                                <Dropdown v-if="$page.props.auth?.user" align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <span v-else class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                                {{ $page.props.auth?.user?.name }}

                                                <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>

                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>

                                        <div class="border-t border-gray-200 dark:border-gray-600" />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                                <Link v-else :href="route('login')">
                                    <Button link>
                                        Login
                                    </Button>
                                </Link>
                            </div>
                        </template>
                    </Menubar>
                </div>
            </nav>

            <!-- Page Heading -->
            <header v-if="$slots.header" class="bg-white dark:bg-gray-800 shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Message v-if="bannerMessage" :severity="bannerStyle">{{ bannerMessage }}</Message>
            </div>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
<style lang="scss">
    div#mainSearch.autocomplete-search > input {
        padding-left: 35px !important;
    }
</style>
