<template>
    <button
        class="inline-flex items-center justify-center rounded-lg border border-[rgba(255,255,255,0.1)] bg-[linear-gradient(135deg,var(--accent),#8b6914)] px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[#fff] shadow-[0_4px_12px_rgba(201,162,39,0.3)] transition duration-150 ease-in-out hover:brightness-110 active:scale-95 focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2 dark:focus:ring-offset-[#111]"
    >
        <slot />
    </button>
</template>
