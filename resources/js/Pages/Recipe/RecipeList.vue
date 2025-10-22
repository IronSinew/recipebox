<script setup>
import { InfiniteScroll } from "@inertiajs/vue3";

import RecipeCard from "@/Components/RecipeCard.vue";

defineProps({
    label: {
        type: [Array, Object],
        required: false,
        default() {
            return {};
        },
    },
    recipes: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});
</script>

<template>
    <Head :title="`${label.name} label`" />
    <div class="px-6 lg:px-0 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-5xl mb-10">
                {{ label.name }}
            </h1>
            <InfiniteScroll data="recipes" :buffer="100">
                <div
                    class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"
                >
                    <transition-group name="fade">
                        <RecipeCard
                            v-for="recipe in recipes.data"
                            :key="recipe.slug"
                            :recipe="recipe"
                        />
                    </transition-group>
                </div>
                <template #loading>
                    <div
                        class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"
                    >
                        <RecipeCard
                            v-for="skeleton in 9"
                            :key="skeleton"
                            :skeleton="true"
                        />
                    </div>
                </template>
            </InfiniteScroll>
            <div ref="last" class="py-7" />
        </div>
    </div>
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
