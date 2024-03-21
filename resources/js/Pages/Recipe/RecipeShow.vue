<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
import Chip from "primevue/chip";
import {Link, router} from "@inertiajs/vue3";
import Markdown from "@/Components/Markdown.vue";

const props = defineProps({
    recipe: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});
</script>

<template>
    <AppLayout :title="recipe.name">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card style="overflow: hidden; position: relative;" id="recipe-card-view">
                    <template #header>
                        <div class="print:hidden">
                            <img class="object-fill w-full" src="https://placehold.co/350x100?text=No+Image+yet" />
                        </div>
                    </template>
                    <template #content>
                        <div class="grid lg:grid-cols-6 lg:grid-rows-2 gap-10">
                            <div class="lg:col-span-2 row-span-3 col-start-1 row-start-2">
                                <h3 class="text-xl text-primary-200 mb-4 mt-4">Ingredients:</h3>
                                <Markdown :body="recipe.ingredients"></Markdown>
                            </div>
                            <div class="lg:col-span-6 lg:col-start-1 lg:row-start-1">
                                <h2 class="text-2xl text-primary-400">{{ recipe.name }}</h2>
                                <div>Serving: {{ recipe.serving }}</div>
                                <h5 class="text-sm mt-2">Last Updated: {{ new Intl.DateTimeFormat(Intl.DateTimeFormat().resolvedOptions().locale).format(new Date(recipe.updated_at)) }}</h5>
                                <Markdown v-if="recipe.description" :body="recipe.description" class="mt-10"></Markdown>
                            </div>
                            <div class="lg:col-span-4 lg:row-span-3 lg:col-start-3 lg:row-start-2">
                                <h3 class="text-xl text-primary-200 mb-4 mt-4">Instructions:</h3>
                                <Markdown :body="recipe.instructions"></Markdown>
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <div class="lg:flex lg:flex-row justify-evenly lg:justify-between">
                            <div class="">
                                Categories:
                                <Link v-for="category in recipe.categories" :key="category.slug" :href="route('category.show', {category:category.slug})" class="mr-2">
                                    <Chip :label="category.name"></Chip>
                                </Link>
                            </div>
                            <div class="lg:hidden mb-10"></div>
                            <div v-if="recipe.labels.length">
                                Labels:
                                <Link v-for="label in recipe.labels" :key="label.slug" :href="route('label.show', {label:label.slug})" class="mr-2">
                                    <Chip :label="label.name"></Chip>
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
