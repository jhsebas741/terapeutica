<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import { Volume2Icon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import type { Game } from '@/types/data/game';

interface Props {
    game: Game;
}

const { game } = defineProps<Props>();

const startTime = Date.now();
const attempts = ref(0);
const selectedImage = ref<number | null>(null);
const matchedPairs = ref<number[]>([]);
const wrongPair = ref<{ image: number; text: number } | null>(null);
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

const elements = computed(() => game.game_elements ?? []);

const shuffledImages = computed(() =>
    [...elements.value].sort(() => Math.random() - 0.5),
);
const shuffledTexts = computed(() =>
    [...elements.value].sort(() => Math.random() - 0.5),
);

const selectImage = (pictogramId: number) => {
    if (matchedPairs.value.includes(pictogramId)) return;
    selectedImage.value = pictogramId;
    wrongPair.value = null;

    const element = elements.value.find((e) => e.pictogram_id === pictogramId);
    if (element?.pictogram?.name) {
        speak(element.pictogram.name);
    }
};

const selectText = (pictogramId: number) => {
    if (!selectedImage.value || matchedPairs.value.includes(pictogramId))
        return;

    attempts.value++;

    if (selectedImage.value === pictogramId) {
        matchedPairs.value.push(pictogramId);
        selectedImage.value = null;

        const element = elements.value.find((e) => e.pictogram_id === pictogramId);
        speak(`¡Correcto! ${element?.pictogram?.name ?? ''}`);

        if (matchedPairs.value.length === elements.value.length) {
            completeGame();
        }
    } else {
        wrongPair.value = { image: selectedImage.value, text: pictogramId };
        speak('Intenta de nuevo');
        setTimeout(() => {
            wrongPair.value = null;
            selectedImage.value = null;
        }, 800);
    }
};

const completeGame = () => {
    isCompleted.value = true;
    speak('¡Felicidades! ¡Juego completado!');
    const timeSpent = Math.round((Date.now() - startTime) / 1000);
    const score = Math.max(
        0,
        100 - (attempts.value - elements.value.length) * 10,
    );

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
            <span class="mb-4 text-7xl">🏆</span>
            <h2
                class="mb-2 text-3xl font-extrabold text-green-600 dark:text-green-400"
            >
                ¡Juego Completado!
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

        <!-- Game board -->
        <div
            v-else
            class="rounded-3xl bg-white p-4 shadow-lg sm:p-8 dark:bg-slate-800"
        >
            <p
                class="mb-4 text-center text-sm font-semibold text-slate-500 dark:text-slate-400"
            >
                Selecciona una imagen y luego su nombre · Intentos:
                {{ attempts }}
            </p>

            <!-- Column headers -->
            <div class="mb-2 grid grid-cols-2 gap-4 sm:gap-6">
                <h3 class="text-center text-lg font-bold text-blue-600 dark:text-blue-400">
                    Imágenes
                </h3>
                <h3 class="text-center text-lg font-bold text-purple-600 dark:text-purple-400">
                    Nombres
                </h3>
            </div>

            <!-- Paired rows: image left, text right -->
            <div class="grid grid-cols-2 gap-x-4 gap-y-3 sm:gap-x-6">
                <template
                    v-for="(imgElement, index) in shuffledImages"
                    :key="'row-' + imgElement.pictogram_id"
                >
                    <!-- Image button -->
                    <button
                        @click="selectImage(imgElement.pictogram_id)"
                        :disabled="matchedPairs.includes(imgElement.pictogram_id)"
                        class="flex h-full w-full flex-col items-center justify-center overflow-hidden rounded-2xl border-3 p-3 transition-all sm:p-4"
                        :class="{
                            'border-green-400 bg-green-50 opacity-60 dark:bg-green-950/20':
                                matchedPairs.includes(imgElement.pictogram_id),
                            'border-blue-500 bg-blue-50 ring-4 ring-blue-200 dark:bg-blue-950/30 dark:ring-blue-800':
                                selectedImage === imgElement.pictogram_id,
                            'border-red-400 bg-red-50 dark:bg-red-950/20':
                                wrongPair?.image === imgElement.pictogram_id,
                            'border-slate-200 bg-slate-50 hover:border-blue-300 hover:bg-blue-50 dark:border-slate-600 dark:bg-slate-700 dark:hover:border-blue-500':
                                !matchedPairs.includes(imgElement.pictogram_id) &&
                                selectedImage !== imgElement.pictogram_id &&
                                wrongPair?.image !== imgElement.pictogram_id,
                        }"
                    >
                        <div
                            class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-xl sm:h-20 sm:w-20"
                        >
                            <img
                                v-if="imgElement.pictogram?.image_url"
                                :src="`/storage/${imgElement.pictogram.image_url}`"
                                :alt="imgElement.pictogram?.name"
                                class="h-full w-full object-cover"
                            />
                            <span v-else class="text-4xl sm:text-5xl">{{
                                imgElement.pictogram?.icon_text
                            }}</span>
                        </div>
                        <Volume2Icon class="mt-1 h-3 w-3 text-blue-400" />
                    </button>

                    <!-- Text button (same row) -->
                    <button
                        @click="selectText(shuffledTexts[index].pictogram_id)"
                        :disabled="
                            matchedPairs.includes(shuffledTexts[index].pictogram_id) ||
                            !selectedImage
                        "
                        class="flex h-full w-full items-center justify-center rounded-2xl border-3 p-4 text-center text-base font-bold transition-all sm:p-5 sm:text-lg"
                        :class="{
                            'border-green-400 bg-green-50 opacity-60 dark:bg-green-950/20':
                                matchedPairs.includes(shuffledTexts[index].pictogram_id),
                            'border-red-400 bg-red-50 dark:bg-red-950/20':
                                wrongPair?.text === shuffledTexts[index].pictogram_id,
                            'border-slate-200 bg-slate-50 hover:border-purple-300 hover:bg-purple-50 dark:border-slate-600 dark:bg-slate-700 dark:hover:border-purple-500':
                                !matchedPairs.includes(shuffledTexts[index].pictogram_id) &&
                                wrongPair?.text !== shuffledTexts[index].pictogram_id,
                            'cursor-not-allowed opacity-50':
                                !selectedImage &&
                                !matchedPairs.includes(shuffledTexts[index].pictogram_id),
                        }"
                    >
                        {{ shuffledTexts[index].pictogram?.name }}
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>
