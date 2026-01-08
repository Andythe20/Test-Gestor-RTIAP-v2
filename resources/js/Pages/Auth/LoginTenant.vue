<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    // TODO
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-orange-50 via-green-50 to-emerald-50 flex"
    >
        <!-- Left Side - Branding -->
        <div
            class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-600 to-emerald-700 p-12 flex-col justify-center"
        >
            <div class="max-w-md">
                <div
                    class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-2xl mb-8"
                >
                    <svg
                        class="w-10 h-10 text-white"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-white mb-6">
                    Gestión Empresarial
                </h1>
                <p class="text-green-100 text-lg leading-relaxed mb-8">
                    Acceda a su plataforma de gestión integral para optimizar
                    procesos, monitorear indicadores y tomar decisiones
                    estratégicas.
                </p>
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div
                            class="w-3 h-3 bg-green-300 rounded-full mr-2"
                        ></div>
                        <span class="text-white text-sm">Monitoreo 24/7</span>
                    </div>
                    <div class="flex items-center">
                        <div
                            class="w-3 h-3 bg-orange-300 rounded-full mr-2"
                        ></div>
                        <span class="text-white text-sm"
                            >Reportes en Tiempo Real</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
            <div class="max-w-md w-full">
                <!-- Mobile Header -->
                <div class="lg:hidden text-center mb-8">
                    <div
                        class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-green-600 to-emerald-700 rounded-2xl mb-4"
                    >
                        <svg
                            class="w-8 h-8 text-white"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z"
                                clip-rule="evenodd"
                            ></path>
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        Gestión Empresarial
                    </h1>
                </div>

                <!-- Login Card -->
                <div
                    class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden"
                >
                    <div
                        class="bg-gradient-to-r from-green-600 to-emerald-600 px-8 py-6"
                    >
                        <h2 class="text-2xl font-bold text-white text-center">
                            Acceso Empresarial
                        </h2>
                        <p class="text-green-100 text-center text-sm mt-2">
                            Ingrese sus credenciales de empresa
                        </p>
                    </div>

                    <div class="p-8">
                        <form @submit.prevent="submit" class="space-y-6">
                            <TextInput
                                id="email"
                                label="Correo Corporativo"
                                v-model="form.email"
                                :error="form.errors.email"
                                placeholder="usuario@empresa.com"
                                required
                                customClass="!border-2 !border-gray-200 !rounded-xl !py-4 !pl-12"
                            />

                            <TextInput
                                id="password"
                                label="Contraseña"
                                type="password"
                                v-model="form.password"
                                :error="form.errors.password"
                                placeholder="••••••••••••"
                                required
                                customClass="!border-2 !border-gray-200 !rounded-xl !py-4 !pl-12"
                            />

                            <Button
                                type="submit"
                                :processing="form.processing"
                                class="w-full !bg-gradient-to-r !from-green-600 !to-emerald-600 hover:!from-green-700 hover:!to-emerald-700 !text-white !font-bold !rounded-xl !py-4 !px-6 !shadow-lg hover:!scale-105 transition-all"
                            >
                                <template v-if="form.processing">
                                    <svg
                                        class="animate-spin -ml-1 mr-3 h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                    >
                                        <circle
                                            class="opacity-25"
                                            cx="12"
                                            cy="12"
                                            r="10"
                                            stroke="currentColor"
                                            stroke-width="4"
                                        ></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    Accediendo...
                                </template>
                                <template v-else> Acceder al Sistema </template>
                            </Button>
                        </form>

                        <!-- Features -->
                        <div class="mt-8 grid grid-cols-2 gap-4">
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2"
                                >
                                    <svg
                                        class="w-6 h-6 text-green-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600 font-medium">
                                    Dashboard
                                </p>
                            </div>
                            <div class="text-center">
                                <div
                                    class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2"
                                >
                                    <svg
                                        class="w-6 h-6 text-orange-600"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"
                                        ></path>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-600 font-medium">
                                    Analytics
                                </p>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-xs text-gray-500 text-center">
                                Plataforma Empresarial RTIAP • Versión 2.0
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
