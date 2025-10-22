<script setup>
import { useForm } from "@inertiajs/vue3";
import Button from "primevue/button";
import Dropdown from "primevue/dropdown";
import InputText from "primevue/inputtext";
import Password from "primevue/password";
import { onMounted, ref } from "vue";

import SimpleBreadcrumb from "@/Components/SimpleBreadcrumb.vue";

const props = defineProps({
    user: {
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
    },
});

const form = useForm({
    id: null,
    name: null,
    email: null,
    password: null,
    password_confirmation: null,
    role: null,
});

const save = () => {
    if (form.id) {
        form.put(route("admin.users.update", { user: form.id }));
    } else {
        form.post(route("admin.users.store"), {
            preserveScroll: true,
            onSuccess: () => {
                form.reset();
            },
        });
    }
};
const breadcrumbs = ref([
    { label: "Users", url: route("admin.users.index") },
    { label: props.user.id ? "Edit User" : "New User" },
]);

onMounted(() => {
    for (const [key, value] of Object.entries(props.user)) {
        form[key] = value;
    }
});
</script>

<template>
    <Head :title="`${user.name} - User Admin`" />
    <SimpleBreadcrumb :data="breadcrumbs" />
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form class="mb-5" @submit.prevent="save">
                <h5 class="text-xl font-bold text-primary-200 mb-3">
                    {{ props.user.id ? `Edit ${props.user.name}` : "New User" }}
                </h5>
                <div class="grid grid-cols-3 gap-x-6">
                    <div class="my-7">
                        <label>User's Name</label>
                        <InputText
                            v-model="form.name"
                            class="font-normal w-full"
                        />
                        <div
                            :class="[
                                form.errors.name ? 'opacity-100' : 'opacity-0',
                            ]"
                            class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold"
                        >
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="my-7">
                        <label>Email</label>
                        <InputText
                            v-model="form.email"
                            class="font-normal w-full"
                        />
                        <div
                            :class="[
                                form.errors.email ? 'opacity-100' : 'opacity-0',
                            ]"
                            class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>

                    <div class="my-7">
                        <label>Role</label>
                        <Dropdown
                            v-model="form.role"
                            class="font-normal w-full"
                            :options="roles"
                            option-label="name"
                            option-value="value"
                            placeholder="Member"
                        />
                        <div
                            :class="[
                                form.errors.email ? 'opacity-100' : 'opacity-0',
                            ]"
                            class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold"
                        >
                            {{ form.errors.email }}
                        </div>
                    </div>
                </div>
                <div v-if="!props.user.id" class="grid grid-cols-3 gap-x-6">
                    <div class="my-7">
                        <label>Password</label>
                        <Password
                            v-model="form.password"
                            class="font-normal w-full"
                        />
                        <div
                            :class="[
                                form.errors.password
                                    ? 'opacity-100'
                                    : 'opacity-0',
                            ]"
                            class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold"
                        >
                            {{ form.errors.password }}
                        </div>
                    </div>

                    <div class="my-7">
                        <label>Confirm Password</label>
                        <Password
                            v-model="form.password_confirmation"
                            class="font-normal w-full"
                        />
                        <div
                            :class="[
                                form.errors.password_confirmation
                                    ? 'opacity-100'
                                    : 'opacity-0',
                            ]"
                            class="transition-opacity ease-in-out delay-150 duration-300 pt-4 text-sm text-red-500 font-bold"
                        >
                            {{ form.errors.password_confirmation }}
                        </div>
                    </div>
                </div>

                <div class="text-right">
                    <Button
                        type="submit"
                        severity="success"
                        label="Save"
                        class="text-right"
                        :disabled="form.processing"
                    />
                </div>
            </form>
        </div>
    </div>
</template>
