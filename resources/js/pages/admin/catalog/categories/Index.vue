<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { EditIcon, PlusIcon, Trash2Icon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { SearchInput } from '@/components/ui/input';
import { useQueryParams } from '@/composables/useQueryParams';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as categoriesIndex,
    create as categoriesCreate,
    edit as categoriesEdit,
    destroy as categoriesDestroy,
} from '@/routes/admin/categories';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import type { PaginatedData } from '@/types/paginated-data';

interface Props {
    paginatedCategories: PaginatedData<Category>;
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Categorías',
        href: categoriesIndex().url,
    },
];

const { params: filters } = useQueryParams(
    {
        search: '',
        sort: '',
        page: 1,
    },
    {
        route: categoriesIndex,
        only: ['paginatedCategories'],
        preserveScroll: true,
        preserveState: true,
    },
);

const deleteCategory = (category: Category) => {
    if (!confirm('¿Estás seguro de eliminar esta categoría?')) return;
    router.delete(categoriesDestroy(category).url);
};
</script>

<template>
    <Head title="Categorías" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 px-4 py-8">
            <div
                class="flex flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div>
                    <h1 class="text-2xl font-bold text-foreground">
                        Categorías
                    </h1>
                    <p class="text-muted-foreground">
                        Organiza los pictogramas en grupos temáticos.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link :href="categoriesCreate().url">
                        <PlusIcon />
                        Nueva Categoría
                    </Link>
                </Button>
            </div>
            <SearchInput
                id="search"
                :default-value="filters.search"
                placeholder="Buscar categoría..."
                :debounce-ms="300"
                :on-debounced-change="
                    (value: string) => (filters.search = value)
                "
            />
            <div
                v-if="paginatedCategories.data.length === 0"
                class="py-16 text-center text-muted-foreground"
            >
                <span class="mb-3 block text-5xl">📁</span>
                <p class="font-medium">No hay categorías registradas aún.</p>
                <p class="mt-1 text-sm">¡Crea la primera categoría!</p>
            </div>
            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
            >
                <Card
                    v-for="category in paginatedCategories.data"
                    :key="category.id"
                >
                    <CardHeader>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-11 w-11 items-center justify-center rounded-2xl text-2xl"
                                :style="{
                                    backgroundColor: category.color_hex + '20',
                                }"
                            >
                                {{ category.icon_emoji || '📁' }}
                            </div>
                            <div>
                                <CardTitle>{{ category.name }}</CardTitle>
                            </div>
                        </div>
                        <CardDescription>
                            <span
                                class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                            >
                                {{ category.pictograms_count ?? 0 }} pictogramas
                            </span>
                        </CardDescription>
                    </CardHeader>
                    <CardContent v-if="category.description">
                        <p class="line-clamp-2 text-sm text-muted-foreground">
                            {{ category.description }}
                        </p>
                    </CardContent>
                    <CardFooter class="grid grid-cols-2 gap-2">
                        <Button as-child variant="outline">
                            <Link :href="categoriesEdit(category).url">
                                <EditIcon class="h-4 w-4" />
                                Editar
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            @click="deleteCategory(category)"
                        >
                            <Trash2Icon class="h-4 w-4" />
                            Eliminar
                        </Button>
                    </CardFooter>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
