<script setup lang="ts">
import { format } from '@formkit/tempo';
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import type { Patient } from '@/types/data/patient';

interface Props {
    patient?: Patient;
    errors: Record<string, string>;
}

defineProps<Props>();
</script>

<template>
    <Field :data-invalid="!!errors.name">
        <FieldLabel for="name"> Nombre </FieldLabel>
        <Input
            id="name"
            name="name"
            autofocus
            :default-value="patient?.name || ''"
            placeholder="..."
            :aria-invalid="!!errors.name"
        />
        <FieldError :errors="errors.name" />
    </Field>
    <Field :data-invalid="!!errors.birth_date">
        <FieldLabel for="birth_date"> Fecha de Nacimiento </FieldLabel>
        <Input
            id="birth_date"
            name="birth_date"
            type="date"
            :default-value="
                patient?.birth_date
                    ? format(patient.birth_date, 'YYYY-MM-DD')
                    : ''
            "
            placeholder="..."
            :aria-invalid="!!errors.birth_date"
        />
        <FieldError :errors="errors.birth_date" />
    </Field>
    <Field :data-invalid="!!errors.diagnosis">
        <FieldLabel for="diagnosis"> Diagnóstico </FieldLabel>
        <Input
            id="diagnosis"
            name="diagnosis"
            :default-value="patient?.diagnosis || ''"
            placeholder="..."
            :aria-invalid="!!errors.diagnosis"
        />
        <FieldError :errors="errors.diagnosis" />
    </Field>
    <Field :data-invalid="!!errors.email">
        <FieldLabel for="email"> Email </FieldLabel>
        <Input
            id="email"
            name="email"
            type="email"
            :default-value="patient?.email || ''"
            placeholder="..."
            :aria-invalid="!!errors.email"
        />
        <FieldError :errors="errors.email" />
    </Field>
    <Field :data-invalid="!!errors.password">
        <FieldLabel for="password">
            Contraseña <span v-if="!!patient">(Dejar vacío para Mantener)</span>
        </FieldLabel>
        <Input
            id="password"
            name="password"
            type="password"
            placeholder="..."
            :aria-invalid="!!errors.password"
        />
        <FieldError :errors="errors.password" />
    </Field>
    <Field :data-invalid="!!errors.password_confirmation">
        <FieldLabel for="password_confirmation">
            Confirmar Contraseña
        </FieldLabel>
        <Input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            placeholder="..."
            :aria-invalid="!!errors.password_confirmation"
        />
        <FieldError :errors="errors.password_confirmation" />
    </Field>
</template>
