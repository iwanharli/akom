<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="guest-shell">
        <div class="ambient-glow glow-1"></div>
        <div class="ambient-glow glow-2"></div>

        <div class="guest-container">
            <!-- Brand / Logo -->
            <div class="auth-brand mb-8">
                <Link href="/" class="brand-box flex items-center justify-center">
                    <div class="brand-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="w-8 h-8">
                            <path d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                </Link>
                <div class="brand-text text-center mt-3">
                    <span class="text-xl font-bold tracking-tight text-white">AKOM <span class="text-[#c9a227]">INTEL</span></span>
                </div>
            </div>

            <div class="guest-card">
                <slot />
            </div>
            
            <div class="mt-8 text-center">
                <Link href="/" class="text-sm text-gray-500 hover:text-white transition">
                    &larr; Back to Public Portal
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.guest-shell {
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

.guest-container {
    width: 100%;
    max-width: 440px;
    position: relative;
    z-index: 10;
}

.guest-card {
    background: rgba(22, 27, 34, 0.6);
    backdrop-filter: blur(24px);
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 24px 48px rgba(0,0,0,0.4);
    border-radius: 20px;
    padding: 40px;
    color: #e6edf3;
    animation: fadeUp 0.6s ease-out forwards;
}

@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.brand-icon {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--accent), #8b6914);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    box-shadow: 0 4px 16px rgba(201, 162, 39, 0.3);
}

.brand-text {
    letter-spacing: 1px;
}
</style>
