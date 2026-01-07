<script setup>
import { Link } from "@inertiajs/vue3";
import { faServer, faArrowRight } from "@fortawesome/free-solid-svg-icons";

defineProps({ tenants: Array });
</script>

<template>
    <div class="min-h-screen bg-gray-900 text-white p-8">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-4xl font-bold mb-2">
                📡 Centro de Comando (Landlord)
            </h1>
            <p class="text-gray-400 mb-8">
                Administración global de clientes y bases de datos.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div
                    v-for="tenant in tenants"
                    :key="tenant.id"
                    class="bg-gray-800 border border-gray-700 p-6 rounded-xl hover:bg-gray-750 transition shadow-lg"
                >
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-indigo-600 p-3 rounded-lg">
                            <font-awesome-icon
                                :icon="faServer"
                                class="text-xl text-white"
                            />
                        </div>
                        <span
                            class="text-xs font-mono bg-gray-700 px-2 py-1 rounded text-gray-300"
                        >
                            ID: {{ tenant.id }}
                        </span>
                    </div>

                    <h2 class="text-2xl font-bold mb-1">{{ tenant.name }}</h2>
                    <p class="text-indigo-400 text-sm mb-4">
                        {{ tenant.domain }}
                    </p>
                    <p class="text-gray-500 text-xs mb-6 font-mono">
                        DB: {{ tenant.database_name }}
                    </p>

                    <Link
                        :href="route('admin.tenant.show', tenant.id)"
                        class="block w-full text-center bg-gray-700 hover:bg-indigo-600 text-white font-bold py-2 px-4 rounded transition"
                    >
                        Inspeccionar Sensores
                        <font-awesome-icon :icon="faArrowRight" class="ml-2" />
                    </Link>
                </div>
            </div>

            <div
                v-if="tenants.length === 0"
                class="text-center py-20 text-gray-500"
            >
                No hay clientes registrados en la base de datos central.
            </div>
        </div>
    </div>
</template>
