<script setup lang="ts">
import { Head, router } from '@inertiajs/vue3';
import {
    ArrowLeftIcon,
    CheckCircle2Icon,
    CircleIcon,
    StarIcon,
    Volume2Icon,
} from 'lucide-vue-next';
import { computed, ref } from 'vue';
import ChildLayout from '@/layouts/ChildLayout.vue';
import type { Routine, RoutineStep } from '@/types/data/routine';

interface Props {
    routine: Routine;
}

const { routine } = defineProps<Props>();

const startTime = Date.now();
const completedStepIds = ref<Set<number>>(new Set());
const isCompleted = ref(false);
const showCelebration = ref(false);

const steps = computed(() => routine.routine_steps ?? []);
const totalSteps = computed(() => steps.value.length);
const doneSteps = computed(() => completedStepIds.value.size);
const progress = computed(() =>
    totalSteps.value > 0 ? (doneSteps.value / totalSteps.value) * 100 : 0,
);

const speak = (text: string) => {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-ES';
        utterance.rate = 0.9;
        window.speechSynthesis.speak(utterance);
    }
};

const toggleStep = (step: RoutineStep) => {
    const stepId = step.pictogram_id;
    if (completedStepIds.value.has(stepId)) {
        return;
    }

    completedStepIds.value = new Set([...completedStepIds.value, stepId]);

    const name = step.pictogram?.name ?? '';
    speak(`¡Listo! ${name}`);

    if (completedStepIds.value.size === totalSteps.value) {
        completeRoutine();
    }
};

const isStepDone = (step: RoutineStep) =>
    completedStepIds.value.has(step.pictogram_id);

const completeRoutine = () => {
    showCelebration.value = true;
    speak('¡Felicidades! ¡Completaste toda la rutina!');

    const timeSpent = Math.round((Date.now() - startTime) / 1000);
    router.post(`/child/routines/${routine.id}/complete`, {
        time_spent_sec: timeSpent,
    });

    setTimeout(() => {
        showCelebration.value = false;
        isCompleted.value = true;
    }, 3000);
};

const goBack = () => {
    router.visit('/child/routines');
};
</script>

<template>
    <Head :title="routine.name" />
    <ChildLayout>
        <div class="mx-auto max-w-2xl">
            <!-- Celebration overlay -->
            <Teleport to="body">
                <Transition
                    enter-active-class="transition-opacity duration-300"
                    enter-from-class="opacity-0"
                    leave-active-class="transition-opacity duration-300"
                    leave-to-class="opacity-0"
                >
                    <div
                        v-if="showCelebration"
                        class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-sky-600/90 backdrop-blur-sm"
                    >
                        <span class="mb-4 animate-bounce text-8xl">🎉</span>
                        <h2 class="mb-2 text-4xl font-extrabold text-white">
                            ¡Completaste todo!
                        </h2>
                        <p class="text-xl text-white/80">
                            ¡Excelente trabajo! 🌟
                        </p>
                    </div>
                </Transition>
            </Teleport>

            <!-- Completed state (after celebration) -->
            <div
                v-if="isCompleted"
                class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-lg dark:bg-slate-800"
            >
                <span class="mb-4 text-7xl">🎉</span>
                <h2
                    class="mb-2 text-3xl font-extrabold text-green-600 dark:text-green-400"
                >
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

            <!-- Routine execution -->
            <div v-else>
                <!-- Header -->
                <div class="mb-6 flex items-center gap-3">
                    <button
                        @click="goBack"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-100 text-sky-600 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400"
                    >
                        <ArrowLeftIcon class="h-5 w-5" />
                    </button>
                    <h1
                        class="text-2xl font-extrabold text-sky-800 sm:text-3xl dark:text-sky-200"
                    >
                        {{ routine.name }}
                    </h1>
                </div>

                <!-- Progress card -->
                <div
                    class="mb-5 rounded-3xl border border-sky-200 bg-white p-5 shadow-md dark:border-sky-800 dark:bg-slate-800"
                >
                    <div class="mb-3 flex items-center justify-between">
                        <p
                            class="text-lg font-bold text-slate-800 dark:text-slate-100"
                        >
                            Progreso
                        </p>
                        <div class="flex items-center gap-1">
                            <StarIcon class="h-4 w-4 text-amber-500" />
                            <span
                                class="text-sm font-bold text-slate-700 dark:text-slate-200"
                            >
                                {{ doneSteps }}/{{ totalSteps }}
                            </span>
                        </div>
                    </div>
                    <div
                        class="mb-2 h-4 overflow-hidden rounded-full bg-sky-100 dark:bg-sky-900/30"
                    >
                        <div
                            class="h-full rounded-full bg-sky-500 transition-all duration-500"
                            :style="{ width: `${progress}%` }"
                        />
                    </div>
                    <p
                        class="text-center text-xs text-slate-500 dark:text-slate-400"
                    >
                        {{
                            progress === 100
                                ? '¡Completaste todo! 🎉'
                                : `${Math.round(progress)}% completado`
                        }}
                    </p>
                </div>

                <!-- Steps list -->
                <div class="space-y-3">
                    <div
                        v-for="(step, idx) in steps"
                        :key="step.pictogram_id"
                        class="rounded-3xl border-2 bg-white p-4 shadow-sm transition-all dark:bg-slate-800"
                        :class="
                            isStepDone(step)
                                ? 'border-green-300 opacity-60 dark:border-green-800'
                                : 'border-sky-200 hover:border-sky-400 dark:border-sky-800 dark:hover:border-sky-600'
                        "
                    >
                        <div class="flex items-center gap-4">
                            <!-- Pictogram (clickable for TTS) -->
                            <button
                                @click="speak(step.pictogram?.name ?? '')"
                                class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-2xl transition-all hover:scale-110"
                                :class="
                                    isStepDone(step)
                                        ? 'bg-green-100/50 dark:bg-green-900/20'
                                        : 'bg-sky-50 dark:bg-sky-900/20'
                                "
                                :title="`Escuchar: ${step.pictogram?.name}`"
                            >
                                <img
                                    v-if="step.pictogram?.image_url"
                                    :src="`/storage/${step.pictogram.image_url}`"
                                    :alt="step.pictogram?.name"
                                    class="h-full w-full rounded-xl object-contain"
                                />
                                <span v-else class="text-3xl">{{
                                    step.pictogram?.icon_text ?? '🖼️'
                                }}</span>
                            </button>

                            <!-- Name + description -->
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-sky-100 text-xs font-bold text-sky-600 dark:bg-sky-900/40 dark:text-sky-400"
                                    >
                                        {{ idx + 1 }}
                                    </span>
                                    <p
                                        class="text-lg font-bold"
                                        :class="
                                            isStepDone(step)
                                                ? 'text-slate-400 line-through dark:text-slate-500'
                                                : 'text-slate-800 dark:text-slate-100'
                                        "
                                    >
                                        {{ step.pictogram?.name }}
                                    </p>
                                    <button
                                        @click="
                                            speak(step.pictogram?.name ?? '')
                                        "
                                        class="text-sky-500 hover:text-sky-600 dark:text-sky-400"
                                        title="Escuchar"
                                    >
                                        <Volume2Icon class="h-4 w-4" />
                                    </button>
                                </div>
                                <p
                                    v-if="step.pictogram?.description"
                                    class="mt-0.5 ml-8 text-sm text-slate-500 dark:text-slate-400"
                                >
                                    {{ step.pictogram.description }}
                                </p>
                            </div>

                            <!-- Checkmark button -->
                            <button
                                @click="toggleStep(step)"
                                :disabled="isStepDone(step)"
                                class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition-all"
                                :class="
                                    isStepDone(step)
                                        ? 'bg-green-500 text-white'
                                        : 'bg-sky-100 text-sky-500 hover:bg-sky-500 hover:text-white active:scale-95 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-500 dark:hover:text-white'
                                "
                            >
                                <CheckCircle2Icon
                                    v-if="isStepDone(step)"
                                    class="h-6 w-6"
                                />
                                <CircleIcon v-else class="h-6 w-6" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ChildLayout>
</template>
