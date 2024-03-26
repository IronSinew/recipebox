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
    users: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    },
    roles: {
        type: [Array, Object],
        required: false,
        default() {
            return [];
        },
    }
});

watch(() => props.users, (data, prevData) => {
    tableData.value = data;
});

onMounted(() => {
    tableData.value = props.users;
});

const tableData = ref();

const reloadTableData = () => {
    router.reload({ only: ['users'], preserveScroll: true, })
}

const form = useForm({
    name: null,
});

const confirm = useConfirm();
const deleteRowData = (data) => {
    confirm.require({
        message: "Are you sure you want to delete this user? This is permanent!",
        header: `User: ${data.name}`,
        icon: "pi pi-exclamation-triangle",
        accept: () => {
            router.delete(route("admin.users.destroy", {
                user: data.id,
            }), {
                preserveScroll: true,
                onFinish: () => {
                    reloadTableData();
                }
            })
        }
    });
};
</script>

<template>
    <AppLayout title="User Admin">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="flex flex-row mb-5">
                    <h1 class="text-5xl font-extrabold dark:text-white grow">User Admin</h1>
                    <Link :href="route('admin.users.create')">
                        <Button label="Add New"></Button>
                    </Link>
                </div>
                <DataTable :value="tableData" tableStyle="min-width: 50rem" striped-rows>
                    <Column header="Name" class="text-surface-700 dark:text-white/70">
                        <template #body="{ data }">
                            <Link :href="route('admin.users.edit', {user: data.id})">
                                {{ data.name }}
                            </Link>
                        </template>
                    </Column>
                    <Column header="Email" class="text-surface-700 dark:text-white/70">
                        <template #body="{ data }">
                            {{ data.email }}
                        </template>
                    </Column>
                    <Column header="Recipe #" class="text-surface-700 dark:text-white/70">
                        <template #body="{ data }">
                            {{ data.recipes_count }}
                        </template>
                    </Column>
                    <Column header="Role" class="text-surface-700 dark:text-white/70">
                        <template #body="{ data }">
                            {{ data.role }}
                        </template>
                    </Column>
                    <Column header="" class="text-right">
                        <template #body="{ data }">
                            <Link :href="route('admin.users.edit', {user: data.id})">
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
                                @click="deleteRowData(data)"
                                severity="danger"
                                size="small"
                                class="ml-3"
                                v-tooltip.top="'Delete'"
                            >
                                <i class="pi pi-trash"></i>
                            </Button>
                        </template>
                    </Column>
                </DataTable>
            </div>
            <ConfirmDialog/>
        </div>
    </AppLayout>
</template>
