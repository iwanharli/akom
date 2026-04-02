<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    institution: '',
    email: '',
});

const submit = () => {
    form.post(route('client.register.store'), {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Client Registration" />

    <div class="auth-shell">
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>

        <div class="auth-card">
            <!-- Brand / Logo -->
            <div class="flex justify-center mb-6">
                <Link href="/" class="brand-box">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-6 h-6">
                            <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </Link>
            </div>

            <h2 class="auth-title">Client Registration.</h2>
            <p class="auth-subtitle">
                Apply for secure access to our strategic intelligence intelligence briefings.
            </p>

            <div v-if="$page.props.flash && $page.props.flash.success" class="bg-green-500/10 border border-green-500/20 text-green-400 p-4 rounded-lg text-sm mb-6">
                {{ $page.props.flash.success }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Full Name</label>
                    <input
                        type="text"
                        v-model="form.name"
                        class="form-input"
                        placeholder="e.g. John Doe"
                        required
                    />
                    <div v-if="form.errors.name" class="text-red-400 text-xs mt-1">{{ form.errors.name }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Institution / Organization</label>
                    <input
                        type="text"
                        v-model="form.institution"
                        class="form-input"
                        placeholder="e.g. Kementerian ESDM"
                        required
                    />
                    <div v-if="form.errors.institution" class="text-red-400 text-xs mt-1">{{ form.errors.institution }}</div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                    <input
                        type="email"
                        v-model="form.email"
                        class="form-input"
                        placeholder="work@institution.gov"
                        required
                    />
                    <div v-if="form.errors.email" class="text-red-400 text-xs mt-1">{{ form.errors.email }}</div>
                </div>

                <div class="pt-2">
                    <button
                        type="submit"
                        class="btn-primary w-full"
                        :class="{ 'opacity-50': form.processing }"
                        :disabled="form.processing"
                    >
                        Submit Application
                    </button>
                </div>

                <div class="text-center mt-6">
                    <Link href="/" class="text-sm text-gray-400 hover:text-white transition">
                        &larr; Back to Portal
                    </Link>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>
.auth-shell {
    --bg-primary: #05070a;
    --accent: #c9a227;
    min-height: 100vh;
    background: var(--bg-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
    font-family: 'Inter', sans-serif;
    padding: 24px;
}

.ambient-glow {
    position: absolute;
    width: 600px;
    height: 600px;
    border-radius: 50%;
    filter: blur(140px);
    opacity: 0.15;
    z-index: 0;
    pointer-events: none;
}
.glow-1 {
    background: var(--accent);
    top: -200px;
    right: -100px;
}
.glow-2 {
    background: #1d4ed8;
    bottom: -300px;
    left: -200px;
}

.auth-card {
    background: rgba(22, 27, 34, 0.6);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 24px 48px rgba(0,0,0,0.4);
    border-radius: 16px;
    width: 100%;
    max-width: 440px;
    padding: 40px;
    position: relative;
    z-index: 10;
    color: #e6edf3;
    animation: fadeUp 0.6s ease-out forwards;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.brand-icon {
    width: 48px; height: 48px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 4px 16px rgba(201, 162, 39, 0.3);
}

.auth-title {
    font-size: 24px;
    font-weight: 700;
    letter-spacing: -0.5px;
    text-align: center;
    margin-bottom: 8px;
}

.auth-subtitle {
    font-size: 14px;
    color: #8b949e;
    text-align: center;
    margin-bottom: 32px;
    line-height: 1.5;
}

.form-input {
    width: 100%;
    background: rgba(13, 17, 23, 0.8);
    border: 1px solid rgba(255,255,255,0.1);
    color: #fff;
    border-radius: 8px;
    padding: 12px 14px;
    font-size: 14px;
    transition: all 0.2s;
}
.form-input:focus {
    border-color: var(--accent);
    outline: none;
    box-shadow: 0 0 0 2px rgba(201, 162, 39, 0.2);
}
.form-input::placeholder { color: rgba(255,255,255,0.2); }

.btn-primary {
    background: linear-gradient(135deg, var(--accent), #8b6914);
    color: #fff;
    border: 1px solid rgba(255,255,255,0.1);
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.5px;
    text-transform: uppercase;
    transition: all 0.2s;
    box-shadow: 0 4px 12px rgba(201, 162, 39, 0.2);
    display: inline-flex;
    justify-content: center;
    align-items: center;
}
.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 8px 20px rgba(201, 162, 39, 0.3);
    filter: brightness(1.1);
}
</style>
