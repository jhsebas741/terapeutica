<script setup lang="ts">
import { ref } from 'vue';

const isOpen = ref(false);
const phase = ref<'inhale' | 'hold' | 'exhale'>('inhale');

let animationTimeout: ReturnType<typeof setTimeout>;

const startBreathing = () => {
    isOpen.value = true;
    runCycle();
};

const runCycle = () => {
    phase.value = 'inhale';
    animationTimeout = setTimeout(() => {
        phase.value = 'hold';
        animationTimeout = setTimeout(() => {
            phase.value = 'exhale';
            animationTimeout = setTimeout(() => {
                if (isOpen.value) {
                    runCycle();
                }
            }, 4000);
        }, 2000);
    }, 4000);
};

const close = () => {
    isOpen.value = false;
    clearTimeout(animationTimeout);
};

const phaseLabel = () => {
    switch (phase.value) {
        case 'inhale':
            return 'Inhala...';
        case 'hold':
            return 'Mantén...';
        case 'exhale':
            return 'Exhala...';
    }
};
</script>

<template>
    <!-- Floating trigger button -->
    <button
        @click="startBreathing"
        class="fixed right-4 bottom-4 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-teal-500 text-2xl text-white shadow-lg transition-transform hover:scale-110 hover:bg-teal-600 active:scale-95 sm:h-16 sm:w-16 sm:text-3xl"
        title="Botón de Calma"
    >
        🌊
    </button>

    <!-- Fullscreen overlay -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition-opacity duration-300"
            enter-from-class="opacity-0"
            leave-active-class="transition-opacity duration-300"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-100 flex flex-col items-center justify-center bg-linear-to-b from-teal-600 to-cyan-800"
                @click="close"
            >
                <!-- Breathing circle -->
                <div class="relative flex items-center justify-center">
                    <div
                        class="rounded-full bg-white/20 transition-all"
                        :class="{
                            'h-40 w-40 sm:h-56 sm:w-56':
                                phase === 'inhale' || phase === 'hold',
                            'h-20 w-20 sm:h-28 sm:w-28': phase === 'exhale',
                        }"
                        :style="{
                            transitionDuration:
                                phase === 'hold' ? '0.3s' : '4s',
                            transitionTimingFunction: 'ease-in-out',
                        }"
                    >
                        <div
                            class="flex h-full w-full items-center justify-center rounded-full bg-white/30 transition-all"
                            :class="{
                                'scale-100':
                                    phase === 'inhale' || phase === 'hold',
                                'scale-75': phase === 'exhale',
                            }"
                            :style="{
                                transitionDuration:
                                    phase === 'hold' ? '0.3s' : '4s',
                                transitionTimingFunction: 'ease-in-out',
                            }"
                        >
                            <span class="text-4xl sm:text-5xl">😌</span>
                        </div>
                    </div>
                </div>

                <!-- Phase text -->
                <p
                    class="mt-8 text-2xl font-bold tracking-wide text-white sm:text-3xl"
                >
                    {{ phaseLabel() }}
                </p>

                <!-- Close instruction -->
                <p class="mt-4 text-sm text-white/60">
                    Toca en cualquier lugar para cerrar
                </p>
            </div>
        </Transition>
    </Teleport>
</template>
