<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import { ArrowLeftIcon, CheckCircleIcon, ChevronRightIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Routine } from '@/types/data/routine';

interface Props {
    routine: Routine;
}

const { routine } = defineProps<Props>();

const currentStep = ref(0);
const isCompleted = ref(false);
const startTime = Date.now();

const steps = computed(() => routine.routine_steps ?? []);
const totalSteps = computed(() => steps.value.length);
const currentStepData = computed(() => steps.value[currentStep.value]);
const progress = computed(() =>
    totalSteps.value > 0 ? ((currentStep.value + 1) / totalSteps.value) * 100 : 0,
);

const nextStep = () => {
    if (currentStep.value < totalSteps.value - 1) {
        currentStep.value++;
    } else {
        completeRoutine();
    }
};

const prevStep = () => {
    if (currentStep.value > 0) {
        currentStep.value--;
    }
};

const completeRoutine = () => {
    isCompleted.value = true;
    const timeSpent = Math.round((Date.now() - startTime) / 1000);

    router.post(`/child/routines/${routine.id}/complete`, {
        time_spent_sec: timeSpent,
    });
};

const goBack = () => {
    router.visit('/child/routines');
};
</script>

<template>
    <Head :title="routine.name" />
    <ChildLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Completed state -->
            <div
                v-if="isCompleted"
                class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-lg dark:bg-slate-800"
            >
                <span class="mb-4 text-7xl">🎉</span>
                <h2 class="mb-2 text-3xl font-extrabold text-green-600 dark:text-green-400">
                    ¡Rutina Completada!
                </h2>
                <p class="mb-8 text-lg text-slate-500 dark:text-slate-400">
                    ¡Muy bien! Has terminado "{{ routine.name }}"
                </p>
                <button
                    @click="goBack"
                    class="rounded-2xl bg-sky-500 px-8 py-4 text-lg font-bold text-white shadow-md transition hover:bg-sky-600"
                >
                    Volver a Mis Rutinas
                </button>
            </div>

            <!-- Step execution -->
            <div v-else>
                <!-- Header -->
                <div class="mb-6 flex items-center gap-3">
                    <button
                        @click="goBack"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                    </button>
                    <h1 class="text-2xl font-extrabold text-sky-800 sm:text-3xl dark:text-sky-200">
                        {{ routine.name }}
                    </h1>
                </div>

                <!-- Progress bar -->
                <div class="mb-6 overflow-hidden rounded-full bg-sky-100 dark:bg-sky-900/30">
                    <div
                        class="h-3 rounded-full bg-sky-500 transition-all duration-500"
                        :style="{ width: `${progress}%` }"
                    />
                </div>
                <p class="mb-6 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
                    Paso {{ currentStep + 1 }} de {{ totalSteps }}
                </p>

                <!-- Current step card -->
                <div
                    class="flex flex-col items-center rounded-3xl bg-white p-8 shadow-lg sm:p-12 dark:bg-slate-800"
                >
                    <!-- Pictogram -->
                    <div
                        class="mb-6 flex h-48 w-48 items-center justify-center overflow-hidden rounded-3xl border-4 border-sky-200 bg-sky-50 shadow-md sm:h-56 sm:w-56 dark:border-sky-800 dark:bg-sky-900/20"
                    >
                        <img
                            v-if="currentStepData?.pictogram?.image_url"
                            :src="`/storage/${currentStepData.pictogram.image_url}`"
                            :alt="currentStepData.pictogram?.name"
                            class="h-full w-full object-cover"
                        />
                        <span
                            v-else
                            class="text-7xl sm:text-8xl"
                        >{{ currentStepData?.pictogram?.icon_text }}</span>
                    </div>

                    <!-- Step name -->
                    <h2 class="mb-8 text-center text-2xl font-extrabold text-slate-800 sm:text-3xl dark:text-slate-100">
                        {{ currentStepData?.pictogram?.name }}
                    </h2>

                    <!-- Navigation buttons -->
                    <div class="flex w-full gap-3">
                        <button
                            v-if="currentStep > 0"
                            @click="prevStep"
                            class="flex-1 rounded-2xl border-2 border-slate-200 bg-white py-4 text-lg font-bold text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-700 dark:text-slate-300"
                        >
                            ← Anterior
                        </button>
                        <button
                            @click="nextStep"
                            class="flex-1 rounded-2xl py-4 text-lg font-bold text-white shadow-md transition"
                            :class="
                                currentStep === totalSteps - 1
                                    ? 'bg-green-500 hover:bg-green-600'
                                    : 'bg-sky-500 hover:bg-sky-600'
                            "
                        >
                            <span v-if="currentStep === totalSteps - 1" class="flex items-center justify-center gap-2">
                                <CheckCircleIcon class="h-5 w-5" />
                                ¡Terminar!
                            </span>
                            <span v-else class="flex items-center justify-center gap-2">
                                Siguiente
                                <ChevronRightIcon class="h-5 w-5" />
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </ChildLayout>
</template>
