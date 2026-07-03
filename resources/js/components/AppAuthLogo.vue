<script setup lang="ts">
import logo from '@/images/logo-kominfik.png';
</script>

<template>
    <div class="mb-6 flex justify-center">
        <div class="
                flex
                h-20 w-20
                items-center
                justify-center
                rounded-3xl
                border border-orange-100
                bg-gradient-to-br
                from-white
                via-orange-50
                to-orange-100
                shadow-lg
                shadow-orange-100/60
                transition-all
                duration-300
                hover:scale-105
                hover:shadow-xl
                hover:shadow-orange-200/50

                sm:h-24
                sm:w-24

                lg:h-28
                lg:w-28
            ">
            <img :src="logo" alt="KOMINFIK" class="
                    h-12
                    w-12
                    object-contain

                    sm:h-14
                    sm:w-14

                    lg:h-16
                    lg:w-16
                " />
        </div>
    </div>
</template>
