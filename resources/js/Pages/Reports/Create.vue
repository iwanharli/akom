<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportForm from './Partials/ReportForm.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Create New Intelligence Report" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-bold leading-tight text-slate-800 dark:text-slate-100">
                Create New Intelligence Report
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <ReportForm />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
