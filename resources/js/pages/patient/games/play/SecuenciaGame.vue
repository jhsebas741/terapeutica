<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import type { Game, GameElement } from '@/types/data/game';

interface Props {
    game: Game;
}

const { game } = defineProps<Props>();

const startTime = Date.now();
const attempts = ref(0);
const selectedOrder = ref<GameElement[]>([]);
const wrongIndex = ref<number | null>(null);
const isCompleted = ref(false);

const correctOrder = computed(() =>
    [...(game.game_elements ?? [])].sort(
        (a, b) => (a.sequence_order ?? 0) - (b.sequence_order ?? 0),
    ),
);

const shuffledElements = computed(() =>
    [...(game.game_elements ?? [])].sort(() => Math.random() - 0.5),
);

const availableElements = computed(() =>
    shuffledElements.value.filter(
        (el) =>
            !selectedOrder.value.some(
                (s) => s.pictogram_id === el.pictogram_id,
            ),
    ),
);

const selectElement = (element: GameElement) => {
    attempts.value++;
    const expectedIndex = selectedOrder.value.length;
    const expectedElement = correctOrder.value[expectedIndex];

    if (element.pictogram_id === expectedElement.pictogram_id) {
        selectedOrder.value.push(element);
        wrongIndex.value = null;

        if (selectedOrder.value.length === correctOrder.value.length) {
            completeGame();
        }
    } else {
        wrongIndex.value = element.pictogram_id;
        setTimeout(() => {
            wrongIndex.value = null;
        }, 600);
    }
};

const completeGame = () => {
    isCompleted.value = true;
    const timeSpent = Math.round((Date.now() - startTime) / 1000);
    const totalElements = correctOrder.value.length;
    const score = Math.max(0, 100 - (attempts.value - totalElements) * 10);

    router.post(`/child/games/${game.id}/complete`, {
        result: score >= 80 ? 'excelente' : 'completado',
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
            <span class="mb-4 text-7xl">🌟</span>
            <h2 class="mb-2 text-3xl font-extrabold text-green-600 dark:text-green-400">
                ¡Secuencia Correcta!
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
            <p class="mb-4 text-center text-sm font-semibold text-slate-500 dark:text-slate-400">
                Presiona los pasos en el orden correcto · Intentos: {{ attempts }}
            </p>

            <!-- Already selected (correct order) -->
            <div v-if="selectedOrder.length > 0" class="mb-6 space-y-2">
                <h3 class="mb-2 text-center text-sm font-bold text-green-600 dark:text-green-400">
                    ✓ Pasos completados
                </h3>
                <div
                    v-for="(element, idx) in selectedOrder"
                    :key="'done-' + element.pictogram_id"
                    class="flex items-center gap-3 rounded-2xl border-2 border-green-300 bg-green-50 p-3 dark:border-green-800 dark:bg-green-950/20"
                >
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-green-500 text-sm font-bold text-white">
                        {{ idx + 1 }}
                    </span>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-xl border bg-white dark:bg-slate-700">
                        <img
                            v-if="element.pictogram?.image_url"
                            :src="`/storage/${element.pictogram.image_url}`"
                            :alt="element.pictogram?.name"
                            class="h-full w-full object-cover"
                        />
                        <span v-else class="text-lg">{{ element.pictogram?.icon_text }}</span>
                    </div>
                    <span class="font-semibold text-green-700 dark:text-green-300">
                        {{ element.pictogram?.name }}
                    </span>
                </div>
            </div>

            <!-- Available options -->
            <div>
                <h3 class="mb-3 text-center text-sm font-bold text-amber-600 dark:text-amber-400">
                    Paso {{ selectedOrder.length + 1 }}: ¿Qué sigue?
                </h3>
                <div class="grid grid-cols-2 gap-3 sm:gap-4">
                    <button
                        v-for="element in availableElements"
                        :key="'opt-' + element.pictogram_id"
                        @click="selectElement(element)"
                        class="flex flex-col items-center rounded-2xl border-3 p-4 transition-all hover:scale-[1.03] sm:p-5"
                        :class="{
                            'animate-shake border-red-400 bg-red-50 dark:bg-red-950/20': wrongIndex === element.pictogram_id,
                            'border-amber-200 bg-amber-50 hover:border-amber-400 dark:border-amber-700 dark:bg-amber-950/10 dark:hover:border-amber-500': wrongIndex !== element.pictogram_id,
                        }"
                    >
                        <div class="mb-2 flex h-14 w-14 items-center justify-center overflow-hidden rounded-xl sm:h-16 sm:w-16">
                            <img
                                v-if="element.pictogram?.image_url"
                                :src="`/storage/${element.pictogram.image_url}`"
                                :alt="element.pictogram?.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else class="text-3xl sm:text-4xl">{{ element.pictogram?.icon_text }}</span>
                        </div>
                        <span class="text-center text-sm font-bold text-slate-700 dark:text-slate-200">
                            {{ element.pictogram?.name }}
                        </span>
                    </button>
                </div>
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
