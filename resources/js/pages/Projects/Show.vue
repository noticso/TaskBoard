<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { destroy, edit, index } from '@/routes/pages/projects';
import type { Project } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    project: Project;
}>();

function deleteProject() {
    if (confirm(`Eliminare il progetto "${props.project.name}"?`)) {
        router.delete(destroy(props.project.id).url);
    }
}
</script>

<template>
    <Head :title="project.name" />

    <AppLayout>
        <Link :href="index().url" class="text-sm text-gray-500 hover:underline">
            &larr; Torna ai progetti
        </Link>

        <div class="mt-4 flex items-start justify-between">
            <h1 class="text-xl font-semibold">{{ project.name }}</h1>
            <div class="flex gap-3 text-sm">
                <Link
                    :href="edit(project.id).url"
                    class="text-gray-600 hover:underline"
                >
                    Modifica
                </Link>
                <button
                    type="button"
                    class="text-red-600 hover:underline"
                    @click="deleteProject"
                >
                    Elimina
                </button>
            </div>
        </div>

        <p class="mt-4 whitespace-pre-line text-gray-700">
            {{ project.description || 'Nessuna descrizione.' }}
        </p>
    </AppLayout>
</template>
