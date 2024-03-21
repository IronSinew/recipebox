<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import RecipeCard from "@/Components/RecipeCard.vue";

const props = defineProps({
    category: {
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
    <AppLayout :title="`${category.name} Category`">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-5xl text-surface-0 mb-10">{{ category.name }}</h1>
                <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                    <RecipeCard v-for="recipe in recipes" :key="recipe.slug" :recipe="recipe"></RecipeCard>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
