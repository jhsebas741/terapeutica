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
    create as categoriesCreate,
    store as categoriesStore,
} from '@/routes/admin/categories';
import type { BreadcrumbItem } from '@/types';
import CategoryFields from './components/CategoryFields.vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categorías',
        href: categoriesIndex().url,
    },
    {
        title: 'Nueva Categoría',
        href: categoriesCreate().url,
    },
];
</script>

<template>
    <Head title="Nueva Categoría" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <Form
                v-bind="categoriesStore.form()"
                v-slot="{ errors, processing }"
            >
                <Card class="w-full sm:w-sm md:w-md lg:w-xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Nueva Categoría
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <CategoryFields :errors="errors" />
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
                            Guardar
                        </Button>
                    </CardFooter>
                </Card>
            </Form>
        </div>
    </AppLayout>
</template>
