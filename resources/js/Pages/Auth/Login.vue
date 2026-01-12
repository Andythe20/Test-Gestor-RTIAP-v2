<script setup>
import { useForm, usePage } from "@inertiajs/vue3";
import Button from "@/Components/Button.vue";
import TextInput from "@/Components/TextInput.vue";
import PasswordInput from "@/Components/PasswordInput.vue";

// useForm() para manejar el formulario de inicio de sesión
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
                        <TextInput
                            id="email"
                            label="Correo Electrónico"
                            v-model="form.email"
                            :error="form.errors.email"
                            type="email"
                            placeholder="admin@empresa.com"
                            help="Usa tu correo de administrador"
                            required
                            size="lg"
                        />

                        <PasswordInput
                            id="password"
                            label="Contraseña"
                            v-model="form.password"
                            :error="form.errors.password"
                            placeholder="••••••••"
                            required
                            @update:modelValue="form.clearErrors('password')"
                            size="lg"
                        />

                        <Button
                            type="submit"
                            :processing="form.processing"
                            size="lg"
                            fullWidth
                            loadingLabel="Iniciando sesión..."
                        >
                            Iniciar Sesión
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
