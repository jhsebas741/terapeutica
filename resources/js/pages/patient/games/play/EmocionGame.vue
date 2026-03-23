<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Volume2Icon } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';
import type { Game, GameElement } from '@/types/data/game';

interface Props {
    game: Game;
}

const { game } = defineProps<Props>();

const startTime = Date.now();
const attempts = ref(0);
const selectedId = ref<number | null>(null);
const isCorrect = ref<boolean | null>(null);
const isCompleted = ref(false);

const speak = (text: string) => {
    if ('speechSynthesis' in window) {
        window.speechSynthesis.cancel();
        const utterance = new SpeechSynthesisUtterance(text);
        utterance.lang = 'es-ES';
        utterance.rate = 0.9;
        window.speechSynthesis.speak(utterance);
    }
};

onMounted(() => {
    if (game.description) {
        speak(`Situación: ${game.description}`);
    }
});

const elements = computed(() => game.game_elements ?? []);

const correctElement = computed(() =>
    [...elements.value].sort(
        (a, b) => (a.sequence_order ?? 0) - (b.sequence_order ?? 0),
    )[0],
);

const shuffledElements = computed(() =>
    [...elements.value].sort(() => Math.random() - 0.5),
);

const selectEmotion = (element: GameElement) => {
    if (isCompleted.value) return;

    attempts.value++;
    selectedId.value = element.pictogram_id;
    speak(element.pictogram?.name ?? '');

    if (element.pictogram_id === correctElement.value?.pictogram_id) {
        isCorrect.value = true;
        setTimeout(() => speak('¡Correcto!'), 500);
        setTimeout(() => {
            completeGame();
        }, 1200);
    } else {
        isCorrect.value = false;
        setTimeout(() => speak('Intenta de nuevo'), 500);
        setTimeout(() => {
            selectedId.value = null;
            isCorrect.value = null;
        }, 1000);
    }
};

const completeGame = () => {
    isCompleted.value = true;
    speak('¡Felicidades! ¡Emoción correcta!');
    const timeSpent = Math.round((Date.now() - startTime) / 1000);
    const score = attempts.value === 1 ? 100 : Math.max(0, 100 - (attempts.value - 1) * 25);

    router.post(`/child/games/${game.id}/complete`, {
        result: attempts.value === 1 ? 'excelente' : 'completado',
        score,
        attempts: attempts.value,
        time_spent_sec: timeSpent,
    });
};

const goBack = () => {
    router.visit('/child/games');
};
</script>

<template>
    <div>
        <!-- Completed -->
        <div
            v-if="isCompleted"
            class="flex flex-col items-center justify-center rounded-3xl bg-white p-12 text-center shadow-lg dark:bg-slate-800"
        >
            <span class="mb-4 text-7xl">😊</span>
            <h2 class="mb-2 text-3xl font-extrabold text-green-600 dark:text-green-400">
                ¡Emoción Correcta!
            </h2>
            <p class="mb-2 text-lg text-slate-500 dark:text-slate-400">
                Intentos: {{ attempts }}
            </p>
            <button
                @click="goBack"
                class="mt-6 rounded-2xl bg-amber-500 px-8 py-4 text-lg font-bold text-white shadow-md transition hover:bg-amber-600"
            >
                Volver a Juegos
            </button>
        </div>

        <!-- Game -->
        <div v-else class="rounded-3xl bg-white p-4 shadow-lg sm:p-8 dark:bg-slate-800">
            <!-- Situation text -->
            <div class="mb-6 rounded-2xl bg-rose-50 p-6 text-center dark:bg-rose-950/20">
                <div class="mb-1 flex items-center justify-center gap-2">
                    <p class="text-sm font-semibold text-rose-500 dark:text-rose-400">
                        Situación:
                    </p>
                    <button
                        @click="speak(`Situación: ${game.description}`)"
                        class="text-rose-400 hover:text-rose-600"
                        title="Escuchar situación"
                    >
                        <Volume2Icon class="h-4 w-4" />
                    </button>
                </div>
                <p class="text-xl font-bold text-slate-800 sm:text-2xl dark:text-slate-100">
                    {{ game.description }}
                </p>
            </div>

            <p class="mb-4 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
                ¿Qué emoción sientes? · Intentos: {{ attempts }}
            </p>

            <!-- Emotion options -->
            <div class="grid grid-cols-2 gap-4 sm:gap-6">
                <button
                    v-for="element in shuffledElements"
                    :key="element.pictogram_id"
                    @click="selectEmotion(element)"
                    :disabled="isCorrect === true"
                    class="flex flex-col items-center rounded-2xl border-3 p-5 transition-all hover:scale-[1.03] sm:p-6"
                    :class="{
                        'border-green-400 bg-green-50 ring-4 ring-green-200 dark:bg-green-950/30 dark:ring-green-800':
                            selectedId === element.pictogram_id && isCorrect === true,
                        'animate-shake border-red-400 bg-red-50 dark:bg-red-950/20':
                            selectedId === element.pictogram_id && isCorrect === false,
                        'border-rose-200 bg-rose-50/50 hover:border-rose-400 dark:border-rose-800 dark:bg-rose-950/10 dark:hover:border-rose-600':
                            selectedId !== element.pictogram_id,
                    }"
                >
                    <div class="mb-3 flex h-20 w-20 items-center justify-center overflow-hidden rounded-2xl sm:h-24 sm:w-24">
                        <img
                            v-if="element.pictogram?.image_url"
                            :src="`/storage/${element.pictogram.image_url}`"
                            :alt="element.pictogram?.name"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-5xl sm:text-6xl">{{
                            element.pictogram?.icon_text
                        }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-center text-base font-bold text-slate-700 sm:text-lg dark:text-slate-200">
                            {{ element.pictogram?.name }}
                        </span>
                        <button
                            @click.stop="speak(element.pictogram?.name ?? '')"
                            class="text-rose-400 hover:text-rose-600"
                            title="Escuchar"
                        >
                            <Volume2Icon class="h-3 w-3" />
                        </button>
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-6px); }
    75% { transform: translateX(6px); }
}
.animate-shake {
    animation: shake 0.3s ease-in-out;
}
</style>
