<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import Modal from "@/Components/Modal.vue"; // Importamos el modal genérico
import {
    faDatabase,
    faGlobe,
    faBuilding,
} from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    show: Boolean,
});

const emit = defineEmits(["close", "tenant-created"]);

// Configuración del formulario Inertia
const form = useForm({
    name: "",
    domain: "",
    database_name: "",
});

const errorMessage = ref("");

const submit = () => {
    errorMessage.value = "";

    if (
        !form.name.trim() ||
        !form.domain.trim() ||
        !form.database_name.trim()
    ) {
        errorMessage.value = "Por favor, completa todos los campos.";
        return;
    }

    // AQUÍ LUEGO IRÁ LA RUTA: route('admin.tenant.store')
    console.log("Enviando formulario...", form.data());

    // Simulación de éxito
    setTimeout(() => {
        emit("tenant-created", form.data());
        form.reset();
        emit("close"); // Avisamos al padre que cierre el modal
    }, 1000);
};

const closeModal = () => {
    form.clearErrors();
    form.reset();
    errorMessage.value = "";
    emit("close");
};
</script>

<template>
    <Modal :show="show" title="Registrar Nuevo Cliente" @close="closeModal">
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
                    >Dominio</label
                >
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500"
                        ><font-awesome-icon :icon="faGlobe"
                    /></span>
                    <input
                        v-model="form.domain"
                        type="text"
                        placeholder="pepsi.termometria.test"
                        class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        required
                    />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-1"
                    >Base de Datos</label
                >
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-500"
                        ><font-awesome-icon :icon="faDatabase"
                    /></span>
                    <input
                        v-model="form.database_name"
                        type="text"
                        placeholder="tenant_pepsi_db"
                        class="w-full bg-gray-900 border border-gray-600 rounded-lg pl-10 pr-4 py-2 text-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        required
                    />
                </div>
            </div>
        </form>

        <div v-if="errorMessage" class="text-red-500 mt-4 text-center">
            {{ errorMessage }}
        </div>

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
                {{ form.processing ? "Guardando..." : "Guardar Cliente" }}
            </button>
        </template>
    </Modal>
</template>
