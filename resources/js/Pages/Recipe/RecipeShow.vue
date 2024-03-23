<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
import Chip from "primevue/chip";
import Galleria from "primevue/galleria";
import {Link, router} from "@inertiajs/vue3";
import Markdown from "@/Components/Markdown.vue";
import {ref} from "vue";
import pluralize from "pluralize";

const props = defineProps({
    recipe: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});

const responsiveOptions = ref([
    {
        breakpoint: '1300px',
        numVisible: 4
    },
    {
        breakpoint: '575px',
        numVisible: 1
    }
]);
const mediaIndex = ref(0);
const mediaDisplay = ref(false);

const imageClick = (index) => {
    mediaIndex.value = index;
    mediaDisplay.value = true;
};

const humanReadableDuration = (durationInMinutes) => {
    const hours = Math.floor(durationInMinutes / 60);
    const minutes = Math.floor(durationInMinutes % 60);
    const phrases = [];

    phrases.push((hours > 0) ? `${hours} ` + pluralize("hour", hours) : null)
    phrases.push((minutes > 0) ? `${minutes} ` + pluralize("minute", minutes) : null);

    return phrases.filter(e => e).join(", ");
}
</script>

<template>
    <AppLayout :title="recipe.name">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card style="overflow: hidden; position: relative;" id="recipe-card-view">
                    <template #header>
                        <div v-if="recipe.hero" class="print:hidden">
                            <img class="object-center object-cover w-full h-96" :src="recipe.hero" />
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
                                <h5 class="text-sm mb-5">Last Updated: {{ new Intl.DateTimeFormat(Intl.DateTimeFormat().resolvedOptions().locale).format(new Date(recipe.updated_at)) }}</h5>
                                <div>Prep: {{ humanReadableDuration(recipe.prep_time) }}</div>
                                <div>Cook: {{ humanReadableDuration(recipe.cook_time) }}</div>
                                <div>Servings: {{ recipe.serving }}</div>
                                <Markdown v-if="recipe.description" :body="recipe.description" class="mt-10"></Markdown>
                                <Galleria v-model:activeIndex="mediaIndex"
                                          v-model:visible="mediaDisplay"
                                          :value="recipe.media"
                                          :responsiveOptions="responsiveOptions"
                                          :numVisible="5"
                                          :circular="true"
                                          :fullScreen="true"
                                          :showThumbnails="false"
                                          class="w-full max-h-64"
                                >
                                    <template #item="slotProps">
                                        <img :src="slotProps.item.original_url" alt="" class="w-full cursor-pointer" @click="mediaDisplay = false;" />
                                    </template>
                                    <template #thumbnail="slotProps">
                                        <img :src="slotProps.item.preview_url" alt="" class="" />
                                    </template>
                                </Galleria>
                                <div v-if="recipe.media" class="flex flex-wrap w-full">
                                    <div v-for="(image, index) of recipe.media" :key="index" class="pr-3">
                                        <img :src="image.preview_url" class="cursor-pointer max-h-32" alt="" @click="imageClick(index)" />
                                    </div>
                                </div>
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
