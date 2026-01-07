<script setup>
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue"; // Importamos el modal genérico
import {
    faBuilding,
    faMapMarkerAlt,
    faEnvelope,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    show: Boolean,
    tenant: Object,
});

const emit = defineEmits(["close"]);

// Configuración del formulario Inertia
const form = useForm({
    name: "",
    address: "",
    email: "",
});

const submit = () => {
    // AQUÍ LUEGO IRÁ LA RUTA: route('admin.company.store')
    console.log("Enviando formulario de empresa...", form.data());

    // Simulación de éxito
    setTimeout(() => {
        form.reset();
        emit("close"); // Avisamos al padre que cierre el modal
        alert("Empresa simulada creada");
    }, 1000);
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    emit("close");
};
</script>

<template>
    <Modal :show="show" :title="`Registrar Empresa de: ${tenant?.name || ''}`" @close="closeModal">
        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1"
                    >Nombre Empresa</label
                >
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500"
                        ><font-awesome-icon :icon="faBuilding"
                    /></span>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="Ej: Pepsi Co."
                        class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1"
                    >Dirección</label
                >
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500"
                        ><font-awesome-icon :icon="faMapMarkerAlt"
                    /></span>
                    <input
                        v-model="form.address"
                        type="text"
                        placeholder="Ej: Calle Principal 123, Ciudad"
                        class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1"
                    >Email</label
                >
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500"
                        ><font-awesome-icon :icon="faEnvelope"
                    /></span>
                    <input
                        v-model="form.email"
                        type="email"
                        placeholder="contacto@empresa.com"
                        class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        required
                    />
                </div>
            </div>
        </form>

        <template #footer>
            <button
                @click="closeModal"
                type="button"
                class="px-4 py-2 text-gray-300 hover:text-white transition"
            >
                Cancelar
            </button>
            <button
                @click="submit"
                :disabled="form.processing"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold shadow-lg transition disabled:opacity-50"
            >
                {{ form.processing ? "Guardando..." : "Guardar Empresa" }}
            </button>
        </template>
    </Modal>
</template>
