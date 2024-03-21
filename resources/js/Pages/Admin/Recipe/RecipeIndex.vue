<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Chip from "primevue/chip";
import Card from "primevue/card";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { useConfirm } from "primevue/useconfirm";
import {router, useForm, Link} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";

const props = defineProps({
    recipes: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    }
});

watch(() => props.recipes, (data, prevData) => {
    tableData.value = data;
});

onMounted(() => {
    tableData.value = props.recipes;
});

const tableData = ref();

const reloadTableData = () => {
    router.reload({ only: ['recipes'], preserveScroll: true, })
}

const saveNew = () => {
    form.post(route("admin.recipes.store"), {
        preserveScroll: true,
        onSuccess: () => {
            reloadTableData();
            form.reset()
        }
    });
}

const showNewForm = ref(false);
const form = useForm({
    name: null,
});

const confirm = useConfirm();
const deleteRowData = (data) => {
    confirm.require({
        message: "Are you sure you want to delete this recipe?",
        header: `Recipe: ${data.name}`,
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.delete(route("admin.recipes.destroy", {
                recipe: data.slug,
            }), {
                preserveScroll: true,
                onFinish: () => {
                    reloadTableData();
                }
            })
        }
    });
};

const restoreRowData = (data) => {
    router.put(route("admin.recipes.restore", {
            recipe: data.slug,
        }), {
            preserveScroll: true,
            onFinish: () => {
                reloadTableData();
            }
        })
};
</script>

<template>
    <AppLayout title="Recipe Admin">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-row mb-5">
                    <h1 class="text-5xl font-extrabold dark:text-white grow">Recipe Admin</h1>
                    <Link :href="route('admin.recipes.create')">
                        <Button label="Add New"></Button>
                    </Link>
                </div>
                <form @submit.prevent="saveNew" :class="[showNewForm ? 'scale-1 h-full' : 'scale-0 h-0']" class="transition-all ease-in-out delay-150 duration-500 mb-5">
                    <Card>
                        <template #content>
                            <h5 class="text-xl font-bold text-primary-200 mb-3">New Recipe</h5>
                                <InputText v-model="form.name" placeholder="Recipe Name"  class="font-normal"/>
                                <div :class="[form.errors.name ? 'opacity-100' : 'opacity-0']" class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold">
                                    {{ form.errors.name }}
                                </div>
                        </template>
                        <template #footer>
                            <div class="text-right">
                                <Button type="submit" severity="success" label="Save" class="text-right" :disabled="form.processing"></Button>
                            </div>
                        </template>
                    </Card>
                </form>
                <DataTable :value="tableData" tableStyle="min-width: 50rem" striped-rows>
                    <Column header="Name" class="text-surface-700 dark:text-white/70">
                        <template #body="{ data }">
                            <Link :href="route('admin.recipes.edit', {recipe: data.slug})">
                                {{ data.name }}
                            </Link>
                        </template>
                    </Column>
                    <Column header="Labels" class="">
                        <template #body="{ data }">
                            <Chip v-for="label in data.labels" :label="label.name" class="mr-2"></Chip>
                        </template>
                    </Column>
                    <Column header="Categories" class="">
                        <template #body="{ data }">
                            <Chip v-for="category in data.categories" :label="category.name" class="mr-2"></Chip>
                        </template>
                    </Column>
                    <Column header="" class="text-right">
                        <template #body="{ data }">
                            <Link :href="route('recipe.show', {recipe: data.slug})">
                                <Button
                                    severity="info"
                                    size="small"
                                    v-tooltip.top="'View'"
                                >
                                    <i class="pi pi-eye"></i>
                                </Button>
                            </Link>
                            <Link :href="route('admin.recipes.edit', {recipe: data.slug})">
                                <Button
                                    severity="help"
                                    size="small"
                                    v-tooltip.top="'Edit'"
                                    class="ml-3"
                                >
                                    <i class="pi pi-pencil"></i>
                                </Button>
                            </Link>
                            <Button
                                v-if="! data.deleted_at"
                                @click="deleteRowData(data)"
                                severity="danger"
                                size="small"
                                class="ml-3"
                                v-tooltip.top="'Delete'"
                            >
                                <i class="pi pi-trash"></i>
                            </Button>
                            <Button
                                v-else
                                @click="restoreRowData(data)"
                                severity="success"
                                size="small"
                                class="ml-3"
                                v-tooltip.top="'Restore'"
                            >
                                <i class="pi pi-replay"></i>
                            </Button>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <ConfirmDialog/>
        </div>
    </AppLayout>
</template>
