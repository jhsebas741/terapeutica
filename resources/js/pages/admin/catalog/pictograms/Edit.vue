<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { SaveIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as pictogramsIndex,
    edit as pictogramsEdit,
    update as pictogramsUpdate,
} from '@/routes/admin/pictograms';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import type { Pictogram } from '@/types/data/pictogram';
import PictogramFields from './components/PictogramFields.vue';

interface Props {
    pictogram: Pictogram;
    categories: Pick<Category, 'id' | 'name' | 'icon_emoji'>[];
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pictogramas',
        href: pictogramsIndex().url,
    },
    {
        title: 'Editar Pictograma',
        href: pictogramsEdit(props.pictogram).url,
    },
];

const form = useForm({
    category_id: props.pictogram.category_id
        ? String(props.pictogram.category_id)
        : '1',
    name: props.pictogram.name,
    description: props.pictogram.description ?? '',
    image: null as File | null,
    icon_text: props.pictogram.icon_text ?? '',
    audio: null as File | null,
    difficulty_level: String(props.pictogram.difficulty_level),
    is_active: props.pictogram.is_active,
});

const submit = () => {
    form.post(pictogramsUpdate(props.pictogram).url, {
        forceFormData: true,
        headers: {
            'X-HTTP-Method-Override': 'PUT',
        },
    });
};
</script>

<template>
    <Head title="Editar Pictograma" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <form @submit.prevent="submit">
                <Card class="w-full sm:w-sm md:w-md lg:w-3xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Editar Pictograma
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <PictogramFields
                            :form="form"
                            :categories="categories"
                            :pictogram="pictogram"
                        />
                    </CardContent>
                    <CardFooter
                        class="flex flex-col gap-4 sm:flex-row sm:justify-between"
                    >
                        <Button
                            variant="outline"
                            as-child
                            class="w-full sm:w-2/7"
                        >
                            <Link :href="pictogramsIndex()">Cancelar</Link>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full sm:w-2/3"
                        >
                            <Spinner v-if="form.processing" />
                            <SaveIcon v-else />
                            Actualizar
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>
