<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";

const props = defineProps({
    type: {
        type: String,
        default: "button",
    },
    href: {
        type: String,
        default: null,
    },
    target: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: "primary", // primary, secondary
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    processing: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: Object,
        default: null,
    },
    iconOnly: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "md", // sm, md, lg
    },
    fullWidth: {
        type: Boolean,
        default: false,
    },
    loadingLabel: {
        type: String,
        default: "Cargando...",
    },
});

const emit = defineEmits(["click"]);

const isExternalLink = computed(() => {
    return (
        props.href &&
        (props.href.startsWith("http") || props.target === "_blank")
    );
});

const isInternalLink = computed(() => {
    return props.href && !isExternalLink.value;
});

const baseClasses =
    "rounded-lg inline-flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2";

const variantClasses = computed(() => {
    switch (props.variant) {
        case "secondary":
            return "bg-gray-300 hover:bg-gray-400 text-gray-800 focus:ring-gray-500";
        case "primary":
        default:
            return "bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500";
    }
});

const componentClasses = computed(() => [
    baseClasses,
    variantClasses.value,
    {
        "opacity-50 cursor-not-allowed": props.disabled || props.processing,
        "p-2": props.iconOnly,
        "px-3 py-1.5 text-sm": !props.iconOnly && props.size === "sm",
        "px-4 py-2 text-base": !props.iconOnly && props.size === "md",
        "px-5 py-3 text-lg": !props.iconOnly && props.size === "lg",
        "w-full": props.fullWidth,
    },
]);

// Tamaño del spinner según el tamaño del botón
const spinnerSizeClass = computed(() => {
    switch (props.size) {
        case "sm":
            return "h-3 w-3";
        case "lg":
            return "h-5 w-5";
        case "md":
        default:
            return "h-4 w-4";
    }
});

// Mostrar spinner si el botón está en procesamiento
const showSpinner = computed(() => props.processing);

const handleClick = (event) => {
    if (props.disabled || props.processing) {
        event.preventDefault();
        return;
    }
    if (!props.href) {
        emit("click", event);
    }
};
</script>

<template>
    <a
        v-if="isExternalLink"
        :href="href"
        :target="target"
        :class="componentClasses"
        @click="handleClick"
        :aria-busy="processing"
        :aria-disabled="disabled || processing"
    >
        <FontAwesomeIcon v-if="icon && !showSpinner" :icon="icon" />
        <svg
            v-if="showSpinner"
            :class="['animate-spin text-current', spinnerSizeClass]"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            />
        </svg>
        <span v-if="!iconOnly" :class="{ 'ml-2': icon || showSpinner }">
            <template v-if="processing && loadingLabel">{{
                loadingLabel
            }}</template>
            <template v-else> <slot /> </template>
        </span>
    </a>
    <Link
        v-else-if="isInternalLink"
        :href="href"
        :class="componentClasses"
        @click="handleClick"
        :aria-busy="processing"
        :aria-disabled="disabled || processing"
    >
        <FontAwesomeIcon v-if="icon && !showSpinner" :icon="icon" />
        <svg
            v-if="showSpinner"
            :class="['animate-spin text-current', spinnerSizeClass]"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            />
        </svg>
        <span v-if="!iconOnly" :class="{ 'ml-2': icon || showSpinner }">
            <template v-if="processing && loadingLabel">{{
                loadingLabel
            }}</template>
            <template v-else><slot /></template>
        </span>
    </Link>
    <button
        v-else
        :type="type"
        :class="componentClasses"
        :disabled="disabled || processing"
        @click="handleClick"
        :aria-busy="processing"
        :aria-disabled="disabled || processing"
    >
        <FontAwesomeIcon v-if="icon && !showSpinner" :icon="icon" />
        <svg
            v-if="showSpinner"
            :class="['animate-spin text-current', spinnerSizeClass]"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
        >
            <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
            />
            <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"
            />
        </svg>
        <span v-if="!iconOnly" :class="{ 'ml-2': icon || showSpinner }">
            <template v-if="processing && loadingLabel">{{
                loadingLabel
            }}</template>
            <template v-else><slot /></template>
        </span>
    </button>
</template>
