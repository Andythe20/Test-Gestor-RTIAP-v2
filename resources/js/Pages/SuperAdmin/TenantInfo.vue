<script setup>
import { Link } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import {
    faArrowLeft,
    faBuilding,
    faMapMarkerAlt,
    faUsers,
    faBox,
    faShoppingCart,
    faDatabase,
    faServer,
    faGlobe,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    tenant: Object,
    empresa: Object,
    sucursales: Array,
    estadisticas: Object,
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <Link
                            :href="route('admin.home')"
                            class="mr-4 text-gray-400 hover:text-gray-600"
                        >
                            <font-awesome-icon :icon="faArrowLeft" />
                        </Link>
                        <div>
                            <h1
                                class="text-3xl font-bold text-gray-900 flex items-center"
                            >
                                <font-awesome-icon
                                    :icon="faServer"
                                    class="mr-3 text-blue-600"
                                />
                                Información del Tenant: {{ tenant.name }}
                            </h1>
                            <p class="text-gray-600 mt-2">
                                Detalles y estadísticas del cliente
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500">Estado:</span>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-green-100 text-green-800':
                                    tenant.status === 'active',
                                'bg-red-100 text-red-800':
                                    tenant.status === 'failed',
                                'bg-yellow-100 text-yellow-800':
                                    tenant.status === 'provisioning',
                            }"
                        >
                            {{ tenant.status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Información del Tenant -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <!-- Información General -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2
                        class="text-xl font-semibold text-gray-900 mb-4 flex items-center"
                    >
                        <font-awesome-icon
                            :icon="faDatabase"
                            class="mr-2 text-blue-600"
                        />
                        Información General
                    </h2>
                    <div class="space-y-3">
                        <div>
                            <label class="text-sm font-medium text-gray-500"
                                >Nombre del Cliente</label
                            >
                            <p class="text-sm text-gray-900">
                                {{ tenant.name }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500"
                                >Ruta</label
                            >
                            <p class="text-sm text-gray-900">
                                {{ tenant.ruta }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500"
                                >Base de Datos</label
                            >
                            <p class="text-sm text-gray-900">
                                {{ tenant.database || "Pendiente" }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500"
                                >Creado</label
                            >
                            <p class="text-sm text-gray-900">
                                {{
                                    new Date(
                                        tenant.created_at,
                                    ).toLocaleDateString()
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Estadísticas -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h2
                        class="text-xl font-semibold text-gray-900 mb-4 flex items-center"
                    >
                        <font-awesome-icon
                            :icon="faServer"
                            class="mr-2 text-green-600"
                        />
                        Estadísticas
                    </h2>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">
                                {{ estadisticas.empleados }}
                            </div>
                            <div
                                class="text-sm text-gray-500 flex items-center justify-center"
                            >
                                <font-awesome-icon
                                    :icon="faUsers"
                                    class="mr-1"
                                />
                                Empleados
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">
                                {{ estadisticas.productos }}
                            </div>
                            <div
                                class="text-sm text-gray-500 flex items-center justify-center"
                            >
                                <font-awesome-icon :icon="faBox" class="mr-1" />
                                Productos
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-purple-600">
                                {{ estadisticas.ventas }}
                            </div>
                            <div
                                class="text-sm text-gray-500 flex items-center justify-center"
                            >
                                <font-awesome-icon
                                    :icon="faShoppingCart"
                                    class="mr-1"
                                />
                                Ventas
                            </div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-orange-600">
                                {{ estadisticas.sucursales }}
                            </div>
                            <div
                                class="text-sm text-gray-500 flex items-center justify-center"
                            >
                                <font-awesome-icon
                                    :icon="faMapMarkerAlt"
                                    class="mr-1"
                                />
                                Sucursales
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información de la Empresa -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8" v-if="empresa">
                <h2
                    class="text-xl font-semibold text-gray-900 mb-4 flex items-center"
                >
                    <font-awesome-icon
                        :icon="faBuilding"
                        class="mr-2 text-indigo-600"
                    />
                    Información de la Empresa
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div v-for="(value, key) in empresa" :key="key">
                        <label
                            class="text-sm font-medium text-gray-500 capitalize"
                            >{{ key.replace("_", " ") }}</label
                        >
                        <p class="text-sm text-gray-900">{{ value }}</p>
                    </div>
                </div>
            </div>

            <!-- Sucursales -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2
                        class="text-xl font-semibold text-gray-900 flex items-center"
                    >
                        <font-awesome-icon
                            :icon="faMapMarkerAlt"
                            class="mr-2 text-red-600"
                        />
                        Sucursales ({{ sucursales.length }})
                    </h2>
                </div>

                <div
                    v-if="sucursales.length === 0"
                    class="p-8 text-center text-gray-500"
                >
                    <font-awesome-icon
                        :icon="faMapMarkerAlt"
                        class="text-4xl mb-4 text-gray-300"
                    />
                    <p>No hay sucursales registradas</p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Nombre
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Dirección
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Teléfono
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Estado
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="sucursal in sucursales"
                                :key="sucursal.id"
                                class="hover:bg-gray-50"
                            >
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                >
                                    {{ sucursal.nombre }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ sucursal.direccion }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                >
                                    {{ sucursal.telefono }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        Activa
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
