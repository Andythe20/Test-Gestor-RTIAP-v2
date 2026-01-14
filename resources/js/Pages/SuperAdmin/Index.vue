<script setup>
import { ref, nextTick, watch, onBeforeUnmount } from "vue";
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

// Estados de visibilidad de formularios
const showCreateForm = ref(false);
const showCreateUserForm = ref(false);

// Estados de token
const apiToken = ref(null);
const showTokenModal = ref(false);
const copySuccess = ref(false);

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
    { deep: true }
);

// Lógica de creación
const createUser = () => {
    if (userForm.is_admin) {
        userForm.tenant_id = null;
    }
    userForm.post(route("admin.users.store"), {
        preserveScroll: true,
        onSuccess: () => {
            userForm.reset();
        },
    });
};

// Envío del formulario de creación de tenant
const submitForm = () => {
    form.post(route("admin.tenants.store"), {
        preserveScroll: true,
        onSuccess: async (inertiaPage) => {
            showCreateForm.value = false;
            form.reset();

            // Helpful logs to inspect what Inertia returns (debugging timing)
            try {
                console.log("onSuccess inertiaPage (arg)", inertiaPage);
                console.log("onSuccess usePage()", page);
            } catch (e) {
                console.log("page log failed", e);
            }

            // Wait a tick to ensure reactive props update
            await nextTick();

            const token =
                // propiedad explícita proporcionada por el controlador (más confiable)
                (page && page.props && page.props.api_token) ||
                (inertiaPage &&
                    inertiaPage.props &&
                    inertiaPage.props.flash &&
                    inertiaPage.props.flash.api_token) ||
                (page &&
                    page.props &&
                    page.props.flash &&
                    page.props.flash.api_token) ||
                null;

            if (token) {
                apiToken.value = token;
                showTokenModal.value = true;
            } else {
                console.warn("API token no disponible en la respuesta.");
            }
        },
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

// Lógica del modal de token
const closeTokenModal = () => {
    showTokenModal.value = false;
    apiToken.value = null;
    form.reset();
};

const copyToken = async () => {
    try {
        await navigator.clipboard.writeText(apiToken.value || "");
        copySuccess.value = true;
        setTimeout(() => (copySuccess.value = false), 3000);
    } catch (e) {
        console.error("Copy failed", e);
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

            <!-- Token Modal -->
            <div
                v-if="showTokenModal"
                class="fixed inset-0 z-50 flex items-center justify-center"
            >
                <div
                    class="fixed inset-0 bg-black opacity-40"
                    @click="closeTokenModal"
                ></div>
                <div
                    class="bg-white rounded-lg shadow-lg p-6 z-10 w-full max-w-lg"
                >
                    <h3 class="text-lg font-semibold mb-3">
                        API Token (guárdalo ahora)
                    </h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Este token se mostrará sólo una vez. Copia y guárdalo en
                        un lugar seguro.
                    </p>
                    <div
                        class="bg-gray-50 border border-gray-200 rounded p-3 break-all mb-4"
                    >
                        <code class="text-sm">{{ apiToken }}</code>
                        <p
                            v-if="copySuccess"
                            class="text-sm text-green-600 mt-2"
                        >
                            Copiado al portapapeles
                        </p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <Button
                            type="button"
                            variant="secondary"
                            @click="closeTokenModal"
                            >Cerrar</Button
                        >
                        <Button type="button" @click="copyToken">{{
                            copySuccess ? "Copiado" : "Copiar token"
                        }}</Button>
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
