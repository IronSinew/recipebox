<script setup>
import { useIntersectionObserver } from "@vueuse/core";
import { onMounted, ref, watch } from "vue";

import RecipeCard from "@/Components/RecipeCard.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
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

watch(
    () => props.recipes,
    (data) => {
        paginationLink.value = data.recipes.next_cursor;
        recipeList.value = data.recipes.data;
    },
);

const paginationLink = ref(null);
const recipeList = ref([]);
const last = ref(null);
const noMoreScrolling = ref(false);
const isLoading = ref(false);

onMounted(() => {
    paginationLink.value = props.recipes.next_cursor;
    if (paginationLink.value === null) {
        noMoreScrolling.value = true;
    }
    recipeList.value = props.recipes.data;
});

useIntersectionObserver(last, ([{ isIntersecting }]) => {
    if (!isIntersecting) {
        return;
    }

    onLoadMore();
});

const onLoadMore = () => {
    if (noMoreScrolling.value) {
        return;
    }
    isLoading.value = true;

    axios
        .get(`${window.location.href}?cursor=${paginationLink.value}`)
        .then((req) => {
            if (req.data.next_cursor === null) {
                noMoreScrolling.value = true;
            }

            recipeList.value = [...recipeList.value, ...req.data.data];
            paginationLink.value = req.data.next_cursor;
        })
        .finally(() => {
            isLoading.value = false;
        });
};
</script>

<template>
    <AppLayout :title="`${label.name} label`">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <h1 class="text-5xl mb-10">
                    {{ label.name }}
                </h1>
                <div
                    class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"
                >
                    <transition-group name="fade">
                        <RecipeCard
                            v-for="recipe in recipeList"
                            :key="recipe.slug"
                            :recipe="recipe"
                        />
                    </transition-group>
                    <div v-if="isLoading">
                        <RecipeCard
                            v-for="skeleton in 9"
                            :key="skeleton"
                            :skeleton="true"
                        />
                    </div>
                </div>
                <div ref="last" class="py-7" />
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
