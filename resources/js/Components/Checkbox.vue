<script setup>
const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: "" },
    modelValue: { type: Boolean, default: false },
    disabled: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <label :for="id" class="inline-flex items-center">
        <input
            :id="id"
            type="checkbox"
            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
            :checked="modelValue"
            :disabled="disabled"
            @change="emit('update:modelValue', $event.target.checked)"
        />
        <span class="ml-2 text-sm text-gray-700">{{ label }}</span>
    </label>
</template>
