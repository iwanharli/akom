<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile Settings" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Profile Settings</h2>
                    <p class="page-subtitle">Manage your account credentials and app personalization</p>
                </div>
            </div>
        </template>

        <div class="dashboard-container">
            <div class="settings-grid">
                <div class="card p-6">
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="max-w-xl"
                    />
                </div>

                <div class="card p-6">
                    <UpdatePasswordForm class="max-w-xl" />
                </div>

                <div class="card p-6 border-danger">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}
.page-title {
    font-size: 24px;
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.5px;
    margin-bottom: 4px;
}
.page-subtitle {
    font-size: 14px;
    color: var(--text-secondary);
}

.dashboard-container {
    padding: 32px 24px;
    max-width: 1000px;
    margin: 0 auto;
}

.settings-grid {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}
.p-6 {
    padding: 32px;
}

.border-danger {
    border-color: rgba(248, 81, 73, 0.3);
    background: linear-gradient(180deg, var(--bg-elevated) 0%, rgba(248, 81, 73, 0.05) 100%);
}

/* Deep style overrides for default Breeze components */
:deep(header h2) {
    font-size: 18px !important;
    font-weight: 700 !important;
    color: var(--text-primary) !important;
    letter-spacing: -0.5px;
}
:deep(header p) {
    color: var(--text-secondary) !important;
    font-size: 14px !important;
}

:deep(.text-gray-900) { color: var(--text-primary) !important; }
:deep(.text-gray-600) { color: var(--text-secondary) !important; }
:deep(.text-sm) { font-size: 13px !important; }

/* Inputs Overrides */
:deep(input[type="text"]),
:deep(input[type="password"]),
:deep(input[type="email"]) {
    background: var(--bg-tertiary) !important;
    border: 1px solid var(--border) !important;
    color: var(--text-primary) !important;
    border-radius: 8px !important;
    padding: 10px 14px !important;
    font-size: 14px !important;
    transition: all 0.2s !important;
    box-shadow: none !important;
}
:deep(input:focus) {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.2) !important;
}

/* Buttons Overrides */
:deep(.bg-gray-800),
:deep(.dark\:bg-gray-200) {
    background: linear-gradient(135deg, var(--accent), #8b6914) !important;
    color: #fff !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    font-weight: 600 !important;
    letter-spacing: 0.5px !important;
    transition: all 0.2s !important;
}
:deep(.bg-gray-800:hover),
:deep(.dark\:bg-gray-200:hover) {
    filter: brightness(1.1) !important;
    transform: translateY(-1px) !important;
}

:deep(.bg-red-600) {
    background: var(--danger) !important;
    color: #fff !important;
    font-weight: 600 !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
}
:deep(.bg-red-600:hover) {
    filter: brightness(1.1) !important;
}

:deep(.dark\:text-gray-400) {
    color: var(--text-secondary) !important;
}

:deep(.mt-6) {
    margin-top: 24px !important;
}
</style>
