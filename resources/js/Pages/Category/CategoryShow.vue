<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
import Badge from "primevue/badge";
import Button from "primevue/button";
import {Link, router} from "@inertiajs/vue3";

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
                    <Card v-for="recipe in recipes" :key="recipe.slug" class="relative overflow-hidden">
                        <template #header>
                            <img class="object-cover object-center w-full" src="https://placehold.co/350x150?text=No+Image+yet" />
                        </template>
                        <template #title>{{ recipe.name }}</template>
                        <template #content>
                            <Link :href="route('recipe.show', {recipe: recipe.slug})" class="absolute inset-0"></Link>
                            <Button
                                :label="`View recipe`"
                                icon="pi pi-arrow-right"
                                iconPos="right"
                                class="float-right"
                                @click="router.visit(route('recipe.show', {recipe: recipe.slug}))"
                            ></Button>
                            <div class="clear-both"></div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
