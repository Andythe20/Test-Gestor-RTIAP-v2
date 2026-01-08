<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post("/login", {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-blue-50 flex items-center justify-center px-4"
    >
        <div class="max-w-lg w-full">
            <!-- Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-blue-600 rounded-full mb-4"
                >
                    <svg
                        class="w-8 h-8 text-white"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                            clip-rule="evenodd"
                        ></path>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    Panel de Administración
                </h1>
                <p class="text-gray-600">
                    Acceso exclusivo para administradores del sistema
                </p>
            </div>

            <!-- Login Card -->
            <div
                class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden"
            >
                <div class="bg-blue-600 px-6 py-4">
                    <h2 class="text-xl font-semibold text-white text-center">
                        Iniciar Sesión
                    </h2>
                </div>

                <div class="p-8">
                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label
                                for="email"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Correo Electrónico
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                >
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                        ></path>
                                        <path
                                            d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    id="email"
                                    type="email"
                                    v-model="form.email"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="admin@empresa.com"
                                    required
                                />
                            </div>
                            <p
                                v-if="form.errors.email"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label
                                for="password"
                                class="block text-sm font-medium text-gray-700 mb-2"
                            >
                                Contraseña
                            </label>
                            <div class="relative">
                                <div
                                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                                >
                                    <svg
                                        class="h-5 w-5 text-gray-400"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                    >
                                        <path
                                            fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd"
                                        ></path>
                                    </svg>
                                </div>
                                <input
                                    id="password"
                                    type="password"
                                    v-model="form.password"
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    placeholder="••••••••"
                                    required
                                />
                            </div>
                            <p
                                v-if="form.errors.password"
                                class="mt-1 text-sm text-red-600"
                            >
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <Button
                            type="submit"
                            :processing="form.processing"
                            class="w-full !bg-blue-600 hover:!bg-blue-700 !text-white !font-medium !py-3 !rounded-lg"
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
                                Iniciando sesión...
                            </template>
                            <template v-else> Iniciar Sesión </template>
                        </Button>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <p class="text-xs text-gray-500 text-center">
                            Sistema de Gestión v1.0
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
