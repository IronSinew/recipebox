<script setup>
import { Link, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Card from "primevue/card";

import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({
    categories: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
});
</script>

<template>
    <AppLayout title="Categories">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="grid sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10"
                >
                    <Card
                        v-for="category in categories"
                        :key="category.id"
                        style="overflow: hidden; position: relative"
                    >
                        <template #title>
                            {{ category.name }}
                        </template>
                        <template #content>
                            <Link
                                :href="
                                    route('category.show', {
                                        category: category.slug,
                                    })
                                "
                                style="
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    bottom: 0;
                                    right: 0;
                                "
                            />
                            <Button
                                :label="`View all ${category.recipes_count} recipes`"
                                icon="pi pi-arrow-right"
                                icon-pos="right"
                                class="float-right"
                                @click="
                                    router.visit(
                                        route('category.show', {
                                            category: category.slug,
                                        }),
                                    )
                                "
                            />
                            <div class="clear-both" />
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
