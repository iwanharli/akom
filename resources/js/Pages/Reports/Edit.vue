<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import ReportForm from './Partials/ReportForm.vue';
import { Head } from '@inertiajs/vue3';
import { defineProps } from 'vue';

const props = defineProps({
    report: Object,
    clients: Array
});
</script>

<template>
    <Head :title="'Editing: ' + report.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Edit Intelligence Report</h2>
                    <p class="page-subtitle">Modifying: {{ report.title }}</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <ReportForm :report="report" :clients="clients" submit-label="Update Report" :is-edit="true" />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
