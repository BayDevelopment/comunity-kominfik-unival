<script setup lang="ts">
import logo from '@/images/logo-kominfik.png';
</script>

<template>
    <div
        class="flex aspect-square size-8 items-center justify-center rounded-md bg-white rounded-lg"
    >
        <img
            :src="logo"
            alt="Kominfik"
            class="size-6 object-contain"
        />
    </div>

    <div class="ml-2 grid flex-1 text-left text-sm">
        <span class="truncate font-semibold">
            KOMINFIK
        </span>
        <span class="text-xs text-muted-foreground">
            Project Management
        </span>
    </div>
</template>
