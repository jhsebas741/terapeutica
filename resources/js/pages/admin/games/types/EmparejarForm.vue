<script setup lang="ts">
import { PlusIcon, Trash2Icon, InfoIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
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
    if (!selectedPictogramId.value || props.form.elements.length >= 4) return;

    const pictoId = Number(selectedPictogramId.value);
    const pictogram = allPictograms.value.find((p) => p.id === pictoId);

    if (!pictogram) return;

    if (props.form.elements.length >= 4) {
        return;
    }

    const newElement: GameElement = {
        pictogram_id: pictoId,
        pictogram,
    };

    props.form.elements.push(newElement);
    selectedPictogramId.value = '';
};

const removeElement = (index: number) => {
    props.form.elements.splice(index, 1);
};

const getCategoryColor = (categoryId?: number | null) => {
    if (!categoryId) return '#f3f4f6';
    const category = props.categories.find((c) => c.id === categoryId);
    return category ? category.color_hex : '#f3f4f6';
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
                    placeholder="Ej. Reconocer animales de la granja..."
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
                >Elementos del Juego (Imagen vs Texto)</Label
            >
            <p class="text-sm text-muted-foreground">
                En este juego de emparejar, el participante debe unir la imagen
                mostrada con su nombre correspondiente. Selecciona 4
                pictogramas.
            </p>
        </div>

        <div
            class="flex items-center gap-4 rounded-lg border border-border/50 bg-muted/30 p-4"
        >
            <div class="flex-1 space-y-2">
                <Label for="pictogram-select" class="text-sm font-medium"
                    >Seleccionar Pictograma</Label
                >
                <Select
                    v-model="selectedPictogramId"
                    :disabled="form.elements.length >= 4"
                >
                    <SelectTrigger
                        id="pictogram-select"
                        class="w-full border-primary/20 bg-background"
                    >
                        <SelectValue
                            placeholder="Selecciona un pictograma para emparejar..."
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
                :disabled="!selectedPictogramId || form.elements.length >= 4"
                class="shrink-0 shadow-sm"
            >
                <PlusIcon class="mr-2 h-4 w-4" /> Añadir Pieza
            </Button>
        </div>

        <div
            v-if="form.elements.length === 0"
            class="flex flex-col items-center justify-center rounded-lg border border-dashed bg-muted/10 p-8 text-center text-muted-foreground"
        >
            <div class="mb-4 rounded-full bg-muted p-3">
                <InfoIcon class="h-6 w-6 text-muted-foreground/70" />
            </div>
            <p class="font-medium text-foreground">
                Aún no hay cartas seleccionadas
            </p>
            <p class="mt-1 text-sm">
                Añade exactamente 4 pictogramas para crear tu actividad de
                emparejar.
            </p>
        </div>

        <div v-else class="mt-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <Card
                v-for="(element, index) in form.elements"
                :key="element.pictogram_id + '-' + index"
                class="relative flex flex-col items-center justify-between border-border/60 p-4 shadow-sm transition-colors hover:bg-muted/20"
            >
                <Button
                    type="button"
                    variant="destructive"
                    size="icon"
                    class="absolute -top-2 -right-2 h-6 w-6 rounded-full shadow-sm"
                    @click="removeElement(Number(index))"
                >
                    <Trash2Icon class="h-3 w-3" />
                </Button>

                <div
                    class="mb-2 flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-md border shadow-sm"
                    :style="{
                        backgroundColor: getCategoryColor(
                            element.pictogram?.category_id,
                        ),
                    }"
                >
                    <img
                        v-if="element.pictogram?.image_url"
                        :src="`/storage/${element.pictogram.image_url}`"
                        :alt="element.pictogram?.name"
                        class="h-full w-full object-cover"
                    />
                    <span v-else class="text-3xl">{{
                        element.pictogram?.icon_text
                    }}</span>
                </div>

                <p
                    class="w-full truncate text-center text-sm leading-tight font-medium"
                    :title="element.pictogram?.name"
                >
                    {{ element.pictogram?.name }}
                </p>
            </Card>

            <!-- Placeholders for remaining slots -->
            <div
                v-for="i in 4 - form.elements.length"
                :key="'placeholder-' + i"
                class="flex min-h-[120px] flex-col items-center justify-center rounded-lg border border-dashed bg-muted/5 text-muted-foreground/50"
            >
                <PlusIcon class="mb-2 h-8 w-8 opacity-20" />
                <span class="px-2 text-center text-xs"
                    >Espacio para pictograma</span
                >
            </div>
        </div>

        <div class="mt-2 flex items-start justify-between">
            <div
                :class="{
                    'text-destructive': form.elements.length !== 4,
                    'text-green-600': form.elements.length === 4,
                }"
                class="flex items-center gap-2 text-sm font-medium"
            >
                <InfoIcon class="h-4 w-4" />
                <span
                    >{{ form.elements.length }} de 4 piezas obligatorias
                    seleccionadas.</span
                >
            </div>
        </div>
    </div>
</template>
