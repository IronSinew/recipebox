<script setup>
import Skeleton from "primevue/skeleton";
import {Link, router} from "@inertiajs/vue3";

defineProps({
    recipe: {
        type: [Array, Object],
        default: {},
    },
    skeleton: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <div v-if="skeleton" class="recipe-card skeleton relative">
        <Skeleton height="200px" width="100%"></Skeleton>
        <div class="m-4">
            <p class="mb-2">
                <Skeleton width="100%" height="2rem"></Skeleton>
            </p>
            <p>
                <Skeleton width="100%" height="1rem"></Skeleton>
            </p>
        </div>
    </div>
    <div v-else class="recipe-card transition ease-in-out hover:scale-110 relative">
        <img
            src="https://i.ibb.co/tpCdNcX/stew.jpg"
            alt="stew"
            class="w-full h-32 sm:h-48 object-cover"
        />
        <div class="m-4">
            <span class="font-bold">{{ recipe.name }}</span>
            <span class="block text-sm mt-1">
                Servings:
                {{ recipe.serving}}
            </span>
        </div>
        <div class="badge">
            <svg
                class="w-5 inline-block"
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
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
            <span class="ml-1">{{ recipe.total_time }} m</span>
        </div>
        <Link v-if="! skeleton" :href="route('recipe.show', {recipe: recipe.slug})" class="absolute inset-0"></Link>
    </div>
</template>
<style lang="scss">
.recipe-card {
    --tw-bg-opacity: 0.66;
    background-color: rgb(var(--surface-600));
    color: rgb(var(--surface-0));
    border-radius: 0.25rem;
    overflow: hidden;
    position: relative;
    --tw-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

.badge {
    --tw-bg-opacity: 1;
    background-color: rgb(var(--surface-900));
    border-radius: 9999px;
    font-weight: 700;
    font-size: 0.75rem;
    line-height: 1rem;
    margin-top: 0.5rem;
    margin-left: 0.5rem;
    padding: 0.5rem;
    position: absolute;
    top: 0px;
    color: rgb(var(--primary-600));
    text-transform: uppercase;
}
</style>
