<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";
import {
    faServer,
    faArrowRight,
    faPlus,
} from "@fortawesome/free-solid-svg-icons";

// IMPORTAMOS EL COMPONENTE FORMULARIO
import CreateTenantForm from "./Partials/CreateTenantForm.vue";
import CreateCompanyForm from "./Partials/CreateCompanyForm.vue";

defineProps({ tenants: Array });

// Estado para controlar la visibilidad del modal
const showCreateModal = ref(false);
const showCreateCompanyModal = ref(false);
const tenantData = ref(null);

const onTenantCreated = (data) => {
    tenantData.value = data;
    showCreateCompanyModal.value = true;
};
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-white p-8 relative">
        <div class="max-w-4xl mx-auto">
            <div class="flex justify-between items-end mb-8">
                <div>
                    <h1 class="text-4xl font-bold mb-2">
                        📡 Centro de Comando
                    </h1>
                    <p class="text-gray-400">
                        Administración global de clientes.
                    </p>
                </div>

                <button
                    @click="showCreateModal = true"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-2 px-6 rounded-lg shadow-lg flex items-center gap-2 transition transform hover:scale-105"
                >
                    <font-awesome-icon :icon="faPlus" />
                    Nuevo Cliente
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="tenant in tenants"
                    :key="tenant.id"
                    class="bg-gray-800 border border-gray-700 p-6 rounded-xl hover:bg-gray-750 transition shadow-lg"
                >
                    <h2 class="text-2xl font-bold mb-1">{{ tenant.name }}</h2>
                    <p class="text-indigo-400 text-sm mb-4">
                        {{ tenant.domain }}
                    </p>

                    <Link
                        :href="route('admin.tenant.show', tenant.id)"
                        class="block w-full text-center bg-gray-700 hover:bg-white hover:text-gray-900 text-white font-bold py-2 px-4 rounded transition group"
                    >
                        Inspeccionar
                    </Link>
                </div>
            </div>
        </div>

        <CreateTenantForm
            :show="showCreateModal"
            @close="showCreateModal = false"
            @tenant-created="onTenantCreated"
        />

        <CreateCompanyForm
            :show="showCreateCompanyModal"
            :tenant="tenantData"
            @close="showCreateCompanyModal = false"
        />
    </div>
</template>
