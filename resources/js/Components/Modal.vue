<script setup>
import { onMounted, onUnmounted } from "vue";
import { faTimes } from "@fortawesome/free-solid-svg-icons";

const props = defineProps({
    show: { type: Boolean, default: false },
    maxWidth: { type: String, default: "2xl" },
    closeable: { type: Boolean, default: true },
    title: { type: String, default: "" },
});

const emit = defineEmits(["close"]);

const close = () => {
    if (props.closeable) {
        emit("close");
    }
};

const closeOnEscape = (e) => {
    if (e.key === "Escape" && props.show) {
        close();
    }
};

onMounted(() => document.addEventListener("keydown", closeOnEscape));
onUnmounted(() => document.removeEventListener("keydown", closeOnEscape));

// Definir ancho máximo según la prop
const maxWidthClass = {
    sm: "sm:max-w-sm",
    md: "sm:max-w-md",
    lg: "sm:max-w-lg",
    xl: "sm:max-w-xl",
    "2xl": "sm:max-w-2xl",
}[props.maxWidth];
</script>

<template>
    <Teleport to="body">
        <div
            v-show="show"
            class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
            scroll-region
        >
            <transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-show="show"
                    class="fixed inset-0 transform transition-all"
                    @click="close"
                >
                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                </div>
            </transition>

            <transition
                enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            >
                <div
                    v-show="show"
                    class="mb-6 bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full sm:mx-auto border border-gray-700 relative z-10"
                    :class="maxWidthClass"
                >
                    <div
                        v-if="title"
                        class="px-6 py-4 bg-gray-900 border-b border-gray-700 flex justify-between items-center"
                    >
                        <h3 class="text-lg font-medium text-white">
                            {{ title }}
                        </h3>
                        <button
                            @click="close"
                            class="text-gray-400 hover:text-white transition focus:outline-none"
                        >
                            <font-awesome-icon :icon="faTimes" />
                        </button>
                    </div>

                    <div class="px-6 py-4">
                        <slot />
                    </div>

                    <div
                        class="px-6 py-4 bg-gray-900/50 border-t border-gray-700 flex justify-end gap-3"
                    >
                        <slot name="footer" />
                    </div>
                </div>
            </transition>
        </div>
    </Teleport>
</template>
