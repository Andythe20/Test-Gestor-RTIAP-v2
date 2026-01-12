<script setup>
const props = defineProps({
    id: { type: String, required: true },
    label: { type: String, default: "" },
    options: { type: Array, default: () => [] }, // [{ value, label }]
    modelValue: { type: [String, Number, null], default: null },
    error: { type: String, default: "" },
    required: { type: Boolean, default: false },
});
const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <select
            :id="id"
            :value="modelValue"
            @change="emit('update:modelValue', $event.target.value)"
            :class="[
                'mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500',
                error ? 'border-red-500' : 'border-gray-300',
            ]"
            :required="required"
        >
            <option value="" disabled>Selecciona una opción</option>
            <option v-for="opt in options" :key="opt.value" :value="opt.value">
                {{ opt.label }}
            </option>
        </select>
        <p v-if="error" class="text-red-600 text-sm mt-1">{{ error }}</p>
    </div>
</template>
