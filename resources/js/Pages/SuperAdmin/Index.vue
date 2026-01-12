<script setup>
import { ref } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import PasswordInput from "@/Components/PasswordInput.vue";
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

const showCreateForm = ref(false);
const showCreateUserForm = ref(false);

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

const createUser = () => {
    if (userForm.is_admin) {
        userForm.tenant_id = null;
    }
    userForm.post(route("admin.users.store"));
};

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

const submitForm = () => {
    form.post(route("admin.tenants.store"), {
        preserveScroll: true,
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
                    <Button @click="logout" variant="secondary">
                        Logout
                    </Button>
                    <Button
                        @click="showCreateForm = !showCreateForm"
                        class="ml-4"
                        :icon="faPlus"
                    >
                        Nuevo Tenant
                    </Button>
                    <Button
                        @click="showCreateUserForm = !showCreateUserForm"
                        class="ml-4"
                        :icon="faPlus"
                    >
                        Nuevo Usuario
                    </Button>
                </div>
            </div>

            <!-- Flash Messages -->
            <div
                v-if="page?.props?.flash?.success"
                class="bg-green-50 border border-green-200 rounded-lg p-4 mb-8"
            >
                <div class="flex">
                    <div class="shrink-0">
                        <font-awesome-icon
                            :icon="faCheck"
                            class="h-5 w-5 text-green-400"
                        />
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            {{ page.props.flash.success }}
                        </p>
                        <div
                            v-if="
                                page.props.flash.success.includes(
                                    'credenciales'
                                )
                            "
                            class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded"
                        >
                            <p class="text-sm text-blue-800 font-medium">
                                Credenciales de acceso generadas:
                            </p>
                            <p class="text-sm text-blue-700 mt-1">
                                <strong>Email:</strong> admin@{{
                                    form.path ||
                                    (form.name
                                        ? form.name
                                              .toLowerCase()
                                              .replace(/\s+/g, "") + ".app.test"
                                        : "path")
                                }}<br />
                                <strong>Password:</strong> password123
                            </p>
                            <p class="text-xs text-blue-600 mt-2">
                                El tenant puede usar estas credenciales para
                                acceder a su dashboard desde la página de login.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div
                v-if="page?.props?.flash?.error"
                class="bg-red-50 border border-red-200 rounded-lg p-4 mb-8"
            >
                <div class="flex">
                    <div class="shrink-0">
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
            <!-- Header clickable -->
            <button
                type="button"
                class="w-full text-left bg-white shadow-sm rounded-lg p-6 mb-2 flex items-center justify-between"
                @click="showCreateForm = !showCreateForm"
            >
                <h2 class="text-xl font-semibold text-gray-900">
                    Crear Nuevo Tenant
                </h2>
                <span class="text-gray-500">{{
                    showCreateForm ? "▲" : "▼"
                }}</span>
            </button>

            <!-- Form body (fuera del button) -->
            <div
                v-if="showCreateForm"
                class="bg-white shadow-sm rounded-lg p-6 mb-8"
            >
                <form @submit.prevent="submitForm" class="space-y-4">
                    <input
                        type="hidden"
                        name="_token"
                        :value="$page.props.csrf_token"
                    />

                    <TextInput
                        id="name"
                        label="Nombre del Cliente"
                        placeholder="Ej: Walmart"
                        v-model="form.name"
                        :error="form.errors.name"
                        required
                        size="md"
                    />

                    <TextInput
                        id="path"
                        placeholder="Ej: walmart"
                        label="Ruta (opcional)"
                        v-model="form.path"
                        :error="form.errors.path"
                        size="md"
                    />

                    <TextInput
                        id="database"
                        placeholder="Ej: walmart_DB"
                        label="Database (opcional)"
                        v-model="form.database"
                        :error="form.errors.database"
                        size="md"
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
                            :processing="form.processing"
                            size="lg"
                            loadingLabel="Creando Tenant..."
                            >Crear Tenant</Button
                        >
                    </div>
                </form>
            </div>

            <!-- Create User -->
            <!-- Header clickable -->
            <button
                type="button"
                class="w-full text-left bg-white shadow-sm rounded-lg p-6 mb-2 flex items-center justify-between"
                @click="showCreateUserForm = !showCreateUserForm"
            >
                <h2
                    class="text-xl font-semibold text-gray-900 flex items-center"
                >
                    <font-awesome-icon
                        :icon="faUsers"
                        class="mr-2 text-indigo-600"
                    />
                    Crear Nuevo Usuario
                </h2>
                <span class="text-gray-500">{{
                    showCreateUserForm ? "▲" : "▼"
                }}</span>
            </button>

            <!-- Form body (fuera del button) -->
            <div
                v-if="showCreateUserForm"
                class="bg-white shadow-sm rounded-lg p-6 mb-8"
            >
                <form class="space-y-4">
                    <input
                        type="hidden"
                        name="_token"
                        :value="$page.props.csrf_token"
                    />

                    <TextInput
                        id="user_name"
                        name="user_name"
                        label="Nombre del Usuario"
                        v-model="userForm.name"
                        :error="userForm.errors?.name"
                        required
                    />

                    <TextInput
                        id="user_email"
                        name="user_email"
                        label="Correo Electrónico"
                        type="email"
                        v-model="userForm.email"
                        :error="userForm.errors?.email"
                        required
                    />

                    <PasswordInput
                        id="user_password"
                        name="user_password"
                        label="Contraseña"
                        v-model="userForm.password"
                        :error="userForm.errors?.password"
                        required
                    />

                    <!-- Admin checkbox -->
                    <div class="flex items-center justify-between">
                        <label class="inline-flex items-center">
                            <Checkbox
                                v-model="userForm.is_admin"
                                label="Usuario administrador"
                                id="user_is_admin"
                            />
                        </label>
                        <span class="text-xs text-gray-500">
                            Por defecto marcado. Si lo desmarcas, podrás asignar
                            el usuario a un tenant y ver sus usuarios.
                        </span>
                    </div>

                    <!-- Tenants & Users list shown when not admin -->
                    <div
                        v-if="!userForm.is_admin"
                        class="mt-4 border rounded-lg"
                    >
                        <div class="px-4 py-3 border-b">
                            <h3 class="text-md font-semibold text-gray-900">
                                Asignar a Tenant y ver usuarios
                            </h3>
                            <p class="text-sm text-gray-600">
                                Selecciona el tenant al que pertenecerá el
                                usuario y revisa los usuarios existentes de cada
                                tenant.
                            </p>
                        </div>

                        <div class="divide-y">
                            <div
                                v-for="tenant in tenants"
                                :key="tenant.id"
                                class="px-4 py-3"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-medium text-gray-900 truncate"
                                        >
                                            {{ tenant.name }}
                                        </p>
                                        <p
                                            class="text-xs text-gray-500 truncate"
                                        >
                                            Ruta: {{ tenant.path || "-" }} • DB:
                                            {{ tenant.database || "Pendiente" }}
                                        </p>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <!-- Select tenant radio -->
                                        <label
                                            class="inline-flex items-center text-sm text-gray-700"
                                        >
                                            <input
                                                type="radio"
                                                name="tenant_id"
                                                class="text-indigo-600 focus:ring-indigo-500"
                                                :value="tenant.id"
                                                v-model="userForm.tenant_id"
                                            />
                                            <span class="ml-2"
                                                >Seleccionar</span
                                            >
                                        </label>
                                        <button
                                            type="button"
                                            class="text-sm text-indigo-600 hover:text-indigo-800"
                                            @click="
                                                toggleTenantExpand(tenant.id)
                                            "
                                        >
                                            {{
                                                expandedTenants[tenant.id]
                                                    ? "Ocultar usuarios"
                                                    : "Ver usuarios"
                                            }}
                                        </button>
                                    </div>
                                </div>

                                <!-- Nested users list -->
                                <div
                                    v-if="expandedTenants[tenant.id]"
                                    class="mt-3 bg-gray-50 rounded-md p-3"
                                >
                                    <div
                                        class="flex items-center mb-2 text-gray-700"
                                    >
                                        <font-awesome-icon
                                            :icon="faUsers"
                                            class="mr-2"
                                        />
                                        <span class="text-sm font-medium"
                                            >Usuarios del tenant</span
                                        >
                                    </div>
                                    <ul class="space-y-2">
                                        <li
                                            v-if="loadingUsers[tenant.id]"
                                            class="text-xs text-gray-500"
                                        >
                                            Cargando Usuarios
                                        </li>
                                        <li
                                            v-else-if="
                                                !tenantsUsers[tenant.id] ||
                                                tenantsUsers[tenant.id]
                                                    .length === 0
                                            "
                                            class="text-xs text-gray-500"
                                        >
                                            No hay usuarios disponibles para
                                            este tenant
                                        </li>
                                        <li
                                            v-else
                                            v-for="u in tenantsUsers[tenant.id]"
                                            :key="u.id"
                                            class="flex items-center justify-between text-sm bg-white px-3 py-2 rounded border"
                                        >
                                            <div class="min-w-0">
                                                <p
                                                    class="font-medium text-gray-900 truncate"
                                                >
                                                    {{ u.name }}
                                                </p>
                                                <p
                                                    class="text-xs text-gray-500 truncate"
                                                >
                                                    {{ u.email }}
                                                </p>
                                            </div>
                                            <span
                                                class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700"
                                                >{{
                                                    u.is_admin
                                                        ? "Admin"
                                                        : "Tenant"
                                                }}</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-3 mt-6">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="showCreateUserForm = false"
                        >
                            Cancelar
                        </Button>
                        <Button type="button" @click="createUser">
                            Crear Usuario
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
                                    Ruta
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
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{
                                        new Date(
                                            tenant.created_at
                                        ).toLocaleDateString()
                                    }}
                                </td>

                                <td
                                    class="px-6 py-4 whitespace-nowrap text-center"
                                >
                                    <Button
                                        :href="
                                            route(
                                                'admin.tenants.show',
                                                tenant.id
                                            )
                                        "
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
