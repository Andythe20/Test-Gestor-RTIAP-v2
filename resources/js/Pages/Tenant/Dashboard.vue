<script setup>
import Button from "@/Components/Button.vue";
import { defineProps } from "vue";

const props = defineProps({
    tenant: { type: String },
    empresa: { type: Object, default: null },
    empleados: { type: Array, default: () => [] },
    productos: { type: Array, default: () => [] },
    tarjetas: { type: Array, default: () => [] },
    ventas: { type: Array, default: () => [] },
    estadisticas: { type: Object, default: () => ({}) },
});
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Panel del Tenant
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Bienvenido,
                            <span class="font-semibold">{{ tenant }}</span>
                        </p>
                    </div>
                    <form method="POST" :action="route('logout')">
                        <input
                            type="hidden"
                            name="_token"
                            :value="$page.props.csrf_token"
                        />
                        <Button type="submit"> Logout </Button>
                    </form>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600">
                        {{ estadisticas.empleados ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Empleados</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-2xl font-bold text-green-600">
                        {{ estadisticas.productos ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Productos</div>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-6 text-center">
                    <div class="text-2xl font-bold text-orange-600">
                        {{ estadisticas.ventas ?? 0 }}
                    </div>
                    <div class="text-sm text-gray-500">Ventas</div>
                </div>
            </div>

            <!-- Empresa -->
            <div v-if="empresa" class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Información de la Empresa
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            Nombre
                        </div>
                        <div class="text-sm text-gray-900">
                            {{ empresa.nombre }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            Dirección
                        </div>
                        <div class="text-sm text-gray-900">
                            {{ empresa.direccion }}
                        </div>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">
                            Correo
                        </div>
                        <div class="text-sm text-gray-900">
                            {{ empresa.correo_electronico }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empleados -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Empleados
                </h2>
                <div v-if="empleados.length === 0" class="text-gray-500">
                    No hay empleados registrados
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
                                    Apellido
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Correo
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="emp in empleados"
                                :key="emp.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ emp.nombre }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ emp.apellido }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ emp.correo_electronico }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-2 text-xs text-gray-500">
                        Mostrando últimos {{ empleados.length }} registros.
                    </p>
                </div>
            </div>

            <!-- Productos -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Productos
                </h2>
                <div v-if="productos.length === 0" class="text-gray-500">
                    No hay productos registrados
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
                                    Descripción
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Precio
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="p in productos"
                                :key="p.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ p.nombre }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ p.descripcion }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{
                                        new Intl.NumberFormat("es-CL").format(
                                            p.precio
                                        )
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-2 text-xs text-gray-500">
                        Mostrando últimos {{ productos.length }} registros.
                    </p>
                </div>
            </div>

            <!-- Tarjetas -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    Tarjetas
                </h2>
                <div v-if="tarjetas.length === 0" class="text-gray-500">
                    No hay tarjetas registradas
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Número
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Titular
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Expira
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="t in tarjetas"
                                :key="t.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ t.numero_tarjeta }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ t.nombre_titular }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ t.fecha_expiracion }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-2 text-xs text-gray-500">
                        Mostrando últimos {{ tarjetas.length }} registros.
                    </p>
                </div>
            </div>

            <!-- Ventas -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Ventas</h2>
                <div v-if="ventas.length === 0" class="text-gray-500">
                    No hay ventas registradas
                </div>
                <div v-else class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Empleado
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Producto
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Tarjeta
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Total
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                >
                                    Fecha
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr
                                v-for="v in ventas"
                                :key="v.id"
                                class="hover:bg-gray-50"
                            >
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ v.empleado?.nombre }}
                                    {{ v.empleado?.apellido }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ v.producto?.nombre }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ v.tarjeta?.numero_tarjeta }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{
                                        new Intl.NumberFormat("es-CL").format(
                                            v.total
                                        )
                                    }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{
                                        new Date(v.created_at).toLocaleString()
                                    }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p class="mt-2 text-xs text-gray-500">
                        Mostrando últimos {{ ventas.length }} registros.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
