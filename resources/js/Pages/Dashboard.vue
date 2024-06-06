<script setup>
import { Link, router } from "@inertiajs/vue3";
import AutoComplete from "primevue/autocomplete";
import { onMounted, ref } from "vue";

import BlogCard from "@/Components/BlogCard.vue";
import CategoryCard from "@/Components/CategoryCard.vue";
// import Markdown from "@/Components/Markdown.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    most_recent_recipes: {
        type: [Array, Object],
        default: () => [],
    },
    categories: {
        type: [Array, Object],
        default: () => [],
    },
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

const loaded = ref(false);

const recentblogs = ref([
    {
        hero_preview: "/assets/blog-img1.jpg",
        title: "Unlocking the benefits of intermittent fasting",
        slug: "unlocking-the-benefits-of-intermittent-fasting",
        excerpt:
            "Weight management and health management is the best solution for a healthier lifestyle. I am overweight and I need to lose some fats and burn some calories",
    },
    {
        hero_preview: "/assets/blog-img2.jpg",
        title: "The impact of sugar consumption on your health",
        slug: "the-impact-of-sugar-consumption-on-your-health",
        excerpt: "Unveling the hidden dangers of sugar on your body ",
    },
]);

onMounted(() => {
    router.reload({
        only: ["most_recent_recipes", "categories"],
        onFinish: () => (loaded.value = true),
    });
});
</script>

<template>
    <AppLayout title="Home">
        <div class="pb-12">
            <div class="container-fluid mx-auto">
                <div
                    class="max-h-[900px] overflow-hidden mb-12 py-20 px-6 lg:p-20 bg-[url('/assets/home-hero-bg.png')] bg-center relative"
                >
                    <div class="absolute left-0 top-16">
                        <img
                            src="/assets/hero-left-img.jpg"
                            class="mix-blend-multiply h-[800px] hidden lg:block"
                            alt=""
                        />
                    </div>
                    <div
                        class="h-[500px] sm:h-[900px] max-w-4xl relative mx-auto"
                    >
                        <div class="text-center mt-10">
                            <h1 class="text-3xl lg:text-4xl !text-[#1F1D2B]">
                                <span class="text-main-orange">Discover</span>
                                Every Recipe!
                            </h1>
                            <h4 class="text-base !text-[#1F1D2B] sm:text-lg">
                                This is a collection of our favorite, or
                                save-worthy recipes for our benefit, and others
                                too.
                            </h4>
                            <div class="w-full max-w-7xl flex-1 mx-auto mt-10">
                                <div class="simple-search relative">
                                    <div
                                        class="absolute inset-y-0 z-10 flex items-center pl-4"
                                    >
                                        <i
                                            class="pi pi-search text-surface-400"
                                        />
                                    </div>
                                    <AutoComplete
                                        id="mainSearch"
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
                                            <div
                                                v-if="slotProps.item.slug"
                                                class="ml-2"
                                            >
                                                <p
                                                    class="text-sm font-semibold leading-none text-primary"
                                                >
                                                    {{ slotProps.item.name }}
                                                </p>
                                                <Link
                                                    :href="
                                                        route('recipe.show', {
                                                            recipe: slotProps
                                                                .item.slug,
                                                        })
                                                    "
                                                    class="absolute inset-0"
                                                />
                                            </div>
                                            <div v-else class="h-full">
                                                No Result
                                            </div>
                                        </template>
                                    </AutoComplete>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="absolute right-0 top-0">
                        <img
                            src="/assets/hero-right-img.jpg"
                            class="mix-blend-multiply h-[800px] hidden lg:block"
                            alt=""
                        />
                    </div>
                </div>
            </div>
            <div class="max-w-7xl mx-auto px-6 lg:px-8">
                <!-- TODO: Make this dynamic and manageable -->
                <!-- <Markdown
          :body="'## Recipebox \n \n This is a collection of our favorite, or save-worthy recipes for our benefit, and others too.'"
        /> -->

                <!-- recent recipes -->
                <div class="mt-10">
                    <div class="mb-5 flex">
                        <div class="flex-1">
                            <h2>
                                <span class="text-main-orange"
                                    >Recently added</span
                                >
                                recipes
                            </h2>
                            <p>
                                Discover custom recipes for your home kitchen or
                                batch formulas for restaurants and commercial
                                kitchens.
                            </p>
                        </div>
                        <a
                            href="/recipes"
                            class="capitalize text-main-orange underline underline-offset-4"
                            >View All</a
                        >
                    </div>
                    <div
                        class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 transition-all"
                    >
                        <div v-if="!loaded">
                            <RecipeCard
                                v-for="skeleton in 3"
                                :key="skeleton"
                                :skeleton="true"
                            />
                        </div>
                        <transition-group name="fade">
                            <RecipeCard
                                v-for="recipe in most_recent_recipes"
                                :key="recipe.slug"
                                :recipe="recipe"
                            />
                        </transition-group>
                    </div>
                </div>

                <!-- blog -->
                <div class="mt-20">
                    <div class="mb-5 flex">
                        <div class="flex-1">
                            <h2>
                                <span class="text-main-orange"
                                    >The Severson</span
                                >
                                Blog
                            </h2>
                        </div>
                        <a
                            href="/recipes"
                            class="capitalize text-main-orange underline underline-offset-4"
                            >View All</a
                        >
                    </div>
                    <div
                        class="grid sm:grid-cols-1 md:grid-cols-2 gap-10 transition-all"
                    >
                        <div v-if="!loaded">
                            <BlogCard
                                v-for="skeleton in 2"
                                :key="skeleton"
                                :skeleton="true"
                            />
                        </div>
                        <transition-group name="fade">
                            <BlogCard
                                v-for="blog in recentblogs"
                                :key="blog.slug"
                                :blog="blog"
                            />
                        </transition-group>
                    </div>
                </div>

                <!-- categories -->
                <div class="mt-20">
                    <div class="mb-5 flex">
                        <div class="flex-1">
                            <h2>
                                <span class="text-main-orange">Explore</span>
                                categories
                            </h2>
                        </div>
                        <a
                            href="/categories"
                            class="capitalize text-main-orange underline underline-offset-4"
                            >View All</a
                        >
                    </div>
                    <div
                        class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 transition-all"
                    >
                        <div v-if="!loaded">
                            <CategoryCard
                                v-for="skeleton in 3"
                                :key="skeleton"
                                :skeleton="true"
                            />
                        </div>
                        <transition-group name="fade">
                            <CategoryCard
                                v-for="(category, index) in categories"
                                :key="index"
                                :category="category"
                            />
                        </transition-group>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style lang="scss" scoped>
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.fade-enter-to,
.fade-leave-from {
    opacity: 1;
}

.fade-enter-active,
.fade-leave-active {
    transition: opacity 1s ease;
}
</style>
