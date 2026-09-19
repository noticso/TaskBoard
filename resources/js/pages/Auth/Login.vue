<script setup lang="ts">
import AuthenticatedSessionController from '@/actions/App/Http/Controllers/Auth/AuthenticatedSessionController';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

function submit() {
    form.post(AuthenticatedSessionController.store().url, {
        onFinish: () => form.reset('password'),
    });
}
</script>

<template>
    <Head title="Accedi" />

    <div class="flex min-h-screen items-center justify-center bg-gray-50">
        <form
            class="w-full max-w-sm space-y-4 rounded-lg border border-gray-200 bg-white p-8 shadow-sm"
            @submit.prevent="submit"
        >
            <h1 class="text-xl font-semibold">TaskBoard</h1>

            <div>
                <label
                    for="email"
                    class="block text-sm font-medium text-gray-700"
                    >Email</label
                >
                <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    autofocus
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label
                    for="password"
                    class="block text-sm font-medium text-gray-700"
                    >Password</label
                >
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p
                    v-if="form.errors.password"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.password }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="w-full rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
            >
                {{ form.processing ? 'Accesso in corso...' : 'Accedi' }}
            </button>
        </form>
    </div>
</template>
