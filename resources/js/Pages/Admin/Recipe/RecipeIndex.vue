<script setup>
import { Link, router } from "@inertiajs/vue3";
import Button from "primevue/button";
import Chip from "primevue/chip";
import Column from "primevue/column";
import ConfirmDialog from "primevue/confirmdialog";
import DataTable from "primevue/datatable";
import { useConfirm } from "primevue/useconfirm";
import { onMounted, ref, watch } from "vue";

const props = defineProps({
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
        tableData.value = data;
    },
);

onMounted(() => {
    tableData.value = props.recipes;
});

const tableData = ref();

const reloadTableData = () => {
    router.reload({ only: ["recipes"], preserveScroll: true });
};

const confirm = useConfirm();
const deleteRowData = (data) => {
    confirm.require({
        message: "Are you sure you want to delete this recipe?",
        header: `Recipe: ${data.name}`,
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.delete(
                route("admin.recipes.destroy", {
                    recipe: data.slug,
                }),
                {
                    preserveScroll: true,
                    onFinish: () => {
                        reloadTableData();
                    },
                },
            );
        },
    });
};

const restoreRowData = (data) => {
    router.put(
        route("admin.recipes.restore", {
            recipe: data.slug,
        }),
        {
            preserveScroll: true,
            onFinish: () => {
                reloadTableData();
            },
        },
    );
};
</script>

<template>
    <Head :title="`Recipe Admin`" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row mb-5">
                <h1 class="text-5xl font-extrabold dark:text-white grow">
                    Recipe Admin
                </h1>
                <Link :href="route('admin.recipes.create')">
                    <Button label="Add New" />
                </Link>
            </div>
            <DataTable
                :value="tableData"
                table-style="min-width: 50rem"
                striped-rows
            >
                <Column
                    header="Name"
                    class="text-surface-700 dark:text-white/70"
                >
                    <template #body="{ data }">
                        <Link
                            :href="
                                route('admin.recipes.edit', {
                                    recipe: data.slug,
                                })
                            "
                        >
                            {{ data.name }}
                        </Link>
                    </template>
                </Column>
                <Column header="Labels" class="">
                    <template #body="{ data }">
                        <Chip
                            v-for="label in data.labels"
                            :key="label.slug"
                            :label="label.name"
                            class="mr-2"
                        />
                    </template>
                </Column>
                <Column header="Categories" class="">
                    <template #body="{ data }">
                        <Chip
                            v-for="category in data.categories"
                            :key="category.slug"
                            :label="category.name"
                            class="mr-2"
                        />
                    </template>
                </Column>
                <Column header="" class="text-right whitespace-nowrap">
                    <template #body="{ data }">
                        <Link
                            :href="route('recipe.show', { recipe: data.slug })"
                        >
                            <Button
                                v-tooltip.top="'View'"
                                severity="info"
                                size="small"
                            >
                                <i class="pi pi-eye" />
                            </Button>
                        </Link>
                        <Link
                            :href="
                                route('admin.recipes.edit', {
                                    recipe: data.slug,
                                })
                            "
                        >
                            <Button
                                v-tooltip.top="'Edit'"
                                severity="help"
                                size="small"
                                class="ml-3"
                            >
                                <i class="pi pi-pencil" />
                            </Button>
                        </Link>
                        <Button
                            v-if="!data.deleted_at"
                            v-tooltip.top="'Delete'"
                            severity="danger"
                            size="small"
                            class="ml-3"
                            @click="deleteRowData(data)"
                        >
                            <i class="pi pi-trash" />
                        </Button>
                        <Button
                            v-else
                            v-tooltip.top="'Restore'"
                            severity="success"
                            size="small"
                            class="ml-3"
                            @click="restoreRowData(data)"
                        >
                            <i class="pi pi-replay" />
                        </Button>
                    </template>
                </Column>
            </DataTable>
        </div>
        <ConfirmDialog />
    </div>
</template>
