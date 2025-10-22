<script setup>
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import AutoComplete from "primevue/autocomplete";
import Badge from "primevue/badge";
import Button from "primevue/button";
import Chip from "primevue/chip";
import Dialog from "primevue/dialog";
import Menubar from "primevue/menubar";
import Message from "primevue/message";
import { computed, nextTick, onMounted, ref, watch, watchEffect } from "vue";

import ApplicationMark from "@/Components/ApplicationMark.vue";
import DarkModeButton from "@/Components/DarkModeButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DropdownLink from "@/Components/DropdownLink.vue";

defineProps({
    title: String,
});

const page = usePage();

const searchModal = ref({
    visible: false,
    position: "top",
});

const menuItems = ref([
    {
        label: "Categories",
        route: "category.index",
        routeGroup: "category.*",
        items: [
            {
                label: "All Categories",
                route: "category.index",
            },
        ],
    },
    {
        label: "Labels",
        route: "label.index",
        routeGroup: "label.*",
    },
    {
        label: "All Recipes",
        route: "recipe.all",
        routeGroup: "recipe.all",
    },
]);

onMounted(() => {
    axios.get(route("category.list")).then((res) => {
        res.data.forEach((row) => {
            menuItems.value[0].items.push({
                label: row.name,
                route: "category.show",
                routeObject: { category: row.slug },
                url: route("category.show", { category: row.slug }),
            });
        });
    });

    if (page.props.can_access_admin_area) {
        menuItems.value.push({
            label: "Admin",
            icon: "pi pi-lock",
            routeGroup: "admin.*",
            items: [
                {
                    label: "Labels",
                    route: "admin.labels.index",
                },
                {
                    label: "Categories",
                    route: "admin.categories.index",
                },
                {
                    label: "Recipes",
                    route: "admin.recipes.index",
                },
                {
                    label: "Users",
                    route: "admin.users.index",
                },
            ],
        });
    }
});

const selectedRecipe = ref();
const filteredRecipes = ref();
const searchRecipes = async (event) => {
    await axios
        .post(route("search.simple"), { search: event.query })
        .then(function (response) {
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

const bannerStyle = ref("success");
const bannerMessage = ref("");

watchEffect(async () => {
    bannerStyle.value = page.props.jetstream.flash?.bannerStyle || "success";
    bannerMessage.value = page.props.jetstream.flash?.banner || "";
});

const autocompleteInput = ref(null);

const logout = () => {
    router.post(route("logout"));
};

const animate = ref(true);
const isLeaving = ref(false);

const theRoute = ref(route());

const currentRoute = computed(() => usePage().props.current_route_salted);
watch(currentRoute, async () => {
    isLeaving.value = true;
    animate.value = false;

    // Wait for leave animation to complete
    await new Promise((resolve) => setTimeout(resolve, 300));

    isLeaving.value = false;
    nextTick(() => {
        animate.value = true;
    });
});

router.on("navigate", () => {
    theRoute.value = route();
});
const current = computed(() => {
    return theRoute.value.current();
});
</script>

<template>
    <Dialog
        v-model:visible="searchModal.visible"
        :position="searchModal.position"
        modal
        dismissable-mask
        :style="{ width: '90%', 'max-width': '500px', 'margin-top': '15px' }"
        :show-header="false"
        :pt="{
            content: 'p-0',
            mask: {
                style: 'backdrop-filter: blur(2px)',
            },
        }"
        @show="
            nextTick(() => {
                autocompleteInput.$el.children[0].focus();
            })
        "
    >
        <div class="w-full max-w-xl flex-1">
            <div class="simple-search relative">
                <div class="absolute inset-y-0 z-10 flex items-center pl-2">
                    <i class="pi pi-search text-surface-400" />
                </div>
                <AutoComplete
                    id="mainSearch"
                    ref="autocompleteInput"
                    v-model="selectedRecipe"
                    :suggestions="filteredRecipes"
                    field="name"
                    placeholder="Search Recipes..."
                    class="w-full autocomplete-search"
                    scroll-height="350px"
                    @item-select="selectedRecipe = ''"
                    @complete="searchRecipes($event)"
                >
                    <template #item="slotProps">
                        <div v-if="slotProps.item.slug" class="ml-2">
                            <p
                                class="text-sm font-semibold leading-none text-primary"
                            >
                                {{ slotProps.item.name }}
                            </p>
                            <Link
                                :href="
                                    route('recipe.show', {
                                        recipe: slotProps.item.slug,
                                    })
                                "
                                class="absolute inset-0"
                                @click="searchModal.visible = false"
                            />
                        </div>
                        <div v-else class="h-full">No Result</div>
                    </template>
                </AutoComplete>
            </div>
        </div>
    </Dialog>
    <div>
        <Head :title="title" />

        <div class="min-h-screen transition-all duration-300">
            <nav class="sticky top-0 z-10">
                <!-- Primary Navigation Menu -->
                <div class="max-w-7xl mx-auto print:hidden">
                    <Menubar :model="menuItems">
                        <template #start>
                            <Link :href="route('home')">
                                <ApplicationMark
                                    class="block h-14 w-auto mr-2"
                                />
                            </Link>
                        </template>
                        <template #item="{ item, props, hasSubmenu, root }">
                            <Link
                                v-if="
                                    item.route &&
                                    typeof item.items === 'undefined'
                                "
                                :href="
                                    route(item.route, item.routeObject || null)
                                "
                            >
                                <span
                                    v-ripple
                                    v-bind="props.action"
                                    :class="{
                                        'bg-gray-100 dark:bg-surface-600/80 rounded-lg':
                                            theRoute.current(item.route) &&
                                            root,
                                    }"
                                >
                                    <!-- <span :class="item.icon" /> -->
                                    <span
                                        :class="{ 'ml-2': item.icon?.length }"
                                        >{{ item.label }}</span
                                    >
                                </span>
                            </Link>
                            <a
                                v-else
                                v-ripple
                                class="flex items-center"
                                v-bind="props.action"
                                :class="{
                                    'bg-gray-100 mx-2 dark:bg-surface-600/80 rounded-lg':
                                        theRoute.current(item.routeGroup),
                                }"
                            >
                                <!-- <span :class="item.icon" /> -->
                                <span :class="{ 'ml-2': item.icon?.length }">{{
                                    item.label
                                }}</span>
                                <Badge
                                    v-if="item.badge"
                                    :class="{ 'ml-auto': !root, 'ml-2': root }"
                                    :value="item.badge"
                                />
                                <i
                                    v-if="hasSubmenu"
                                    :class="[
                                        'pi pi-angle-down text-primary-500 dark:text-primary-400-500 dark:text-primary-400',
                                        {
                                            'pi-angle-down ml-2': root,
                                            'pi-angle-right ml-auto': !root,
                                        },
                                    ]"
                                />
                            </a>
                        </template>
                        <template #end>
                            <div class="flex items-center gap-2">
                                <Chip
                                    class="dark:bg-surface-800 p-2 cursor-pointer"
                                    @click="
                                        searchModal.visible =
                                            !searchModal.visible
                                    "
                                >
                                    <i class="pi pi-search" />
                                </Chip>
                                <DarkModeButton />
                                <Dropdown
                                    v-if="$page.props.auth?.user"
                                    align="right"
                                    width="48"
                                >
                                    <template #trigger>
                                        <button
                                            v-if="
                                                $page.props.jetstream
                                                    .managesProfilePhotos
                                            "
                                            class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition"
                                        >
                                            <img
                                                class="h-8 w-8 rounded-full object-cover"
                                                :src="
                                                    $page.props.auth.user
                                                        .profile_photo_url
                                                "
                                                :alt="
                                                    $page.props.auth.user.name
                                                "
                                            />
                                        </button>

                                        <span
                                            v-else
                                            class="inline-flex rounded-md"
                                        >
                                            <button
                                                type="button"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-white bg-gray-100 mx-2 dark:bg-surface-600/80 rounded-lg hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150"
                                            >
                                                {{
                                                    $page.props.auth?.user?.name
                                                }}

                                                <svg
                                                    class="ms-2 -me-0.5 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke-width="1.5"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        stroke-linecap="round"
                                                        stroke-linejoin="round"
                                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <!-- Account Management -->
                                        <div
                                            class="block px-4 py-2 text-xs text-gray-400"
                                        >
                                            Manage Account
                                        </div>

                                        <DropdownLink
                                            :href="route('profile.show')"
                                        >
                                            Profile
                                        </DropdownLink>

                                        <DropdownLink
                                            v-if="
                                                $page.props.jetstream
                                                    .hasApiFeatures
                                            "
                                            :href="route('api-tokens.index')"
                                        >
                                            API Tokens
                                        </DropdownLink>

                                        <div
                                            class="border-t border-gray-200 dark:border-gray-600"
                                        />

                                        <!-- Authentication -->
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                                <Link
                                    v-else
                                    v-tooltip="'Login'"
                                    :href="route('login')"
                                >
                                    <Chip class="dark:bg-surface-800 p-2">
                                        <i class="pi pi-user" />
                                    </Chip>
                                </Link>
                            </div>
                        </template>
                    </Menubar>
                </div>
            </nav>

            <!-- Page Heading -->
            <header id="header-slot" class="bg-white dark:bg-gray-800 shadow">
                <div
                    class="header-wrapper max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8"
                >
                    <slot name="header" />
                </div>
            </header>

            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Message v-if="bannerMessage" :severity="bannerStyle">
                    {{ bannerMessage }}
                </Message>
            </div>

            <transition name="slide-fade" mode="out-in" appear>
                <div
                    v-if="animate"
                    :class="{ 'is-leaving': isLeaving }"
                    class="mx-auto container h-full"
                >
                    <!-- Page Content -->
                    <main>
                        <slot />
                    </main>
                </div>
            </transition>

            <div class="max-w-7xl mx-auto overflow-hidden mb-12 px-8">
                <div
                    class="w-full h-full flex mx-auto py-20 bg-[url('/assets/sub-footer-bg.png')] bg-no-repeat bg-center relative"
                >
                    <div class="max-w-4xl relative mx-auto">
                        <div class="text-center mt-10 px-4">
                            <h1 class="text-3xl lg:text-4xl !text-[#1F1D2B]">
                                <span class="text-main-orange">Recreate</span> a
                                Recipe!
                            </h1>
                            <p class="mt-4 lg:text-lg !text-[#1F1D2B]">
                                Figure out what's in your food! Have you ever
                                looked at a commercial food product and wondered
                                how to make it? Not a problem! Use this
                                professional technique to recreate a recipe from
                                any nutrition label
                            </p>
                            <Link :href="route('login')" class="mt-6 block">
                                <Button class="!text-white">
                                    LEARN MORE
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <footer>
                <div
                    class="mx-auto max-w-7xl px-6 py-12 md:flex md:items-center md:justify-between lg:px-8"
                >
                    <div
                        class="flex justify-center items-center space-x-6 md:order-2 mb-4 lg:mb-0"
                    >
                        <a
                            href="#"
                            class="text-gray-400 hover:text-gray-500"
                            target="_blank"
                            ><span class="sr-only">Facebook</span>
                            <svg
                                fill="currentColor"
                                viewBox="0 0 24 24"
                                class="h-6 w-6"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd"
                                ></path>
                            </svg> </a
                        ><a
                            href="#"
                            class="text-gray-400 hover:text-gray-500"
                            target="_blank"
                            ><span class="sr-only">LinkedIn</span>
                            <svg
                                fill="currentColor"
                                viewBox="0 0 24 24"
                                class="h-6 w-6"
                                aria-hidden="true"
                            >
                                <path
                                    fill-rule="evenodd"
                                    clip-rule="evenodd"
                                    d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"
                                ></path>
                            </svg> </a
                        ><a
                            href="#"
                            class="text-gray-400 hover:text-gray-500"
                            target="_blank"
                            ><span class="sr-only">X</span>
                            <svg
                                fill="currentColor"
                                viewBox="0 0 24 24"
                                class="h-6 w-6"
                                aria-hidden="true"
                            >
                                <path
                                    d="M13.6823 10.6218L20.2391 3H18.6854L12.9921 9.61788L8.44486 3H3.2002L10.0765 13.0074L3.2002 21H4.75404L10.7663 14.0113L15.5685 21H20.8131L13.6819 10.6218H13.6823ZM11.5541 13.0956L10.8574 12.0991L5.31391 4.16971H7.70053L12.1742 10.5689L12.8709 11.5655L18.6861 19.8835H16.2995L11.5541 13.096V13.0956Z"
                                ></path>
                            </svg>
                        </a>
                    </div>
                    <p class="text-center text-sm leading-5 mb-10 lg:mb-0">
                        Recipebox &copy; {{ new Date().getFullYear() }} All
                        rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>
</template>
<style lang="scss">
#header-slot > .header-wrapper {
    display: none;
}

#header-slot > .header-wrapper:has(*) {
    display: block;
}

div#mainSearch.autocomplete-search > input {
    padding-left: 45px !important;
    @apply py-4 w-full;
}

/*
  Slide Fade Transition
*/
.slide-right-enter-active,
.slide-left-enter-active,
.slide-bottom-enter-active,
.slide-top-enter-active {
    transition: all 0.3s ease-out;
}

.slide-right-leave-active,
.slide-left-leave-active,
.slide-bottom-leave-active,
.slide-top-enter-active {
    transition: all 0.3s;
}

.slide-right-enter-from,
.slide-right-leave-to {
    transform: translateX(50%);
    opacity: 0;
}

.slide-left-enter-from,
.slide-left-leave-to {
    transform: translateX(-50%);
    opacity: 0;
}

.slide-bottom-enter-from,
.slide-bottom-leave-to {
    transform: translateY(25%);
    opacity: 0;
}

.slide-top-enter-from,
.slide-top-leave-to {
    transform: translateY(-50%);
    opacity: 0;
}

/*
  Fade Transition
*/
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease-out;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/*
  Slide Fade Combined Transition
*/
.slide-fade-enter-active,
.slide-fade-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-enter-from {
    transform: translateY(10px);
    opacity: 0;
}

.slide-fade-leave-to {
    transform: translateY(-10px);
    opacity: 0;
}

/* Add appear transition classes */
.slide-fade-appear-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-fade-appear-from {
    transform: translateY(10px);
    opacity: 0;
}

/* Optional: Add class for leaving state */
.is-leaving {
    pointer-events: none;
}
</style>
