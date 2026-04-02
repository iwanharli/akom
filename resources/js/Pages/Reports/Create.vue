<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportForm from './Partials/ReportForm.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    clients: Array
});
</script>

<template>
    <Head title="Create New Intelligence Report" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Create Intelligence Report</h2>
                    <p class="page-subtitle">Draft a new secure intelligence brief</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <ReportForm :clients="clients" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
