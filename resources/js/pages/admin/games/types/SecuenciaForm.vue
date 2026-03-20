<script setup lang="ts">
import { GripVerticalIcon, InfoIcon, PlusIcon, Trash2Icon } from 'lucide-vue-next';
import { computed, ref, toRef } from 'vue';
import { useDraggable } from 'vue-draggable-plus';
import { Button } from '@/components/ui/button';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectLabel,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Slider } from '@/components/ui/slider';

import type { Category } from '@/types/data/category';
import type { GameElement } from '@/types/data/game';
import type { Pictogram } from '@/types/data/pictogram';

interface Props {
    form: any;
    categories: Category[];
}

const props = defineProps<Props>();

const selectedPictogramId = ref<string>('');
const listRef = ref<HTMLElement | null>(null);
const elementsRef = toRef(props.form, 'elements');

useDraggable(listRef, elementsRef, {
    handle: '.drag-handle',
    animation: 200,
    ghostClass: 'opacity-30',
    onUpdate: () => {
        props.form.elements.forEach((element: GameElement, i: number) => {
            element.sequence_order = i + 1;
        });
    },
});

const allPictograms = computed(() => {
    const pictograms: Pictogram[] = [];
    props.categories.forEach((cat) => {
        if (cat.pictograms) {
            pictograms.push(...cat.pictograms);
        }
    });
    return pictograms;
});

const addElement = () => {
    if (!selectedPictogramId.value) return;

    const pictoId = Number(selectedPictogramId.value);
    const pictogram = allPictograms.value.find((p) => p.id === pictoId);

    if (!pictogram) return;

    const newElement: GameElement = {
        pictogram_id: pictoId,
        sequence_order: props.form.elements.length + 1,
        pictogram,
    };

    props.form.elements.push(newElement);
    selectedPictogramId.value = '';
};

const removeElement = (index: number) => {
    props.form.elements.splice(index, 1);
    props.form.elements.forEach((element: GameElement, i: number) => {
        element.sequence_order = i + 1;
    });
};
</script>

<template>
    <div class="space-y-4">
        <div class="mb-6 grid gap-6 md:grid-cols-2">
            <!-- Nombre del Juego -->
            <Field :data-invalid="!!form.errors.name">
                <FieldLabel for="name">Nombre del Juego *</FieldLabel>
                <Input
                    id="name"
                    name="name"
                    v-model="form.name"
                    :aria-invalid="!!form.errors.name"
                    placeholder="Ej. Ir al colegio, Lavarse las manos..."
                />
                <FieldError :errors="form.errors.name" />
            </Field>

            <!-- Dificultad -->
            <Field :data-invalid="!!form.errors.level">
                <FieldLabel for="level" class="mb-2 block"
                    >Nivel de Dificultad ({{ form.level }})</FieldLabel
                >
                <Slider
                    id="level"
                    name="level"
                    :model-value="[form.level]"
                    :min="1"
                    :max="10"
                    :step="1"
                    @update:model-value="form.level = $event?.[0] ?? 1"
                    class="py-4"
                />
                <FieldError :errors="form.errors.level" />
            </Field>
        </div>

        <div class="mb-2 flex flex-col gap-2">
            <Label class="text-base font-semibold"
                >Pasos de la Secuencia</Label
            >
            <p class="text-sm text-muted-foreground">
                Agrega los pictogramas que representan cada paso de la
                actividad. El jugador deberá presionarlos en el orden correcto.
                Puedes arrastrar para reordenar.
            </p>
        </div>

        <!-- Selector de pictograma -->
        <div
            class="flex flex-col gap-2 rounded-lg border border-border/50 bg-muted/30 p-4 sm:flex-row sm:items-end sm:gap-4"
        >
            <div class="flex-1 space-y-2">
                <Label for="pictogram-select-seq" class="text-sm font-medium"
                    >Seleccionar Pictograma</Label
                >
                <Select v-model="selectedPictogramId">
                    <SelectTrigger
                        id="pictogram-select-seq"
                        class="w-full border-primary/20 bg-background"
                    >
                        <SelectValue
                            placeholder="Selecciona un pictograma para añadir..."
                        />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup
                            v-for="category in categories"
                            :key="category.id"
                        >
                            <SelectLabel
                                class="bg-muted/50 font-bold text-primary"
                                >{{ category.name }}</SelectLabel
                            >
                            <SelectItem
                                v-for="pictogram in category.pictograms"
                                :key="pictogram.id"
                                :value="String(pictogram.id)"
                                class="pl-6"
                            >
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex h-6 w-6 items-center justify-center overflow-hidden rounded bg-muted"
                                    >
                                        <img
                                            v-if="pictogram.image_url"
                                            :src="`/storage/${pictogram.image_url}`"
                                            :alt="pictogram.name"
                                            class="h-full w-full object-cover"
                                        />
                                        <span v-else class="text-xs">{{
                                            pictogram.icon_text
                                        }}</span>
                                    </div>
                                    <span>{{ pictogram.name }}</span>
                                </div>
                            </SelectItem>
                        </SelectGroup>
                    </SelectContent>
                </Select>
            </div>
            <Button
                type="button"
                @click="addElement"
                :disabled="!selectedPictogramId"
                class="shrink-0 shadow-sm"
            >
                <PlusIcon class="mr-2 h-4 w-4" /> Añadir Paso
            </Button>
        </div>

        <!-- Empty state -->
        <div
            v-if="form.elements.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed bg-muted/10 p-8 text-center text-muted-foreground"
        >
            <div class="mb-4 rounded-full bg-muted p-3">
                <InfoIcon class="h-6 w-6 text-muted-foreground/70" />
            </div>
            <p class="font-medium text-foreground">
                Aún no hay pasos agregados
            </p>
            <p class="mt-1 text-sm">
                Agrega al menos 2 pictogramas para formar la secuencia de pasos
                que el jugador debe ordenar.
            </p>
        </div>

        <!-- Steps list with drag & drop (always rendered so useDraggable can attach) -->
        <div ref="listRef" class="mt-4 space-y-2" :class="{ hidden: form.elements.length === 0 }">
            <div
                v-for="(element, index) in form.elements"
                :key="`seq-${element.pictogram_id}-${index}`"
                class="flex items-center gap-3 rounded-lg border bg-card p-3 transition-colors hover:bg-muted/50"
            >
                <div
                    class="drag-handle cursor-grab text-muted-foreground active:cursor-grabbing"
                >
                    <GripVerticalIcon class="h-5 w-5" />
                </div>

                <span
                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-primary text-xs font-bold text-primary-foreground"
                >
                    {{ Number(index) + 1 }}
                </span>

                <div
                    v-if="element.pictogram?.image_url"
                    class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border"
                >
                    <img
                        :src="`/storage/${element.pictogram.image_url}`"
                        :alt="element.pictogram.name"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div
                    v-else-if="element.pictogram?.icon_text"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-xl"
                >
                    {{ element.pictogram.icon_text }}
                </div>
                <div
                    v-else
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-lg"
                >
                    🖼️
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">
                        {{ element.pictogram?.name ?? 'Pictograma' }}
                    </p>
                </div>

                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeElement(Number(index))"
                >
                    <Trash2Icon class="h-4 w-4 text-destructive" />
                </Button>
            </div>
        </div>

        <!-- Counter -->
        <div class="mt-2 flex items-start justify-between">
            <div
                :class="{
                    'text-destructive': form.elements.length < 2,
                    'text-green-600': form.elements.length >= 2,
                }"
                class="flex items-center gap-2 text-sm font-medium"
            >
                <InfoIcon class="h-4 w-4" />
                <span
                    >{{ form.elements.length }} paso{{
                        form.elements.length !== 1 ? 's' : ''
                    }}
                    agregado{{ form.elements.length !== 1 ? 's' : '' }}.
                    Mínimo 2 requeridos.</span
                >
            </div>
        </div>
    </div>
</template>
