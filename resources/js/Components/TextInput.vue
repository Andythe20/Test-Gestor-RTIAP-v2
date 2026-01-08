<script setup>
// Props a definir: id, label, v-model, :error, placeholder y required
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
        type: String,
        default: "",
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
    type: {
        type: String,
        default: "text",
    },
    customClass: {
        type: String,
        default: "",
    },
});

const emit = defineEmits(["update:modelValue"]);
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            @input="$emit('update:modelValue', $event.target.value)"
            :placeholder="placeholder"
            :class="[
                'mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500',
                error ? 'border-red-500' : 'border-gray-300',
                customClass,
            ]"
            :required="required"
        />
        <p v-if="error" class="text-red-600 text-sm mt-1">{{ error }}</p>
    </div>
</template>
