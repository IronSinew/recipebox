<script setup>
import { Link } from "@inertiajs/vue3";
import pluralize from "pluralize";
import Card from "primevue/card";
import Chip from "primevue/chip";
import Galleria from "primevue/galleria";
import { onMounted, onUnmounted, ref } from "vue";

import Markdown from "@/Components/Markdown.vue";
import SocialShare from "@/Components/SocialShare.vue";
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
        props.recipe.hero ? props.recipe.hero : "/assets/no-recipe-img.png",
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
        <div class="angled-recipe-header relative">
            <img
                :src="
                    recipe.hero ? recipe.hero : '/assets/recipe-hero-no-img.png'
                "
                :alt="recipe.name"
                class="w-full object-cover"
            />
        </div>

        <div class="px-6 lg:px-0 py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-3xl lg:text-6xl text-center font-normal">
                    {{ recipe.name }}
                </h1>
                <SocialShare />
                <Markdown
                    v-if="recipe.description"
                    :body="recipe.description"
                    class="my-10 text-center"
                />

                <div class="dark:bg-surface-700 bg-main-dark p-6 my-10">
                    <div
                        class="flex flex-col lg:flex-row gap-4 justify-between"
                    >
                        <div class="flex items-center gap-10 text-white">
                            <div class="flex items-center">
                                <svg
                                    class="w-5 inline-block"
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    width="24"
                                    height="24"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                                <span class="ml-1">{{
                                    humanReadableDuration(recipe.total_time)
                                }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg
                                    class="w-5 inline-block"
                                    viewBox="0 0 24 20"
                                    fill="none"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M2.5 13.5C2.5 8.25 6.75 4 12 4C17.24 4 21.5 8.25 21.5 13.5"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M2.5 13.5C2.5 8.25 6.75 4 12 4C17.24 4 21.5 8.25 21.5 13.5"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M10.2 6.87805C7.88002 7.50805 6.06002 9.29805 5.40002 11.6081"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M1 15.5H23"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M10.417 2.79201C10.407 1.91201 11.117 1.20201 11.997 1.20201C12.867 1.19201 13.577 1.90201 13.577 2.78201"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                    <path
                                        d="M2.5 15.5C3.23 16.32 3.887 18.5 6.22 18.5H17.77C20.1 18.5 20.76 16.32 21.49 15.5"
                                        stroke="white"
                                        stroke-width="1.3"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                    />
                                </svg>

                                <span class="ml-1">
                                    {{ recipe.serving }}
                                </span>
                            </div>
                        </div>
                        <a
                            class="flex gap-2 text-white items-center"
                            href="#"
                            onclick="window.print();return false;"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 100 100"
                                width="30"
                                height="30"
                            >
                                <path
                                    fill="#fff"
                                    d="M31.8 71.5v13.8h36.9v-23H31.8v9.2zm4.6-4.6h27.7v4.6H36.4v-4.6zm0 9.2h27.7v4.6H36.4v-4.6zM68.7 30V16.1H31.8v23.1h36.9z"
                                ></path>
                                <path
                                    fill="#fff"
                                    d="M77.9 30h-4.6v13.8H27.2V30h-4.6c-4.6 0-9.2 4.6-9.2 9.2v23.1c0 4.6 4.6 9.2 9.2 9.2h4.6V57.7h46.1v13.8h4.6c4.6 0 9.2-4.6 9.2-9.2V39.2c.1-4.6-4.5-9.2-9.2-9.2z"
                                ></path>
                            </svg>
                            <p class="text-white">Print Recipe</p>
                        </a>
                    </div>
                </div>
                <Card
                    id="recipe-card-view"
                    style="
                        overflow: hidden;
                        position: relative;
                        box-shadow: none;
                    "
                >
                    <template #content>
                        <div
                            v-if="recipe.media.length"
                            class="flex flex-wrap w-full mt-5"
                        >
                            <div class="w-full">
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
                            class="grid grid-cols-1 lg:grid-cols-3 gap-4 px-4 py-4"
                        >
                            <div class="w-full">
                                <h3 class="text-3xl mb-4 mt-4">Ingredients:</h3>
                                <Markdown :body="recipe.ingredients" />
                            </div>

                            <div class="w-full lg:col-span-2">
                                <h3 class="text-3xl mb-4 mt-4">
                                    Instructions:
                                </h3>
                                <Markdown :body="recipe.instructions" />
                            </div>
                        </div>
                    </template>
                    <template #footer>
                        <div
                            class="px-4 lg:flex lg:flex-row justify-evenly lg:justify-between"
                        >
                            <div class="">
                                Categories:
                                <div class="flex gap-2 mt-2">
                                    <Link
                                        v-for="category in recipe.categories"
                                        :key="category.slug"
                                        :href="
                                            route('category.show', {
                                                category: category.slug,
                                            })
                                        "
                                    >
                                        <Chip :label="category.name" />
                                    </Link>
                                </div>
                            </div>
                            <div class="lg:hidden mb-10" />
                            <div v-if="recipe.labels.length">
                                Labels:
                                <div class="flex gap-2 mt-2">
                                    <Link
                                        v-for="label in recipe.labels"
                                        :key="label.slug"
                                        :href="
                                            route('label.show', {
                                                label: label.slug,
                                            })
                                        "
                                    >
                                        <Chip :label="label.name" />
                                    </Link>
                                </div>
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
