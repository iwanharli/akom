<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { confirm, toast } from '@/Utils/Alert';

function debounce(fn, wait) {
    let timer;
    return function (...args) {
        if (timer) clearTimeout(timer);
        timer = setTimeout(() => fn.apply(this, args), wait);
    }
}

const props = defineProps({
    clients: Object,
    filters: Object,
    flash: Object
});

const search = ref(props.filters.search || '');
const showingClientModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const editingClientId = ref(null);

const form = useForm({
    name: '',
    institution: '',
    email: '',
    status: 'Active'
});

watch(search, debounce((value) => {
    router.get(route('clients.index'), { search: value }, {
        preserveState: true,
        replace: true
    });
}, 300));

// Handle Flash Messages with Toasts
watch(() => props.flash, (flash) => {
    if (flash?.success) toast(flash.success);
    if (flash?.error) toast(flash.error, 'error');
}, { deep: true, immediate: true });

const openCreateModal = () => {
    modalMode.value = 'create';
    editingClientId.value = null;
    form.reset();
    form.clearErrors();
    showingClientModal.value = true;
};

const openEditModal = (client) => {
    modalMode.value = 'edit';
    editingClientId.value = client.id;
    form.name = client.name;
    form.institution = client.institution || '';
    form.email = client.email || '';
    form.status = client.status;
    form.clearErrors();
    showingClientModal.value = true;
};

const closeClientModal = () => {
    showingClientModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('clients.store'), {
            onSuccess: () => closeClientModal(),
        });
    } else {
        form.put(route('clients.update', editingClientId.value), {
            onSuccess: () => closeClientModal(),
        });
    }
};

const toggleStatus = (client) => {
    router.put(route('clients.toggle-status', client.id), {}, {
        preserveScroll: true
    });
};

const deleteClient = async (client) => {
    const isConfirmed = await confirm(
        'Delete Client?',
        `Are you sure you want to delete ${client.name}?`,
        'Delete Now'
    );

    if (isConfirmed) {
        router.delete(route('clients.destroy', client.id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="Client Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">Client Management</h2>
                    <p class="page-subtitle">Manage client profiles and access</p>
                </div>
                <div class="header-actions">
                    <div class="search-wrap">
                        <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 108 2a6 6 0 000 12z" clip-rule="evenodd"/></svg>
                        <input v-model="search" type="text" class="search-input" placeholder="Search clients..." />
                    </div>
                    <button @click="openCreateModal" class="btn-primary">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                        Add Client
                    </button>
                </div>
            </div>
        </template>

        <div class="dashboard-container">
            <!-- Flash Messages handled by SweetAlert2 Toast -->

            <div class="card">
                <div v-if="!clients.data || clients.data.length === 0" class="empty-state">
                    <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3>No Clients Directory</h3>
                    <p>The client database is currently empty. Integration coming soon.</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Institution (BUMN)</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="client in clients.data" :key="client.id">
                                <td>
                                    <div class="client-name">{{ client.name }}</div>
                                </td>
                                <td>
                                    <div class="client-institution">{{ client.institution || '—' }}</div>
                                </td>
                                <td>
                                    <div class="client-email">{{ client.email || '—' }}</div>
                                </td>
                                <td>
                                    <button @click="toggleStatus(client)" :class="['badge-btn', client.status.toLowerCase()]" :title="`Click to ${client.status === 'Active' ? 'Deactivate' : 'Activate'}`">
                                        {{ client.status }}
                                    </button>
                                </td>
                                <td class="actions-cell">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openEditModal(client)" class="action-btn edit" title="Edit">
                                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                        </button>
                                        <button @click="deleteClient(client)" class="action-btn delete" title="Delete">
                                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination-wrapper" v-if="clients.links && clients.links.length > 3">
                        <div class="pagination-info">
                            Showing {{ clients.from }} to {{ clients.to }} of {{ clients.total }} results
                        </div>
                        <div class="pagination-links">
                            <template v-for="(link, key) in clients.links" :key="key">
                                <span v-if="link.url === null" class="page-link disabled" v-html="link.label"></span>
                                <Link v-else :href="link.url" :class="['page-link', link.active && 'active']" v-html="link.label"></Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Client Form Modal -->
        <Modal :show="showingClientModal" @close="closeClientModal">
            <div class="p-6">
                <h2 class="text-xl font-bold text-[var(--text-primary)] mb-6">
                    {{ modalMode === 'create' ? 'Add New Client' : 'Edit Client Profile' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Client Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="institution" value="Institution / Organization" />
                        <TextInput id="institution" type="text" class="mt-1 block w-full" v-model="form.institution" />
                        <InputError :message="form.errors.institution" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Address" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="status" value="Status" />
                        <select id="status" v-model="form.status" class="mt-1 block w-full form-select">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                        </select>
                        <InputError :message="form.errors.status" class="mt-2" />
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <SecondaryButton @click="closeClientModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ modalMode === 'create' ? 'Create Client' : 'Update Client' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<style scoped>
/* Copied dashboard styles for consistency */
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
    max-width: 1400px;
    margin: 0 auto;
}

.alert-success {
    background: rgba(63, 185, 80, 0.1);
    border: 1px solid rgba(63, 185, 80, 0.2);
    color: var(--success);
    padding: 12px 16px;
    border-radius: 8px;
    font-size: 14px;
}

.card {
    background: var(--bg-elevated);
    border: 1px solid var(--border);
    border-radius: 12px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.2);
    overflow: hidden;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}
.search-wrap {
    position: relative;
    max-width: 250px;
}
.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    width: 16px;
    height: 16px;
    color: var(--text-muted);
}
.search-input {
    width: 100%;
    padding: 10px 12px 10px 36px;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-tertiary);
    color: var(--text-primary);
    font-size: 13px;
    outline: none;
    transition: all 0.2s;
}
.search-input:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.2);
}

.btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2);
    border: 1px solid rgba(255,255,255,0.1);
    cursor: pointer;
}

/* ─── TABLE STYLES ─── */
.table-responsive {
    overflow-x: auto;
}
.data-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}
.data-table th {
    padding: 16px 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
    background: rgba(0,0,0,0.2);
}
.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid var(--border-subtle);
    vertical-align: middle;
}
.data-table tr:hover {
    background: var(--bg-tertiary);
}

.client-name { font-size: 14px; font-weight: 600; color: var(--text-primary); }
.client-institution { font-size: 13px; color: var(--accent); }
.client-email { font-size: 12px; font-family: monospace; color: var(--text-secondary); }

/* Badges */
.badge-btn {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: var(--bg-tertiary);
    border: 1px solid var(--border);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.2s;
}
.badge-btn:hover {
    filter: brightness(1.2);
    transform: translateY(-1px);
}
.badge-btn.active { color: var(--success); background: rgba(63, 185, 80, 0.1); border-color: rgba(63, 185, 80, 0.2); }
.badge-btn.inactive { color: var(--danger); background: rgba(248, 81, 73, 0.1); border-color: rgba(248, 81, 73, 0.2); }

/* Actions */
.actions-cell {
    text-align: right;
}
.action-btn {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: none;
    border: 1px solid transparent;
    color: var(--text-muted);
    transition: all 0.2s;
    cursor: pointer;
}
.action-btn svg { width: 16px; height: 16px; }
.action-btn.edit:hover { color: var(--accent); background: rgba(201, 162, 39, 0.1); border-color: rgba(201, 162, 39, 0.2); }
.action-btn.delete:hover { color: var(--danger); background: rgba(248, 81, 73, 0.1); border-color: rgba(248, 81, 73, 0.2); }

/* Form Styles */
.text-primary { color: var(--text-primary); }

/* Pagination */
.pagination-wrapper {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 20px;
    border-top: 1px solid var(--border);
    background: var(--bg-secondary);
}
.pagination-info {
    font-size: 12px;
    color: var(--text-secondary);
}
.pagination-links {
    display: flex;
    gap: 4px;
}
.page-link {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 32px;
    height: 32px;
    padding: 0 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-secondary);
    text-decoration: none;
    border: 1px solid transparent;
    transition: all 0.2s;
}
.page-link:hover:not(.disabled):not(.active) {
    background: var(--bg-tertiary);
    color: var(--text-primary);
    background-color: var(--bg-tertiary);
}
.page-link.active {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
}
.page-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
</style>
