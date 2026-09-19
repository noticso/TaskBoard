<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { index, update } from '@/routes/pages/projects/tasks';
import type { Project, Task } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    project: Project;
    task: Task;
}>();

const form = useForm({
    title: props.task.title,
    description: props.task.description ?? '',
    priority: props.task.priority,
    status: props.task.status,
    due_date: props.task.due_date?.slice(0, 10) ?? '',
});

function submit() {
    form.put(update({ project: props.project.id, task: props.task.id }).url);
}
</script>

<template>
    <Head title="Modifica task" />

    <AppLayout>
        <h1 class="mb-6 text-xl font-semibold">Modifica task</h1>

        <form class="max-w-md space-y-4" @submit.prevent="submit">
            <div>
                <label
                    for="title"
                    class="block text-sm font-medium text-gray-700"
                    >Titolo</label
                >
                <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">
                    {{ form.errors.title }}
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
                    rows="3"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                ></textarea>
                <p
                    v-if="form.errors.description"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.description }}
                </p>
            </div>

            <div class="flex gap-4">
                <div>
                    <label
                        for="priority"
                        class="block text-sm font-medium text-gray-700"
                        >Priorità</label
                    >
                    <select
                        id="priority"
                        v-model="form.priority"
                        class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label
                        for="status"
                        class="block text-sm font-medium text-gray-700"
                        >Status</label
                    >
                    <select
                        id="status"
                        v-model="form.status"
                        class="mt-1 rounded-md border border-gray-300 px-3 py-2 text-sm"
                    >
                        <option value="todo">Todo</option>
                        <option
                            value="in_progress"
                            :disabled="task.status === 'completed'"
                        >
                            In progress
                        </option>
                        <option value="completed">Completed</option>
                    </select>
                    <p
                        v-if="task.status === 'completed'"
                        class="mt-1 text-xs text-gray-500"
                    >
                        Una task completata non può tornare in progress.
                    </p>
                </div>
            </div>

            <div>
                <label
                    for="due_date"
                    class="block text-sm font-medium text-gray-700"
                    >Scadenza</label
                >
                <input
                    id="due_date"
                    v-model="form.due_date"
                    type="date"
                    class="mt-1 w-full rounded-md border border-gray-300 px-3 py-2 text-sm"
                />
                <p
                    v-if="form.errors.due_date"
                    class="mt-1 text-sm text-red-600"
                >
                    {{ form.errors.due_date }}
                </p>
            </div>

            <div class="flex gap-3">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white disabled:opacity-50"
                >
                    Salva
                </button>
                <Link
                    :href="index(props.project.id).url"
                    class="px-4 py-2 text-sm text-gray-600 hover:underline"
                >
                    Annulla
                </Link>
            </div>
        </form>
    </AppLayout>
</template>
