<script setup>
import { Link } from "@inertiajs/vue3";
import pluralize from "pluralize";
import Card from "primevue/card";
import Chip from "primevue/chip";
import Galleria from "primevue/galleria";
import { onMounted, onUnmounted, ref } from "vue";

import Markdown from "@/Components/Markdown.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

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
        breakpoint: "1300px",
        numVisible: 4,
    },
    {
        breakpoint: "575px",
        numVisible: 1,
    },
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

    phrases.push(hours > 0 ? `${hours} ` + pluralize("hour", hours) : null);
    phrases.push(
        minutes > 0 ? `${minutes} ` + pluralize("minute", minutes) : null,
    );

    return phrases.filter((e) => e).join(", ");
};

const MinutesToDuration = (s) => {
    const days = Math.floor(s / 1440);
    s = s - days * 1440;
    const hours = Math.floor(s / 60);
    s = s - hours * 60;

    let dur = "PT";
    if (days > 0) {
        dur += days + "D";
    }
    if (hours > 0) {
        dur += hours + "H";
    }
    dur += s + "M";

    return dur;
};

const recipeMarkup = ref({
    "@context": "https://schema.org/",
    "@type": "Recipe",
    name: props.recipe.name,
    recipeYield: props.recipe.serving,
    prepTime: MinutesToDuration(props.recipe.prep_time),
    cookTime: MinutesToDuration(props.recipe.cook_time),
    totalTime: MinutesToDuration(props.recipe.total_time),
    image: [
        props.recipe.hero
            ? props.recipe.hero
            : "https://placehold.co/350x200?text=No+Image+yet",
    ],
});

onMounted(() => {
    const script = document.createElement("script");
    script.setAttribute("type", "application/ld+json");
    script.setAttribute("id", "recipe-markup");
    script.textContent = JSON.stringify(recipeMarkup.value);
    document.head.appendChild(script);
});

onUnmounted(() => {
    const element = document.getElementById("recipe-markup");
    element?.parentNode?.removeChild(element);
});
</script>

<template>
    <AppLayout :title="recipe.name">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <Card
                    id="recipe-card-view"
                    style="overflow: hidden; position: relative"
                >
                    <template #header>
                        <div v-if="recipe.hero" class="print:hidden">
                            <img
                                class="object-center object-cover w-full h-96"
                                :src="recipe.hero"
                            />
                        </div>
                    </template>
                    <template #content>
                        <div class="grid lg:grid-cols-6 lg:grid-rows-2 gap-10">
                            <div
                                class="lg:col-span-2 row-span-3 col-start-1 row-start-2"
                            >
                                <h3 class="text-xl text-primary-200 mb-4 mt-4">
                                    Ingredients:
                                </h3>
                                <Markdown :body="recipe.ingredients" />
                            </div>
                            <div
                                class="lg:col-span-6 lg:col-start-1 lg:row-start-1"
                            >
                                <h2 class="text-2xl text-primary-400">
                                    {{ recipe.name }}
                                </h2>
                                <h5 class="text-sm mb-5">
                                    Last Updated:
                                    {{
                                        new Intl.DateTimeFormat(
                                            Intl.DateTimeFormat().resolvedOptions().locale,
                                        ).format(new Date(recipe.updated_at))
                                    }}
                                </h5>
                                <div>
                                    Prep:
                                    {{
                                        humanReadableDuration(recipe.prep_time)
                                    }}
                                </div>
                                <div>
                                    Cook:
                                    {{
                                        humanReadableDuration(recipe.cook_time)
                                    }}
                                </div>
                                <div>Servings: {{ recipe.serving }}</div>
                                <Markdown
                                    v-if="recipe.description"
                                    :body="recipe.description"
                                    class="mt-10"
                                />
                                <Galleria
                                    v-model:activeIndex="mediaIndex"
                                    v-model:visible="mediaDisplay"
                                    :value="recipe.media"
                                    :responsive-options="responsiveOptions"
                                    :num-visible="5"
                                    :circular="true"
                                    :full-screen="true"
                                    :show-thumbnails="false"
                                    :show-item-navigators="true"
                                    class="w-full max-h-64"
                                >
                                    <template #item="slotProps">
                                        <img
                                            :src="slotProps.item.original_url"
                                            alt=""
                                            class="w-full cursor-pointer max-w-7xl"
                                            @click="mediaDisplay = false"
                                        />
                                    </template>
                                    <template #thumbnail="slotProps">
                                        <img
                                            :src="slotProps.item.preview_url"
                                            alt=""
                                            class=""
                                        />
                                    </template>
                                </Galleria>
                                <div
                                    v-if="recipe.media"
                                    class="flex flex-wrap w-full mt-5"
                                >
                                    <div class="gallery-container mx-auto">
                                        <div class="gallery">
                                            <img
                                                v-for="(
                                                    image, index
                                                ) of recipe.media"
                                                :key="index"
                                                :src="image.preview_url"
                                                class="cursor-pointer"
                                                alt=""
                                                @click="imageClick(index)"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="lg:col-span-4 lg:row-span-3 lg:col-start-3 lg:row-start-2"
                            >
                                <h3 class="text-xl text-primary-200 mb-4 mt-4">
                                    Instructions:
                                </h3>
                                <Markdown :body="recipe.instructions" />
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <div
                            class="lg:flex lg:flex-row justify-evenly lg:justify-between"
                        >
                            <div class="">
                                Categories:
                                <Link
                                    v-for="category in recipe.categories"
                                    :key="category.slug"
                                    :href="
                                        route('category.show', {
                                            category: category.slug,
                                        })
                                    "
                                    class="mr-2"
                                >
                                    <Chip :label="category.name" />
                                </Link>
                            </div>
                            <div class="lg:hidden mb-10" />
                            <div v-if="recipe.labels.length">
                                Labels:
                                <Link
                                    v-for="label in recipe.labels"
                                    :key="label.slug"
                                    :href="
                                        route('label.show', {
                                            label: label.slug,
                                        })
                                    "
                                    class="mr-2"
                                >
                                    <Chip :label="label.name" />
                                </Link>
                            </div>
                        </div>
                    </template>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
<style lang="scss">
@media (width >= 720px) {
    .gallery-container {
        display: grid;
        place-items: center;
        min-height: 1vh;
    }
    .gallery {
        --size: 100px;
        display: grid;
        grid-template-columns: repeat(6, var(--size));
        grid-auto-rows: var(--size);
        margin-bottom: var(--size);
        place-items: start center;
        gap: 5px;

        &:has(:hover) img:not(:hover),
        &:has(:focus) img:not(:focus) {
            filter: brightness(0.5) contrast(0.5);
        }

        & img {
            object-fit: cover;
            width: calc(var(--size) * 2);
            height: calc(var(--size) * 2);
            clip-path: path(
                "M90,10 C100,0 100,0 110,10 190,90 190,90 190,90 200,100 200,100 190,110 190,110 110,190 110,190 100,200 100,200 90,190 90,190 10,110 10,110 0,100 0,100 10,90Z"
            );
            transition:
                clip-path 0.25s,
                filter 0.75s;
            grid-column: auto / span 2;
            border-radius: 5px;

            &:nth-child(5n - 1) {
                grid-column: 2 / span 2;
            }

            &:hover,
            &:focus {
                clip-path: path(
                    "M0,0 C0,0 200,0 200,0 200,0 200,100 200,100 200,100 200,200 200,200 200,200 100,200 100,200 100,200 100,200 0,200 0,200 0,100 0,100 0,100 0,100 0,100Z"
                );
                z-index: 1;
                transition:
                    clip-path 0.25s,
                    filter 0.25s;
            }

            &:focus {
                outline: 1px dashed black;
                outline-offset: -5px;
            }
        }
    }
}
</style>
