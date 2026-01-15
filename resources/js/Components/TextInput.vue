<script setup>
import { computed, useSlots } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    id: {
        type: String,
        required: true,
    },
    label: {
        type: String,
        default: "",
    },
    modelValue: {
        type: [String, Number],
        default: "",
    },
    type: {
        type: String,
        default: "text",
    },
    error: {
        type: String,
        default: "",
    },
    placeholder: {
        type: String,
        default: "",
    },
    required: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    help: {
        type: String,
        default: "",
    },
    // Prefix y Suffix pueden ser texto simple pasado por prop, o slots
    prefix: {
        type: [String, Object],
        default: null,
    },
    suffix: {
        type: [String, Object],
        default: null,
    },
    size: {
        type: String,
        default: "md",
        validator: (value) => ["sm", "md", "lg"].includes(value),
    },
});

// emit actualiza el prop modelValue (v-model desde una pagina)
const emit = defineEmits(["update:modelValue"]);

// slots para prefix/suffix, si se usan se activa el padding izquierdo/derecho
const slots = useSlots();

// isIcon para detectar si prefix o sufix es un objeto (icono) o texto
const isIcon = (val) => val && typeof val === "object";

// --- Logica de tamaños ---
const sizeClasses = {
    sm: "px-3 py-1.5 text-sm",
    md: "px-4 py-2 text-base",
    lg: "px-5 py-3 text-lg",
};

// --- CLASES COMPUTADAS ---
// Detectar si hay contenido en los slots o props para ajustar el padding
const hasPrefix = computed(() => props.prefix || slots.prefix);
const hasSuffix = computed(() => props.suffix || slots.suffix);

// Logica de clases para el input
const inputClasses = computed(() => {
    return [
        "block w-full rounded-lg shadow-sm transition-colors duration-200 disabled:bg-gray-100 disabled:cursor-not-allowed",
        // Tamaño
        sizeClasses[props.size] || sizeClasses.md,
        // Bordes y Colores (Aquí usamos los tokens de tu config)
        props.error
            ? "border-danger text-danger placeholder-danger/50 focus:ring-danger focus:border-danger"
            : "border-gray-300 focus:ring-primary-500 focus:border-primary-500 text-gray-900 placeholder-gray-400",
        // Espaciado para prefix/suffix (Icons)
        hasPrefix.value ? "pl-10" : "",
        hasSuffix.value ? "pr-10" : "",
    ];
});

// IDs para accesibilidad
const helpId = computed(() => `${props.id}-help`);
const errorId = computed(() => `${props.id}-error`);
</script>

<template>
    <div class="w-full">
        <label
            v-if="label"
            :for="id"
            class="block text-sm font-medium text-gray-700 mb-1"
        >
            {{ label }}
            <span v-if="required" class="text-danger">*</span>
        </label>

        <div class="relative">
            <div
                v-if="hasPrefix"
                class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500"
            >
                <slot name="prefix">
                    <FontAwesomeIcon
                        v-if="isIcon(prefix)"
                        :icon="prefix"
                        fixed-width
                    />
                    <span v-else>{{ prefix }}</span>
                </slot>
            </div>

            <input
                :id="id"
                :type="type"
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :class="inputClasses"
                :required="required"
                :disabled="disabled"
                :aria-invalid="!!error"
                :aria-describedby="error ? errorId : help ? helpId : null"
                v-bind="$attrs"
            />

            <div
                v-if="hasSuffix"
                class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-gray-500"
            >
                <slot name="suffix">
                    <FontAwesomeIcon
                        v-if="isIcon(suffix)"
                        :icon="suffix"
                        fixed-width
                    />
                    <span v-else>{{ suffix }}</span>
                </slot>
            </div>
        </div>

        <p
            v-if="error"
            :id="errorId"
            class="mt-1 text-sm text-danger animate-pulse"
        >
            {{ error }}
        </p>
        <p v-else-if="help" :id="helpId" class="mt-1 text-sm text-muted">
            {{ help }}
        </p>
    </div>
</template>
