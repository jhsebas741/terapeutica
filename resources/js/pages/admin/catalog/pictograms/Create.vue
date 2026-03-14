<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
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
    create as pictogramsCreate,
    store as pictogramsStore,
} from '@/routes/admin/pictograms';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import PictogramFields from './components/PictogramFields.vue';

interface Props {
    categories: Pick<Category, 'id' | 'name' | 'icon_emoji'>[];
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pictogramas',
        href: pictogramsIndex().url,
    },
    {
        title: 'Nuevo Pictograma',
        href: pictogramsCreate().url,
    },
];

const form = useForm({
    category_id: '1',
    name: '',
    description: '',
    image: null as File | null,
    icon_text: '',
    audio: null as File | null,
    difficulty_level: '1',
    is_active: true,
});

const submit = () => {
    form.post(pictogramsStore().url);
};
</script>

<template>
    <Head title="Nuevo Pictograma" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <form @submit.prevent="submit">
                <Card class="w-full sm:w-sm md:w-md lg:w-3xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Nuevo Pictograma
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <PictogramFields
                            :form="form"
                            :categories="categories"
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
                            Guardar
                        </Button>
                    </CardFooter>
                </Card>
            </form>
        </div>
    </AppLayout>
</template>
