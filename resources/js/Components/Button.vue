<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
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
        default: 'primary', // primary, secondary
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
});

const emit = defineEmits(['click']);

const isExternalLink = computed(() => {
    return props.href && (props.href.startsWith('http') || props.target === '_blank');
});

const isInternalLink = computed(() => {
    return props.href && !isExternalLink.value;
});

const baseClasses = 'rounded-lg inline-flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-gray-300 hover:bg-gray-400 text-gray-800 focus:ring-gray-500';
        case 'primary':
        default:
            return 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500';
    }
});

const componentClasses = computed(() => [
    baseClasses,
    variantClasses.value,
    {
        'opacity-50 cursor-not-allowed': props.disabled || props.processing,
        'p-2': props.iconOnly,
        'px-4 py-2': !props.iconOnly,
    },
]);

const handleClick = (event) => {
    if (props.disabled || props.processing) {
        event.preventDefault();
        return;
    }
    if (!props.href) {
        emit('click', event);
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
    >
        <FontAwesomeIcon v-if="icon" :icon="icon" />
        <span v-if="!iconOnly && $slots.default" :class="{ 'ml-2': icon }">
            <slot />
        </span>
    </a>
    <Link
        v-else-if="isInternalLink"
        :href="href"
        :class="componentClasses"
        @click="handleClick"
    >
        <FontAwesomeIcon v-if="icon" :icon="icon" />
        <span v-if="!iconOnly && $slots.default" :class="{ 'ml-2': icon }">
            <slot />
        </span>
    </Link>
    <button
        v-else
        :type="type"
        :class="componentClasses"
        :disabled="disabled || processing"
        @click="handleClick"
    >
        <FontAwesomeIcon v-if="icon" :icon="icon" />
        <span v-if="!iconOnly && $slots.default" :class="{ 'ml-2': icon }">
            <slot />
        </span>
    </button>
</template>
