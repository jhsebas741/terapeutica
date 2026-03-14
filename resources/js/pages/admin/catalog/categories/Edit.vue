<script setup lang="ts">
import { Form, Head, Link } from '@inertiajs/vue3';
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
    index as categoriesIndex,
    edit as categoriesEdit,
    update as categoriesUpdate,
} from '@/routes/admin/categories';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import CategoryFields from './components/CategoryFields.vue';

interface Props {
    category: Category;
}
const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categorías',
        href: categoriesIndex().url,
    },
    {
        title: 'Editar Categoría',
        href: categoriesEdit(props.category).url,
    },
];
</script>

<template>
    <Head title="Editar Categoría" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <Form
                v-bind="categoriesUpdate.form(category)"
                v-slot="{ errors, processing }"
            >
                <Card class="w-full sm:w-sm md:w-md lg:w-xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Editar Categoría
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <CategoryFields
                            :errors="errors"
                            :defaults="{
                                name: category.name,
                                description: category.description ?? undefined,
                                icon_emoji: category.icon_emoji ?? undefined,
                                color_hex: category.color_hex,
                            }"
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
                            <Link :href="categoriesIndex()">Cancelar</Link>
                        </Button>
                        <Button
                            type="submit"
                            :disabled="processing"
                            class="w-full sm:w-2/3"
                        >
                            <Spinner v-if="processing" />
                            <SaveIcon v-else />
                            Actualizar
                        </Button>
                    </CardFooter>
                </Card>
            </Form>
        </div>
    </AppLayout>
</template>
