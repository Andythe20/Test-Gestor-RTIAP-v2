<script setup>
import Button from "@/Components/Button.vue";
</script>

<template>
    <div class="min-h-screen bg-gray-50 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white shadow-sm rounded-lg p-6 mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            Tenant Dashboard
                        </h1>
                        <p class="text-gray-600 mt-2">
                            Bienvenido a tu panel de control
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

            <!-- Content -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <p>Contenido del dashboard del tenant aquí.</p>
            </div>
        </div>
    </div>
</template>
