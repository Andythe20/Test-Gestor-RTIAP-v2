<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    // Permite pasar 'as' para forzar un tipo de renderizado si fuera necesario
    as: { type: [String, Object], default: null },
    type: { type: String, default: "button" },
    href: { type: String, default: null },
    target: { type: String, default: null }, // para enlaces externos
    // Ahora soportamos más variantes típicas de manuales de marca
    variant: {
        type: String,
        default: "primary",
        validator: (value) =>
            [
                "primary",
                "secondary",
                "danger",
                "success",
                "outline",
                "ghost",
            ].includes(value),
    },
    disabled: { type: Boolean, default: false },
    processing: { type: Boolean, default: false },
    icon: { type: Object, default: null },
    iconOnly: { type: Boolean, default: false },
    size: { type: String, default: "md" },
    fullWidth: { type: Boolean, default: false },
    loadingLabel: { type: String, default: "Cargando..." },
});

const emit = defineEmits(["click"]);

// --- LÓGICA DE URL (La que ya arreglamos) ---
const isExternalLink = computed(() => {
    if (!props.href) return false;
    if (props.target === "_blank") return true;
    if (props.href.startsWith("http")) {
        try {
            const url = new URL(props.href);
            return url.origin !== window.location.origin;
        } catch (e) {
            return true;
        }
    }
    return false;
});

// --- MAGIA 1: DETERMINAR EL COMPONENTE DINÁMICAMENTE ---
// Esto elimina la necesidad de tener 3 bloques en el template
const componentTag = computed(() => {
    if (props.as) return props.as; // Override manual
    if (isExternalLink.value) return "a";
    if (props.href) return Link; // Si hay href y no es externo, es Inertia Link
    return "button";
});

// --- MAGIA 2: MAPA DE ESTILOS (Diseño Atómico) ---
// Fácil de editar según el manual de marca
const variants = {
    primary:
        "bg-primary-600 hover:bg-primary-700 text-white focus:ring-primary-500 border border-transparent",
    secondary:
        "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300 focus:ring-primary-500",
    danger: "bg-red-600 hover:bg-red-700 text-white focus:ring-red-500 border border-transparent",
    success:
        "bg-green-600 hover:bg-green-700 text-white focus:ring-green-500 border border-transparent",
    outline:
        "bg-transparent border-2 border-primary-600 text-primary-600 hover:bg-primary-50 focus:ring-primary-500",
    ghost: "bg-transparent text-gray-600 hover:bg-gray-100 hover:text-gray-900 focus:ring-gray-500",
};

const sizes = {
    sm: "px-3 py-1.5 text-xs",
    md: "px-4 py-2 text-sm",
    lg: "px-6 py-3 text-base",
};

// Clases Base (Estructura y comportamiento, no color)
const baseClasses =
    "inline-flex items-center justify-center font-medium rounded-lg shadow-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";

const componentClasses = computed(() => [
    baseClasses,
    variants[props.variant] || variants.primary, // Fallback a primary
    sizes[props.size] || sizes.md,
    props.fullWidth ? "w-full" : "",
    props.iconOnly ? "!p-2" : "", // !p-2 fuerza el padding cuadrado para íconos solos
]);

// Spinner dinámico
const spinnerSizeClass = computed(() => {
    return props.size === "lg"
        ? "h-5 w-5"
        : props.size === "sm"
        ? "h-3 w-3"
        : "h-4 w-4";
});

const handleClick = (event) => {
    if (props.disabled || props.processing) {
        event.preventDefault();
        return;
    }
    emit("click", event);
};
</script>

<template>
    <component
        :is="componentTag"
        :href="href"
        :target="isExternalLink ? target : null"
        :type="!href ? type : null"
        :class="componentClasses"
        :disabled="!href && (disabled || processing)"
        @click="handleClick"
    >
        <svg
            v-if="processing"
            :class="[
                'animate-spin -ml-1 mr-2',
                spinnerSizeClass,
                { 'mr-0': iconOnly && !loadingLabel },
            ]"
            xmlns="http://www.w3.org/2000/svg"
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
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            ></path>
        </svg>

        <FontAwesomeIcon
            v-if="icon && !processing"
            :icon="icon"
            :class="[iconOnly ? '' : 'mr-2']"
        />

        <span v-if="!iconOnly">
            {{ processing ? loadingLabel : "" }}
            <slot v-if="!processing" />
        </span>
    </component>
</template>
