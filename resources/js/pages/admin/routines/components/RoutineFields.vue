<script setup lang="ts">
import type { useForm } from '@inertiajs/vue3';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Textarea } from '@/components/ui/textarea';
import type { RoutineStep } from '@/types/data/routine';

interface RoutineFormData {
    patient_id: string;
    name: string;
    description: string;
    steps: RoutineStep[];
}

interface Props {
    form: ReturnType<typeof useForm<RoutineFormData>>;
    patients: { id: number; name: string }[];
}

defineProps<Props>();
</script>

<template>
    <Field :data-invalid="!!form.errors.name">
        <FieldLabel for="name">
            Nombre <span class="text-destructive">*</span>
        </FieldLabel>
        <Input
            id="name"
            v-model="form.name"
            autofocus
            :tabindex="1"
            placeholder="Ej. Rutina de la mañana, Prepararse para dormir..."
            :aria-invalid="!!form.errors.name"
        />
        <FieldError :errors="form.errors.name" />
    </Field>

    <Field :data-invalid="!!form.errors.patient_id">
        <FieldLabel>
            Paciente <span class="text-destructive">*</span>
        </FieldLabel>
        <Select v-model="form.patient_id">
            <SelectTrigger :tabindex="2">
                <SelectValue placeholder="Selecciona un paciente" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="p in patients"
                    :key="p.id"
                    :value="String(p.id)"
                >
                    {{ p.name }}
                </SelectItem>
            </SelectContent>
        </Select>
        <FieldError :errors="form.errors.patient_id" />
    </Field>

    <Field :data-invalid="!!form.errors.description">
        <FieldLabel>Descripción</FieldLabel>
        <Textarea
            id="description"
            v-model="form.description"
            :tabindex="3"
            placeholder="Describe brevemente esta rutina..."
            :aria-invalid="!!form.errors.description"
            rows="3"
        />
        <FieldError :errors="form.errors.description" />
    </Field>
</template>
