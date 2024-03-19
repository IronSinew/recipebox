<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Card from "primevue/card";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import DataTable from "primevue/datatable";
import InputText from "primevue/inputtext";
import Button from "primevue/button";
import { useConfirm } from "primevue/useconfirm";
import {router, useForm} from "@inertiajs/vue3";
import {onMounted, ref, watch} from "vue";

const props = defineProps({
    categories: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    }
});

watch(() => props.categories, (data, prevData) => {
    tableData.value = data;
});

onMounted(() => {
    tableData.value = props.categories;
});

const tableData = ref();
const columns = [
    { field: 'name', header: 'Name' },
    { field: 'order_column', header: 'Order' },
    { field: 'recipes_count', header: '# Recipes' },
];

const reloadTableData = () => {
    router.reload({ only: ['categories'], preserveScroll: true, })
}

const onRowReorder = (event) => {
    tableData.value = event.value;
    axios.post(route("admin.categories.set_order"), tableData.value.map(row => row.id)).then(() => {
        reloadTableData();
    })
};

const saveNew = () => {
    form.post(route("admin.categories.store"), {
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
        message: "Are you sure you want to delete this category?",
        header: `Category: ${data.name}`,
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.delete(route("admin.categories.destroy", {
                slug: data.slug,
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
    router.put(route("admin.categories.restore", {
            slug: data.slug,
        }), {
            preserveScroll: true,
            onFinish: () => {
                reloadTableData();
            }
        })
};
</script>

<template>
    <AppLayout title="Category Admin">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-row mb-5">
                    <h1 class="text-5xl font-extrabold dark:text-white grow">Category Admin</h1>
                    <Button @click="showNewForm = !showNewForm" label="Add New"></Button>
                </div>
                <form @submit.prevent="saveNew" :class="[showNewForm ? 'scale-1 h-full' : 'scale-0 h-0']" class="transition-all ease-in-out delay-150 duration-500 mb-5">
                    <Card>
                        <template #content>
                            <h5 class="text-xl font-bold text-primary-200 mb-3">New Category</h5>
                                <InputText v-model="form.name" placeholder="Category Name"  class="font-normal"/>
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
                <DataTable :value="tableData" tableStyle="min-width: 50rem" @rowReorder="onRowReorder" pt:transition="transition-all ease-in-out delay-150 duration-500">
                    <Column rowReorder headerStyle="width: 3rem" :reorderableColumn="false" />
                    <Column v-for="col of columns" :key="col.field" :field="col.field" :header="col.header"></Column>
                    <Column header="" class="text-right">
                        <template #body="{ data }">
                            <Button
                                v-if="! data.deleted_at"
                                @click="deleteRowData(data)"
                                severity="danger"
                                size="small"
                                class="ml-5"
                                v-tooltip.top="'Delete'"
                            >
                                <i class="pi pi-trash"></i>
                            </Button>
                            <Button
                                v-else
                                @click="restoreRowData(data)"
                                severity="success"
                                size="small"
                                class="ml-5"
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
