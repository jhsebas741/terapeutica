<script setup lang="ts">
import { GripVerticalIcon, PlusIcon, Trash2Icon } from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';
import { useDraggable } from 'vue-draggable-plus';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import type { Category } from '@/types/data/category';
import type { Pictogram } from '@/types/data/pictogram';
import type { RoutineStep } from '@/types/data/routine';

interface CategoryWithPictograms extends Category {
    pictograms: Pictogram[];
}

interface Props {
    modelValue: RoutineStep[];
    categories: CategoryWithPictograms[];
    errors?: Record<string, string>;
}

const props = defineProps<Props>();
const emit = defineEmits<{
    'update:modelValue': [steps: RoutineStep[]];
}>();

const listRef = ref<HTMLElement | null>(null);
const localSteps = ref<RoutineStep[]>([...props.modelValue]);

watch(
    () => props.modelValue,
    (newVal) => {
        localSteps.value = [...newVal];
    },
);

useDraggable(listRef, localSteps, {
    handle: '.drag-handle',
    animation: 200,
    ghostClass: 'opacity-30',
    onUpdate: () => {
        const reordered = localSteps.value.map((step, i) => ({
            ...step,
            order: i + 1,
        }));
        emit('update:modelValue', reordered);
    },
});

const selectedPictogramId = ref('');

const allPictograms = computed(() =>
    props.categories.flatMap((c) => c.pictograms),
);

const findPictogram = (id: number): Pictogram | undefined =>
    allPictograms.value.find((p) => p.id === id);

const addStep = () => {
    const pictoId = Number(selectedPictogramId.value);
    if (!pictoId) return;

    const pictogram = findPictogram(pictoId);
    if (!pictogram) return;

    const newStep: RoutineStep = {
        pictogram_id: pictoId,
        order: props.modelValue.length + 1,
        pictogram,
    };

    emit('update:modelValue', [...props.modelValue, newStep]);
    selectedPictogramId.value = '';
};

const removeStep = (index: number) => {
    const updated = props.modelValue
        .filter((_, i) => i !== index)
        .map((step, i) => ({ ...step, order: i + 1 }));
    emit('update:modelValue', updated);
};

</script>

<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <label class="text-sm font-medium">
                Pasos de la Rutina
                <span class="text-destructive">*</span>
            </label>
            <span class="text-xs text-muted-foreground">
                {{ modelValue.length }} paso{{
                    modelValue.length !== 1 ? 's' : ''
                }}
            </span>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <Select v-model="selectedPictogramId" class="flex-1">
                <SelectTrigger>
                    <SelectValue placeholder="Selecciona un pictograma..." />
                </SelectTrigger>
                <SelectContent>
                    <template v-for="cat in categories" :key="cat.id">
                        <div
                            v-if="cat.pictograms.length > 0"
                            class="px-2 py-1.5 text-xs font-semibold text-muted-foreground"
                        >
                            {{ cat.icon_emoji }} {{ cat.name }}
                        </div>
                        <SelectItem
                            v-for="picto in cat.pictograms"
                            :key="picto.id"
                            :value="String(picto.id)"
                        >
                            <span v-if="picto.icon_text" class="mr-1">{{
                                picto.icon_text
                            }}</span>
                            {{ picto.name }}
                        </SelectItem>
                    </template>
                </SelectContent>
            </Select>
            <Button
                type="button"
                :disabled="!selectedPictogramId"
                @click="addStep"
            >
                <PlusIcon class="h-4 w-4" />
                Agregar
            </Button>
        </div>

        <p v-if="errors?.steps" class="text-sm text-destructive">
            {{ errors.steps }}
        </p>

        <div
            v-if="modelValue.length === 0"
            class="rounded-lg border border-dashed py-8 text-center text-muted-foreground"
        >
            <span class="mb-2 block text-3xl">📋</span>
            <p class="text-sm">Agrega pictogramas para construir la rutina.</p>
        </div>

        <div ref="listRef" class="space-y-2">
            <div
                v-for="(step, index) in modelValue"
                :key="`step-${index}`"
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
                    {{ index + 1 }}
                </span>

                <div
                    v-if="step.pictogram?.image_url"
                    class="h-10 w-10 shrink-0 overflow-hidden rounded-lg border"
                >
                    <img
                        :src="`/storage/${step.pictogram.image_url}`"
                        :alt="step.pictogram.name"
                        class="h-full w-full object-cover"
                    />
                </div>
                <div
                    v-else-if="step.pictogram?.icon_text"
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-xl"
                >
                    {{ step.pictogram.icon_text }}
                </div>
                <div
                    v-else
                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-muted text-lg"
                >
                    🖼️
                </div>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">
                        {{ step.pictogram?.name ?? 'Pictograma' }}
                    </p>
                </div>


                <Button
                    type="button"
                    variant="ghost"
                    size="icon"
                    @click="removeStep(index)"
                >
                    <Trash2Icon class="h-4 w-4 text-destructive" />
                </Button>
            </div>
        </div>
    </div>
</template>
