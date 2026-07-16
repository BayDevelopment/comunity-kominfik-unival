<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import logoSrc from '@/images/logo-kominfik.png';

defineOptions({
    inheritAttrs: false,
});

type Props = {
    className?: HTMLAttributes['class'];
};

defineProps<Props>();
</script>

<template>
    <img :src="logoSrc" alt="Logo" :class="className" />
</template>
