<script setup>
import { computed } from 'vue';

const props = defineProps({
    type: {
        type: String,
        default: 'button',
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
});

const emit = defineEmits(['click']);

const baseClasses = 'px-4 py-2 rounded-lg flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2';

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-gray-300 hover:bg-gray-400 text-gray-800 focus:ring-gray-500';
        case 'primary':
        default:
            return 'bg-blue-600 hover:bg-blue-700 text-white focus:ring-blue-500';
    }
});

const buttonClasses = computed(() => [
    baseClasses,
    variantClasses.value,
    {
        'opacity-50 cursor-not-allowed': props.disabled || props.processing,
    },
]);

const handleClick = (event) => {
    if (!props.disabled && !props.processing) {
        emit('click', event);
    }
};
</script>

<template>
    <button
        :type="type"
        :class="buttonClasses"
        :disabled="disabled || processing"
        @click="handleClick"
    >
        <slot />
    </button>
</template>
