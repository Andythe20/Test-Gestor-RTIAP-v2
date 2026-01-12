<script setup>
import { ref, watch, onBeforeUnmount } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import Checkbox from "@/Components/Checkbox.vue";
import axios from "axios";
import {
    faServer,
    faPlus,
    faCheck,
    faTimes,
    faClock,
    faDatabase,
    faArrowRight,
    faUsers,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    tenants: { type: Array, default: () => [] },
});

const page = usePage();

// Estados de visibilidad de formularios
const showCreateForm = ref(false);
const showCreateUserForm = ref(false);

// Formularios
const logoutForm = useForm({});
const logout = () => logoutForm.post(route("logout"));

const form = useForm({
    name: "",
    path: "",
    database: "",
});

const userForm = useForm({
    name: "",
    email: "",
    password: "",
    is_admin: false,
    tenant_id: null,
});

// Configuración del Toast
const toast = ref({ show: false, type: "success", message: "" });
let toastTimer = null;

const showToast = (message, type = "success") => {
    toast.value = { show: true, type, message };

    if (toastTimer) clearTimeout(toastTimer);

    toastTimer = setTimeout(() => {
        toast.value.show = false;
    }, 3500);
};

onBeforeUnmount(() => {
    if (toastTimer) clearTimeout(toastTimer);
});

// Watcher para capturar mensajes Flash de Laravel/Inertia
watch(
    () => page.props.flash,
    (flash) => {
        console.log("Datos recibidos en flash:", flash);
        if (flash?.success) {
            showToast(flash.success, "success");

            // Limpiar y cerrar formularios tras éxito
            userForm.reset();
            form.reset();
            showCreateUserForm.value = false;
            showCreateForm.value = false;
        }
        if (flash?.error) {
            showToast(flash.error, "error");
        }
    },
    { deep: true },
);

// Lógica de creación
const createUser = () => {
    if (userForm.is_admin) userForm.tenant_id = null;

    userForm.post(route("admin.users.store"), {
        preserveScroll: true,
        onSuccess: () => {
            userForm.reset();
        },
    });
};

const submitForm = () => {
    form.post(route("admin.tenants.store"), {
        preserveScroll: true,
    });
};

// Lógica de carga de usuarios por tenant
const expandedTenants = ref({});
const tenantsUsers = ref({});
const loadingUsers = ref({});

const toggleTenantExpand = async (tenant_id) => {
    expandedTenants.value[tenant_id] = !expandedTenants.value[tenant_id];
    if (!expandedTenants.value[tenant_id]) return;
    if (tenantsUsers.value[tenant_id]) return;

    loadingUsers.value[tenant_id] = true;
    try {
        const res = await axios.get(route("admin.tenants.users", tenant_id));
        tenantsUsers.value[tenant_id] = res.data.users || [];
    } catch (e) {
        tenantsUsers.value[tenant_id] = [];
        console.error(e);
    } finally {
        loadingUsers.value[tenant_id] = false;
    }
};

// Helpers de UI
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
    <Teleport to="body">
        <Transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
            leave-active-class="transition ease-in duration-100"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="toast.show"
                class="fixed top-5 right-5 z-[100] max-w-sm w-full"
            >
                <div
                    class="rounded-lg shadow-xl px-4 py-3 border flex items-start gap-3"
                    :class="
                        toast.type === 'success'
                            ? 'bg-white border-green-500 text-green-800'
                            : 'bg-white border-red-500 text-red-800'
                    "
                >
                    <div class="mt-0.5 shrink-0">
                        <font-awesome-icon
                            :icon="toast.type === 'success' ? faCheck : faTimes"
                            :class="
                                toast.type === 'success'
                                    ? 'text-green-500'
                                    : 'text-red-500'
                            "
                        />
                    </div>

                    <div class="text-sm font-semibold">
                        {{ toast.message }}
                    </div>

                    <button
                        type="button"
                        class="ml-auto text-gray-400 hover:text-gray-600"
                        @click="toast.show = false"
                    >
                        ✕
                    </button>
                </div>
            </div>
        </Transition>
    </Teleport>

    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div
                    class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4"
                >
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
                    <div class="flex gap-2">
                        <Button @click="logout" variant="secondary"
                            >Logout</Button
                        >
                        <Button @click="showCreateForm = !showCreateForm">
                            <font-awesome-icon :icon="faPlus" class="mr-2" />
                            Nuevo Tenant
                        </Button>
                        <Button
                            @click="showCreateUserForm = !showCreateUserForm"
                        >
                            <font-awesome-icon :icon="faPlus" class="mr-2" />
                            Nuevo Usuario
                        </Button>
                    </div>
                </div>
            </div>

            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
            >
                <div
                    v-if="showCreateForm"
                    class="bg-white shadow-md rounded-lg p-6 mb-8 border-t-4 border-blue-500"
                >
                    <h2 class="text-xl font-bold mb-4">
                        Configurar Nuevo Tenant
                    </h2>
                    <form @submit.prevent="submitForm" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <TextInput
                                id="name"
                                label="Nombre del Cliente"
                                v-model="form.name"
                                :error="form.errors.name"
                                required
                            />
                            <TextInput
                                id="path"
                                label="Ruta (URL)"
                                v-model="form.path"
                                :error="form.errors.path"
                                placeholder="ej: mi-cliente"
                            />
                            <TextInput
                                id="database"
                                label="Base de Datos"
                                v-model="form.database"
                                :error="form.errors.database"
                            />
                        </div>
                        <div class="flex justify-end space-x-3 mt-4">
                            <Button
                                type="button"
                                variant="secondary"
                                @click="showCreateForm = false"
                                >Cancelar</Button
                            >
                            <Button
                                type="submit"
                                :disabled="form.processing"
                                :processing="form.processing"
                                >Crear Tenant</Button
                            >
                        </div>
                    </form>
                </div>
            </Transition>

            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="transform scale-95 opacity-0"
                enter-to-class="transform scale-100 opacity-100"
            >
                <div
                    v-if="showCreateUserForm"
                    class="bg-white shadow-md rounded-lg p-6 mb-8 border-t-4 border-indigo-500"
                >
                    <h2 class="text-xl font-bold mb-4">
                        Nuevo Usuario de Sistema
                    </h2>
                    <form @submit.prevent="createUser" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <TextInput
                                id="user_name"
                                label="Nombre completo"
                                v-model="userForm.name"
                                :error="userForm.errors.name"
                                required
                            />
                            <TextInput
                                id="user_email"
                                label="Email"
                                type="email"
                                v-model="userForm.email"
                                :error="userForm.errors.email"
                                required
                            />
                            <TextInput
                                id="user_password"
                                label="Contraseña"
                                type="password"
                                v-model="userForm.password"
                                :error="userForm.errors.password"
                                required
                            />
                            <div class="flex items-center mt-8">
                                <Checkbox
                                    v-model="userForm.is_admin"
                                    label="¿Es Administrador Global?"
                                    id="user_is_admin"
                                />
                            </div>
                        </div>

                        <div
                            v-if="!userForm.is_admin"
                            class="mt-4 border rounded-lg overflow-hidden"
                        >
                            <div class="bg-gray-50 px-4 py-2 border-b">
                                <span class="text-sm font-semibold"
                                    >Asignar a un Tenant:</span
                                >
                            </div>
                            <div class="max-height-60 overflow-y-auto divide-y">
                                <div
                                    v-for="tenant in tenants"
                                    :key="tenant.id"
                                    class="p-3 flex items-center justify-between hover:bg-gray-50"
                                >
                                    <div class="flex items-center">
                                        <input
                                            type="radio"
                                            :value="tenant.id"
                                            v-model="userForm.tenant_id"
                                            class="mr-3"
                                        />
                                        <span class="text-sm">{{
                                            tenant.name
                                        }}</span>
                                    </div>
                                    <button
                                        type="button"
                                        @click="toggleTenantExpand(tenant.id)"
                                        class="text-xs text-indigo-600"
                                    >
                                        {{
                                            expandedTenants[tenant.id]
                                                ? "Cerrar"
                                                : "Ver usuarios"
                                        }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3 mt-6">
                            <Button
                                type="button"
                                variant="secondary"
                                @click="showCreateUserForm = false"
                                >Cancelar</Button
                            >
                            <Button
                                type="submit"
                                :disabled="userForm.processing"
                                >Registrar Usuario</Button
                            >
                        </div>
                    </form>
                </div>
            </Transition>

            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-white">
                    <h2 class="text-xl font-semibold text-gray-900">
                        Tenants Registrados ({{ tenants.length }})
                    </h2>
                </div>

                <div
                    v-if="tenants.length === 0"
                    class="p-12 text-center text-gray-500"
                >
                    <font-awesome-icon
                        :icon="faDatabase"
                        class="text-5xl mb-4 text-gray-200"
                    />
                    <p>No se encontraron registros de tenants.</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                >
                                    Cliente
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                >
                                    Ruta
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                >
                                    Base de Datos
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                >
                                    Estado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase"
                                >
                                    Acciones
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="tenant in tenants"
                                :key="tenant.id"
                                class="hover:bg-gray-50 transition-colors"
                            >
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900">
                                        {{ tenant.name }}
                                    </div>
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-600"
                                >
                                    {{ tenant.path }}
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
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm"
                                >
                                    <Link
                                        :href="
                                            route(
                                                'admin.tenants.show',
                                                tenant.id,
                                            )
                                        "
                                        class="text-blue-600 hover:text-blue-900"
                                    >
                                        Ver detalles
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
