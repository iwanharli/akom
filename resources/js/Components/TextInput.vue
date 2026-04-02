<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="rounded-lg border-[var(--border)] bg-[var(--bg-tertiary)] text-[var(--text-primary)] shadow-sm focus:border-[var(--accent)] focus:ring-[var(--accent)] transition-all"
        v-model="model"
        ref="input"
    />
</template>
