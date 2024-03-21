<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Breadcrumb from 'primevue/breadcrumb';
import Button from "primevue/button";
import Card from "primevue/card";
import Fieldset from "primevue/fieldset";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Listbox from "primevue/listbox";
import Textarea from 'primevue/textarea'
import {router, useForm, Link} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";
import Markdown from "@/Components/Markdown.vue";

const props = defineProps({
    recipe: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
    categories: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
    labels: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    }
});

const labelOptions = ref([]);
const categoryOptions = ref([]);

onMounted(() => {
    for (const [key, value] of Object.entries(props.recipe)) {
        form[key] = value;
    }
    for (const [key, value] of Object.entries(props.recipe.categories || [])) {
        form.categoriesSelected.push(value.slug);
    }
    for (const [key, value] of Object.entries(props.recipe.labels || [])) {
        form.labelsSelected.push(value.slug);
    }
    labelOptions.value = props.labels;
    categoryOptions.value = props.categories;
});

const form = useForm({
    id: null,
    name: null,
    serving: null,
    ingredients: null,
    instructions: null,
    cook_time: 0,
    prep_time: 0,
    description: null,
    labelsSelected: [],
    categoriesSelected: [],
});

const save = () => {
    if (form.id) {
        form.put(route("admin.recipes.update", {recipe: form.slug}));
    } else {
        form.post(route("admin.recipes.store"), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset()
            }
        });
    }
}
const breadcrumbs = ref([
    { label: 'Recipes', url: route("admin.recipes.index") },
    { label: props.recipe.slug ? `Edit Recipe` : "New Recipe" }
]);
</script>

<template>
    <AppLayout title="Recipe Admin">
        <template #header>
            <Breadcrumb :model="breadcrumbs"/>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="save" class="mb-5">
                    <Card>
                        <template #content>
                            <h5 class="text-xl font-bold text-primary-200 mb-3">
                                {{ props.recipe.slug ? `Edit ${props.recipe.name}` : "New Recipe" }}
                            </h5>
                            <div class="grid grid-cols-3 gap-x-6">
                                <div class="my-7">
                                    <label>Recipe Name</label>
                                    <InputText v-model="form.name" class="font-normal w-full"/>
                                    <div :class="[form.errors.name ? 'opacity-100' : 'opacity-0']"
                                         class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                        {{ form.errors.name }}
                                    </div>
                                </div>

                                <div class="my-7">
                                    <label>Serving (ie, 2 dozen cookies, 4 servings, etc)</label>
                                    <InputText v-model="form.serving" class="font-normal w-full"/>
                                    <div :class="[form.errors.serving ? 'opacity-100' : 'opacity-0']"
                                         class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                        {{ form.errors.serving }}
                                    </div>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-x-6">
                                <div class="my-7">
                                    <label>Prep Time (in whole minutes)</label>
                                    <InputNumber v-model="form.prep_time" class="font-normal w-full"/>
                                    <div :class="[form.errors.prep_time ? 'opacity-100' : 'opacity-0']"
                                         class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                        {{ form.errors.prep_time }}
                                    </div>
                                </div>

                                <div class="my-7">
                                    <label>Cook Time (in whole minutes)</label>
                                    <InputNumber v-model="form.cook_time" class="font-normal w-full"/>
                                    <div :class="[form.errors.cook_time ? 'opacity-100' : 'opacity-0']"
                                         class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                        {{ form.errors.cook_time }}
                                    </div>
                                </div>

                                <div class="my-7">
                                    Total time: {{ form.cook_time + form.prep_time }}m
                                </div>
                            </div>

                            <div class="my-7">
                                <div class="text-xs mb-5">
                                    These all support <a class="text-primary-400" href="https://www.markdownguide.org/cheat-sheet/" target="_new">markdown</a>
                                </div>
                                <div class="grid md:grid-cols-2 gap-x-6">
                                    <Fieldset legend="Ingredients">
                                        <div class="h-64">
                                            <Textarea v-model="form.ingredients" class="font-normal w-full h-full"/>
                                            <div :class="[form.errors.ingredients ? 'opacity-100' : 'opacity-0']"
                                                 class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                                {{ form.errors.ingredients }}
                                            </div>
                                        </div>
                                    </Fieldset>
                                    <div>
                                        <div class="mb-3">Preview</div>
                                        <Markdown :body="form.ingredients"></Markdown>
                                    </div>
                                </div>
                            </div>

                            <div class="my-7">
                                <div class="grid md:grid-cols-2 gap-x-6">
                                    <Fieldset legend="Instructions">
                                        <div class="h-64">
                                            <Textarea v-model="form.instructions" class="font-normal w-full h-full"/>
                                            <div :class="[form.errors.instructions ? 'opacity-100' : 'opacity-0']"
                                                 class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                                {{ form.errors.instructions }}
                                            </div>
                                        </div>
                                    </Fieldset>
                                    <div>
                                        <div class="mb-3">Preview</div>
                                        <Markdown :body="form.instructions"></Markdown>
                                    </div>
                                </div>
                            </div>

                            <div class="my-7">
                                <div class="grid md:grid-cols-2 gap-x-6">
                                    <Fieldset legend="Description">
                                        <div class="h-64">
                                            <Textarea v-model="form.description" class="font-normal w-full h-full"/>
                                            <div :class="[form.errors.description ? 'opacity-100' : 'opacity-0']"
                                                 class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                                {{ form.errors.description }}
                                            </div>
                                        </div>
                                    </Fieldset>
                                    <div>
                                        <div class="mb-3">Preview</div>
                                        <Markdown :body="form.description"></Markdown>
                                    </div>
                                </div>
                            </div>

                            <div class="my-7">
                                <div class="grid md:grid-cols-2 gap-x-6">
                                    <Fieldset legend="Categories">
                                        <Listbox v-model="form.categoriesSelected" :options="categoryOptions" multiple optionValue="slug" optionLabel="name" class="w-full" />
                                    </Fieldset>
                                    <Fieldset legend="Labels">
                                        <Listbox v-model="form.labelsSelected" :options="labelOptions" multiple optionValue="slug" optionLabel="name" class="w-full" />
                                    </Fieldset>
                                </div>
                            </div>
                        </template>
                        <template #footer>
                            <div class="text-right">
                                <Button type="submit" severity="success" label="Save" class="text-right"
                                        :disabled="form.processing"></Button>
                            </div>
                        </template>
                    </Card>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
