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
import type { Category } from '@/types/data/category';
import type { Pictogram } from '@/types/data/pictogram';

interface PictogramFormData {
    category_id: string;
    name: string;
    description: string;
    image: File | null;
    icon_text: string;
    audio: File | null;
    difficulty_level: string;
    is_active: boolean;
}

interface Props {
    form: ReturnType<typeof useForm<PictogramFormData>>;
    categories: Pick<Category, 'id' | 'name' | 'icon_emoji'>[];
    pictogram?: Pictogram;
}

const props = defineProps<Props>();

const handleFileChange = (field: 'image' | 'audio', event: Event) => {
    const target = event.target as HTMLInputElement;
    if (target.files?.length) {
        (props.form as any)[field] = target.files[0];
    }
};
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
            placeholder="Ej. Feliz, Manzana, Casa..."
            :aria-invalid="!!form.errors.name"
        />
        <FieldError :errors="form.errors.name" />
    </Field>

    <Field :data-invalid="!!form.errors.category_id">
        <FieldLabel>
            Categoría <span class="text-destructive">*</span>
        </FieldLabel>
        <Select v-model="form.category_id">
            <SelectTrigger :tabindex="2">
                <SelectValue placeholder="Selecciona una categoría" />
            </SelectTrigger>
            <SelectContent>
                <SelectItem
                    v-for="cat in categories"
                    :key="cat.id"
                    :value="String(cat.id)"
                >
                    {{ cat.icon_emoji }} {{ cat.name }}
                </SelectItem>
            </SelectContent>
        </Select>
        <FieldError :errors="form.errors.category_id" />
    </Field>

    <Field :data-invalid="!!form.errors.description">
        <FieldLabel>Descripción</FieldLabel>
        <Textarea
            id="description"
            v-model="form.description"
            :tabindex="3"
            placeholder="Describe brevemente este pictograma..."
            :aria-invalid="!!form.errors.description"
            rows="3"
        />
        <FieldError :errors="form.errors.description" />
    </Field>

    <div class="rounded-lg border p-4">
        <p class="mb-3 text-sm font-medium">
            Representación visual
            <span class="text-destructive">*</span>
            <span class="text-xs text-muted-foreground">
                (imagen o emoji, al menos uno)
            </span>
        </p>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
            <Field :data-invalid="!!form.errors.image">
                <FieldLabel>Imagen</FieldLabel>
                <div
                    v-if="pictogram?.image_url"
                    class="mb-2 flex items-center gap-2"
                >
                    <img
                        :src="`/storage/${pictogram.image_url}`"
                        :alt="pictogram.name"
                        class="h-16 w-16 rounded-lg border object-cover"
                    />
                    <span class="text-xs text-muted-foreground"
                        >Imagen actual</span
                    >
                </div>
                <Input
                    id="image"
                    type="file"
                    accept="image/jpg,image/jpeg,image/png,image/webp,image/svg+xml"
                    :tabindex="4"
                    :aria-invalid="!!form.errors.image"
                    @change="handleFileChange('image', $event)"
                />
                <FieldError :errors="form.errors.image" />
            </Field>

            <Field :data-invalid="!!form.errors.icon_text">
                <FieldLabel>Emoji / Texto</FieldLabel>
                <div
                    v-if="pictogram?.icon_text"
                    class="mb-2 flex items-center gap-2"
                >
                    <span class="text-3xl">{{ pictogram.icon_text }}</span>
                    <span class="text-xs text-muted-foreground"
                        >Emoji actual</span
                    >
                </div>
                <Input
                    id="icon_text"
                    v-model="form.icon_text"
                    :tabindex="5"
                    placeholder="Ej. 😊, 🍎, 🏠"
                    maxlength="10"
                    :aria-invalid="!!form.errors.icon_text"
                />
                <FieldError :errors="form.errors.icon_text" />
            </Field>
        </div>
    </div>

    <Field :data-invalid="!!form.errors.audio">
        <FieldLabel>Audio</FieldLabel>
        <div v-if="pictogram?.audio_url" class="mb-2">
            <audio controls class="h-8 w-full">
                <source :src="`/storage/${pictogram.audio_url}`" />
            </audio>
        </div>
        <Input
            id="audio"
            type="file"
            accept="audio/mp3,audio/wav,audio/ogg"
            :tabindex="6"
            :aria-invalid="!!form.errors.audio"
            @change="handleFileChange('audio', $event)"
        />
        <FieldError :errors="form.errors.audio" />
    </Field>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <Field :data-invalid="!!form.errors.difficulty_level">
            <FieldLabel>
                Nivel de Dificultad <span class="text-destructive">*</span>
            </FieldLabel>
            <Select v-model="form.difficulty_level">
                <SelectTrigger :tabindex="7">
                    <SelectValue placeholder="Selecciona un nivel" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem value="1">1 - Muy fácil</SelectItem>
                    <SelectItem value="2">2 - Fácil</SelectItem>
                    <SelectItem value="3">3 - Medio</SelectItem>
                    <SelectItem value="4">4 - Difícil</SelectItem>
                    <SelectItem value="5">5 - Muy difícil</SelectItem>
                </SelectContent>
            </Select>
            <FieldError :errors="form.errors.difficulty_level" />
        </Field>
    </div>
</template>
