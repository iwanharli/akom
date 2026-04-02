<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-6 text-center">
            <h1 class="text-2xl font-bold text-white mb-2">Secure Access.</h1>
            <p class="text-sm text-gray-400">Please enter your credentials to access the intelligence portal.</p>
        </div>

        <div v-if="status" class="mb-6 p-4 bg-green-500/10 border border-green-500/20 rounded-lg text-sm font-medium text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Email Address" class="text-gray-300 mb-1.5 ml-1" />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full auth-input"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@institution.gov"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex items-center justify-between mb-1.5 ml-1">
                    <InputLabel for="password" value="Password" class="text-gray-300" />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-xs text-[#c9a227] hover:text-[#e5bf45] transition-colors"
                    >
                        Forgot password?
                    </Link>
                </div>

                <TextInput
                    id="password"
                    type="password"
                    class="block w-full auth-input"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center">
                <Checkbox name="remember" v-model:checked="form.remember" class="auth-checkbox" />
                <span class="ms-2 text-sm text-gray-400">Remember this device</span>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full btn-premium"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Sign In
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

<style scoped>
:deep(.auth-input) {
    background: rgba(13, 17, 23, 0.8) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: white !important;
    border-radius: 10px !important;
    padding: 12px 14px !important;
    transition: all 0.2s !important;
}
:deep(.auth-input:focus) {
    border-color: #c9a227 !important;
    box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.15) !important;
}

.btn-premium {
    background: linear-gradient(135deg, #c9a227, #8b6914) !important;
    color: white !important;
    height: 48px;
    justify-content: center;
    border-radius: 10px !important;
    font-weight: 700 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    font-size: 13px !important;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2) !important;
}
.btn-premium:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(201, 162, 39, 0.3) !important;
}

:deep(.auth-checkbox) {
    background: rgba(13, 17, 23, 0.8) !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    color: #c9a227 !important;
    border-radius: 4px !important;
}
</style>
