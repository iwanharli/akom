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
    users: Object,
    roles: Array,
    filters: Object,
    flash: Object
});

const search = ref(props.filters?.search || '');
const showingUserModal = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const editingUserId = ref(null);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: '',
});

watch(search, debounce((value) => {
    router.get(route('users.index'), { search: value }, {
        preserveState: true,
        replace: true
    });
}, 300));

// Handle Flash Messages with Toasts
watch(() => props.flash, (flash) => {
    if (flash?.success) toast(flash.success);
    if (flash?.error) toast(flash.error, 'error');
}, { deep: true, immediate: true });

const openInviteModal = () => {
    modalMode.value = 'create';
    editingUserId.value = null;
    form.reset();
    form.clearErrors();
    if (props.roles.length > 0) {
        // Default to admin role if available
        const adminRole = props.roles.find(r => r.name === 'admin');
        form.role_id = adminRole ? adminRole.id : props.roles[0].id;
    }
    showingUserModal.value = true;
};

const openEditModal = (user) => {
    modalMode.value = 'edit';
    editingUserId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role_id = user.role_id;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    showingUserModal.value = true;
};

const closeUserModal = () => {
    showingUserModal.value = false;
    form.reset();
};

const submitForm = () => {
    if (modalMode.value === 'create') {
        form.post(route('users.store'), {
            onSuccess: () => closeUserModal(),
        });
    } else {
        form.put(route('users.update', editingUserId.value), {
            onSuccess: () => closeUserModal(),
        });
    }
};

const deleteUser = async (user) => {
    const isConfirmed = await confirm(
        'Remove User?',
        `Are you sure you want to remove ${user.name}? This action cannot be undone.`,
        'Remove Now'
    );

    if (isConfirmed) {
        router.delete(route('users.destroy', user.id), {
            preserveScroll: true
        });
    }
};
</script>

<template>
    <Head title="User Management" />

    <AuthenticatedLayout>
        <template #header>
            <div class="dashboard-header">
                <div>
                    <h2 class="page-title">User Management</h2>
                    <p class="page-subtitle">Superadmin access to system users</p>
                </div>
                <div class="header-actions">
                    <div class="search-wrap">
                        <svg class="search-icon" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M12.9 14.32a8 8 0 111.41-1.41l5.35 5.33-1.42 1.42-5.33-5.34zM8 14A6 6 0 108 2a6 6 0 000 12z" clip-rule="evenodd"/></svg>
                        <input v-model="search" type="text" class="search-input" placeholder="Search users by name or email..." />
                    </div>
                    <button @click="openInviteModal" class="btn-primary">
                        <svg viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/></svg>
                        Invite User
                    </button>
                </div>
            </div>
        </template>

        <div class="dashboard-container">
            <!-- Flash Messages handled by SweetAlert2 Toast -->

            <div class="card">
                <div v-if="!users.data || users.data.length === 0" class="empty-state">
                    <svg class="empty-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3>User Database Empty</h3>
                    <p>No users found matching your search.</p>
                </div>

                <div v-else class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joined</th>
                                <th class="text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users.data" :key="user.id">
                                <td>
                                    <div class="client-name">{{ user.name }}</div>
                                </td>
                                <td>
                                    <div class="client-email">{{ user.email }}</div>
                                </td>
                                <td>
                                    <span :class="['badge', user.role?.name === 'superadmin' ? 'superadmin' : 'admin']">
                                        {{ user.role?.name || 'user' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="date-cell">
                                        {{ new Date(user.created_at).toLocaleDateString() }}
                                    </div>
                                </td>
                                <td class="actions-cell">
                                    <div class="flex justify-end gap-2">
                                        <button @click="openEditModal(user)" class="action-btn edit" title="Edit User">
                                            <svg viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/></svg>
                                        </button>
                                        <button v-if="user.id !== $page.props.auth.user.id" @click="deleteUser(user)" class="action-btn delete" title="Delete User">
                                            <svg viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <div class="pagination-wrapper" v-if="users.links && users.links.length > 3">
                        <div class="pagination-info">
                            Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                        </div>
                        <div class="pagination-links">
                            <template v-for="(link, key) in users.links" :key="key">
                                <span v-if="link.url === null" class="page-link disabled" v-html="link.label"></span>
                                <Link v-else :href="link.url" :class="['page-link', link.active && 'active']" v-html="link.label"></Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Form Modal -->
        <Modal :show="showingUserModal" @close="closeUserModal">
            <div class="p-6">
                <h2 class="text-xl font-bold text-[var(--text-primary)] mb-6">
                    {{ modalMode === 'create' ? 'Invite New User' : 'Edit User Access' }}
                </h2>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <div>
                        <InputLabel for="name" value="Full Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="email" value="Email Address" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="role" value="Assigned Role" />
                        <select id="role" v-model="form.role_id" class="mt-1 block w-full form-select" required>
                            <option value="" disabled>Select a role</option>
                            <option v-for="role in roles" :key="role.id" :value="role.id">
                                {{ role.name.toUpperCase() }} - {{ role.description }}
                            </option>
                        </select>
                        <InputError :message="form.errors.role_id" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <InputLabel for="password" :value="modalMode === 'create' ? 'Password' : 'New Password (optional)'" />
                            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" :required="modalMode === 'create'" />
                            <InputError :message="form.errors.password" class="mt-2" />
                        </div>
                        <div>
                            <InputLabel for="password_confirmation" :value="modalMode === 'create' ? 'Confirm Password' : 'Confirm New Password'" />
                            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" :required="modalMode === 'create' || form.password" />
                            <InputError :message="form.errors.password_confirmation" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end gap-3">
                        <SecondaryButton @click="closeUserModal">Cancel</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ modalMode === 'create' ? 'Invite User' : 'Save Changes' }}
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

.alert-danger {
    background: rgba(248, 81, 73, 0.1);
    border: 1px solid rgba(248, 81, 73, 0.2);
    color: var(--danger);
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
.client-email { font-size: 12px; font-family: monospace; color: var(--text-secondary); }
.date-cell { font-size: 13px; color: var(--text-secondary); }

/* Badges */
.badge {
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
}
.badge.superadmin { color: var(--accent); background: rgba(201, 162, 39, 0.1); border-color: rgba(201, 162, 39, 0.2); }
.badge.admin { color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); }

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
