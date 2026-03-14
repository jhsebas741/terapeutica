<script setup lang="ts">
import { Field, FieldError, FieldLabel } from '@/components/ui/field';
import { Input } from '@/components/ui/input';
import { Textarea } from '@/components/ui/textarea';
import type { Category } from '@/types/data/category';

interface Props {
    defaults?: Partial<Category>;
    errors: Record<string, string>;
}

defineProps<Props>();
</script>

<template>
    <Field :data-invalid="!!errors.name">
        <FieldLabel for="name">
            Nombre <span class="text-destructive">*</span>
        </FieldLabel>
        <Input
            id="name"
            name="name"
            autofocus
            :tabindex="1"
            placeholder="Ej. Emociones, Alimentos..."
            :aria-invalid="!!errors.name"
            :default-value="defaults?.name || ''"
            required
        />
        <FieldError :errors="errors.name" />
    </Field>

    <Field :data-invalid="!!errors.description">
        <FieldLabel>Descripción</FieldLabel>
        <Textarea
            id="description"
            name="description"
            :tabindex="2"
            placeholder="Describe brevemente esta categoría..."
            :aria-invalid="!!errors.description"
            :default-value="defaults?.description || ''"
            rows="3"
        />
        <FieldError :errors="errors.description" />
    </Field>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field :data-invalid="!!errors.icon_emoji">
            <FieldLabel>Icono / Emoji</FieldLabel>
            <Input
                id="icon_emoji"
                name="icon_emoji"
                :tabindex="3"
                placeholder="Ej. 😊, 🍎, 🏠"
                :aria-invalid="!!errors.icon_emoji"
                :default-value="defaults?.icon_emoji || ''"
                maxlength="10"
            />
            <FieldError :errors="errors.icon_emoji" />
        </Field>

        <Field :data-invalid="!!errors.color_hex">
            <FieldLabel>
                Color <span class="text-destructive">*</span>
            </FieldLabel>
            <Input
                id="color_hex"
                name="color_hex"
                type="color"
                :tabindex="4"
                :aria-invalid="!!errors.color_hex"
                :default-value="defaults?.color_hex || '#6BB8FF'"
            />
            <FieldError :errors="errors.color_hex" />
        </Field>
    </div>
</template>
