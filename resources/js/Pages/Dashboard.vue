<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
</script>

<template>
    <AppLayout title="Dashboard">

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="card">
                    <Card>
                        <template #title>Recipebox</template>
                        <template #content>
                            <p class="m-0">
                                This is a collection of our favorite, or save-worthy recipes for our benefit, and others too.
                            </p>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
