<script setup>
import { ref } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faEye, faEyeSlash } from "@fortawesome/free-solid-svg-icons";

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
    required: {
        type: Boolean,
        default: false,
    },
    placeholder: {
        type: String,
        default: "",
    },
    size: {
        type: String,
        default: "md", // sm, md, lg
    },
});

const emit = defineEmits(["update:modelValue"]);
const show = ref(false);
</script>

<template>
    <div>
        <label :for="id" class="block text-sm font-medium text-gray-700">
            {{ label }} <span v-if="required" class="text-red-500">*</span>
        </label>
        <div class="mt-1 relative">
            <input
                :id="id"
                :type="show ? 'text' : 'password'"
                :value="modelValue"
                @input="emit('update:modelValue', $event.target.value)"
                :placeholder="placeholder"
                :class="[
                    'block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500',
                    size === 'sm'
                        ? 'px-3 py-2 text-sm'
                        : size === 'lg'
                        ? 'px-5 py-3 text-lg'
                        : 'px-4 py-2 text-base',
                    'pr-10',
                    error ? 'border-red-500' : 'border-gray-300',
                ]"
                :required="required"
                :aria-invalid="!!error"
            />
            <button
                type="button"
                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-gray-700"
                @click="show = !show"
            >
                <FontAwesomeIcon :icon="show ? faEyeSlash : faEye" />
            </button>
        </div>
        <p v-if="error" class="text-red-600 text-sm mt-1">{{ error }}</p>
    </div>
</template>
