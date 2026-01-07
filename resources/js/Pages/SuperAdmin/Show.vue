<script setup>
import { Link } from "@inertiajs/vue3";
import {
    faArrowLeft,
    faBuilding,
    faDatabase,
    faBriefcase,
    faIndustry,
    faTemperatureHalf,
} from "@fortawesome/free-solid-svg-icons";

defineProps({
    tenant: Object,
    empresas: Array,
});
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div
            class="bg-gray-900 text-white px-6 py-4 shadow-md flex justify-between items-center sticky top-0 z-50"
        >
            <div class="flex items-center gap-4">
                <Link
                    :href="route('admin.index')"
                    class="text-gray-400 hover:text-white transition"
                >
                    <font-awesome-icon :icon="faArrowLeft" /> Volver
                </Link>
                <div class="h-6 w-px bg-gray-700"></div>
                <div>
                    <h1 class="font-bold text-lg flex items-center gap-2">
                        <font-awesome-icon
                            :icon="faDatabase"
                            class="text-indigo-400"
                        />
                        Base de Datos: {{ tenant.database_name }}
                    </h1>
                </div>
            </div>
            <div
                class="text-xs bg-red-900 text-red-100 px-2 py-1 rounded border border-red-700"
            >
                MODO SUPER-ADMIN
            </div>
        </div>

        <div class="p-8 max-w-7xl mx-auto">
            <h2 class="text-3xl font-bold text-gray-800 mb-6">
                {{ tenant.name }}
            </h2>

            <div
                v-if="empresas.length === 0"
                class="text-center py-10 bg-white rounded shadow text-gray-500"
            >
                No hay empresas registradas en esta base de datos.
            </div>

            <div
                v-for="empresa in empresas"
                :key="empresa.id"
                class="mb-10 border-l-4 border-indigo-600 pl-4"
            >
                <h3
                    class="text-2xl font-bold text-indigo-700 mb-2 flex items-center gap-2"
                >
                    <font-awesome-icon :icon="faBriefcase" />
                    {{ empresa.nombre }}
                </h3>
                <p class="text-gray-500 mb-4">{{ empresa.direccion }}</p>

                <div
                    v-for="sucursal in empresa.sucursales"
                    :key="sucursal.id"
                    class="mb-6 bg-white shadow rounded-lg overflow-hidden border border-gray-200 ml-4"
                >
                    <div
                        class="bg-gray-50 px-6 py-3 border-b flex justify-between items-center"
                    >
                        <h4
                            class="text-lg font-bold text-gray-800 flex items-center gap-2"
                        >
                            <font-awesome-icon
                                :icon="faBuilding"
                                class="text-gray-400"
                            />
                            {{ sucursal.nombre }}
                        </h4>
                        <span class="text-xs text-gray-400">{{
                            sucursal.direccion
                        }}</span>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div
                                v-for="sistema in sucursal.sistemas"
                                :key="sistema.id"
                                class="border rounded-md p-4 bg-gray-50"
                            >
                                <h5
                                    class="font-bold text-gray-700 mb-3 pb-2 border-b flex items-center gap-2"
                                >
                                    <font-awesome-icon
                                        :icon="faIndustry"
                                        class="text-gray-400"
                                    />
                                    {{ sistema.nombre }}
                                </h5>

                                <ul class="space-y-2 text-sm">
                                    <li
                                        v-for="sensor in sistema.sensores"
                                        :key="sensor.id"
                                        class="flex justify-between"
                                    >
                                        <span class="text-gray-500 text-xs">{{
                                            sensor.codigo_serie
                                        }}</span>
                                        <span
                                            :class="
                                                sensor.valor_actual > 0
                                                    ? 'text-red-600 font-bold'
                                                    : 'text-blue-600 font-bold'
                                            "
                                        >
                                            <font-awesome-icon
                                                :icon="faTemperatureHalf"
                                                class="mr-1"
                                            />
                                            {{ sensor.valor_actual }}°C
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div
                            v-if="sucursal.sistemas.length === 0"
                            class="text-sm text-gray-400 italic"
                        >
                            Sin sistemas registrados.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
