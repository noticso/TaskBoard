<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { store } from '@/routes/pages/projects';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    description: '',
});

function submit() {
    form.post(store().url);
}
</script>

<template>
    <Head title="Nuovo progetto" />

    <AppLayout>
        <h1 class="mb-6 text-xl font-semibold">Nuovo progetto</h1>

        <form class="max-w-md space-y-4" @submit.prevent="submit">
            <div>
                <label
                    for="name"
                    class="block text-sm font-medium text-gray-700"
                    >Nome</label
                >
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    autofocus
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label
                    for="description"
                    class="block text-sm font-medium text-gray-700"
                    >Descrizione</label
                >
                <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                ></textarea>
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.description }}
                </p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
            >
                Crea progetto
            </button>
        </form>
    </AppLayout>
</template>
