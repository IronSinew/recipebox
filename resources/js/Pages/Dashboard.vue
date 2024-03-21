<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Markdown from "@/Components/Markdown.vue";
import RecipeCard from "@/Components/RecipeCard.vue";
import {onMounted, ref} from "vue";
import {router} from "@inertiajs/vue3";

defineProps({
    most_recent_recipes: {
        type: [Array, Object],
        default: [],
    }
});

const loaded = ref(false);

onMounted(() => {
    router.reload({
        only: ['most_recent_recipes'],
        onFinish: () => loaded.value = true
    })
});
</script>

<template>
    <AppLayout title="Home">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- TODO: Make this dynamic and manageable -->
                <Markdown :body="'## Recipebox \n \n This is a collection of our favorite, or save-worthy recipes for our benefit, and others too.'"/>

                <div class="mt-10">
                    <h3 class="mb-5">Recently added recipes</h3>
                    <div class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10 transition-all">
                        <RecipeCard v-if="! loaded" v-for="skeleton in 3" :skeleton="true"></RecipeCard>
                        <transition-group name="fade">
                            <RecipeCard  v-for="recipe in most_recent_recipes" :key="recipe.slug" :recipe="recipe"></RecipeCard>
                        </transition-group>
                    </div>
                </div>
                <div class="mt-10">
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
