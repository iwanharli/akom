<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-lg border border-[var(--border)] bg-transparent px-5 py-2.5 text-xs font-bold uppercase tracking-widest text-[var(--text-secondary)] shadow-sm transition duration-150 ease-in-out hover:bg-[var(--bg-tertiary)] hover:text-[var(--text-primary)] focus:outline-none focus:ring-2 focus:ring-[var(--accent)] focus:ring-offset-2 disabled:opacity-25 dark:focus:ring-offset-[#111]"
    >
        <slot />
    </button>
</template>
