<script setup>
import { useForm } from "@inertiajs/vue3";
import TextInput from "@/Components/TextInput.vue";
import Button from "@/Components/Button.vue";

const form = useForm({
    email: "",
    password: "",
});

const submit = () => {
    form.post("/login", {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-gray-100">
        <div class="max-w-md w-full bg-white p-8 rounded-lg shadow-md">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>
            <form @submit.prevent="submit">
                <TextInput
                    id="email"
                    label="Email"
                    v-model="form.email"
                    :error="errors.email"
                    placeholder="Enter your email"
                    required
                />
                <div class="mt-4">
                    <TextInput
                        id="password"
                        label="Password"
                        type="password"
                        v-model="form.password"
                        :error="errors.password"
                        placeholder="Enter your password"
                        required
                    />
                </div>
                <div class="mt-6">
                    <Button
                        type="submit"
                        :processing="processing"
                        class="w-full"
                    >
                        Login
                    </Button>
                </div>
            </form>
        </div>
    </div>
</template>
