<script setup>
import { ref } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import TextInput from "../../Components/TextInput.vue";
import {
    faServer,
    faPlus,
    faCheck,
    faTimes,
    faClock,
    faDatabase,
    faArrowRight,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({ tenants: Array });

const page = usePage();

const showCreateForm = ref(false);

const form = useForm({
    name: "",
    domain: "",
});

const submitForm = () => {
    form.post("/admin/tenants", {
        onSuccess: () => {
            showCreateForm.value = false;
            form.reset();
        },
    });
};

const getStatusIcon = (status) => {
    switch (status) {
        case "active":
            return faCheck;
        case "failed":
            return faTimes;
        default:
            return faClock;
    }
};

const getStatusColor = (status) => {
    switch (status) {
        case "active":
            return "text-green-600 bg-green-100";
        case "failed":
            return "text-red-600 bg-red-100";
        default:
            return "text-yellow-600 bg-yellow-100";
    }
};
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1
                            class="text-3xl font-bold text-gray-900 flex items-center"
                        >
                            <font-awesome-icon
                                :icon="faServer"
                                class="mr-3 text-blue-600"
                            />
                            Gestión de Tenants
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Administra los clientes y sus bases de datos
                            dedicadas
                        </p>
                    </div>
                    <Button @click="showCreateForm = !showCreateForm">
                        <font-awesome-icon :icon="faPlus" class="mr-2" />
                        Nuevo Tenant
                    </Button>
                </div>
            </div>

            <!-- Flash Messages -->
            <div
                v-if="page?.props?.flash?.success"
                class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <font-awesome-icon
                            :icon="faCheck"
                            class="h-5 w-5 text-green-400"
                        />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ page.props.flash.success }}
                        </p>
                    </div>
                </div>
            </div>

            <div
                v-if="page?.props?.flash?.error"
                class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <font-awesome-icon
                            :icon="faTimes"
                            class="h-5 w-5 text-red-400"
                        />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ page.props.flash.error }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Create Form -->
            <div
                v-if="showCreateForm"
                class="bg-white shadow-sm rounded-lg p-6 mb-8"
            >
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Crear Nuevo Tenant
                </h2>
                <form @submit.prevent="submitForm" class="space-y-4">
                    <TextInput
                        id="name"
                        label="Nombre del Cliente"
                        v-model="form.name"
                        placeholder="Ej: Walmart Chile"
                        :error="form.errors.name"
                        required
                    />

                    <TextInput
                        id="domain"
                        label="Dominio"
                        v-model="form.domain"
                        :error="form.errors.domain"
                        placeholder="Ej: walmart.localhost"
                        required
                    />

                    <div class="flex justify-end space-x-3">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="showCreateForm = false"
                        >
                            Cancelar
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            :processing="form.processing"
                        >
                            <font-awesome-icon
                                v-if="form.processing"
                                :icon="faClock"
                                class="mr-2 animate-spin"
                            />
                            {{
                                form.processing ? "Creando..." : "Crear Tenant"
                            }}
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Tenants List -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Tenants Registrados ({{ tenants.length }})
                    </h2>
                </div>

                <div
                    v-if="tenants.length === 0"
                    class="p-8 text-center text-gray-500"
                >
                    <font-awesome-icon
                        :icon="faDatabase"
                        class="text-4xl mb-4 text-gray-300"
                    />
                    <p>No hay tenants registrados aún.</p>
                    <p class="text-sm">
                        Haz clic en "Nuevo Tenant" para crear el primero.
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Cliente
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Dominio
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Base de Datos
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Creado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Acceder
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="tenant in tenants"
                                :key="tenant.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div
                                                class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center"
                                            >
                                                <span
                                                    class="text-white font-medium text-sm"
                                                >
                                                    {{
                                                        tenant.name
                                                            .charAt(0)
                                                            .toUpperCase()
                                                    }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div
                                                class="text-sm font-medium text-gray-900"
                                            >
                                                {{ tenant.name }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                >
                                    {{ tenant.domain }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ tenant.database || "Pendiente" }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                        :class="getStatusColor(tenant.status)"
                                    >
                                        <font-awesome-icon
                                            :icon="getStatusIcon(tenant.status)"
                                            class="mr-1"
                                        />
                                        {{ tenant.status }}
                                    </span>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{
                                        new Date(
                                            tenant.created_at
                                        ).toLocaleDateString()
                                    }}
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <Button
                                        :href="route('admin.tenants.show', tenant.id)"
                                        variant="secondary"
                                        :icon="faArrowRight"
                                        iconOnly
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
