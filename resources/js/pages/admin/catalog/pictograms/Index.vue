<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import { ArchiveIcon, EditIcon, PlusIcon } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { SearchInput } from '@/components/ui/input';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { useQueryParams } from '@/composables/useQueryParams';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    create as pictogramsCreate,
    destroy as pictogramsDestroy,
    edit as pictogramsEdit,
    index as pictogramsIndex,
} from '@/routes/admin/pictograms';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import type { Pictogram } from '@/types/data/pictogram';

interface PictogramGroup {
    category: Pick<Category, 'id' | 'name' | 'icon_emoji' | 'color_hex'>;
    pictograms: Pictogram[];
}

interface Props {
    groupedPictograms: PictogramGroup[];
    categories: Pick<Category, 'id' | 'name' | 'icon_emoji'>[];
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pictogramas',
        href: pictogramsIndex().url,
    },
];

const { params: filters } = useQueryParams(
    {
        search: '',
        category_id: 'all',
        is_active: '',
        sort: '',
        page: 1,
    },
    {
        route: pictogramsIndex,
        only: ['groupedPictograms'],
        preserveScroll: true,
        preserveState: true,
    },
);

const archivePictogram = (pictogram: Pictogram) => {
    if (!confirm('¿Deseas archivar este pictograma?')) return;
    router.delete(pictogramsDestroy(pictogram).url);
};

const difficultyLabel = (level: number): string => {
    const labels: Record<number, string> = {
        1: 'Muy fácil',
        2: 'Fácil',
        3: 'Medio',
        4: 'Difícil',
        5: 'Muy difícil',
    };
    return labels[level] ?? `Nivel ${level}`;
};

const totalPictograms = (groups: PictogramGroup[]): number =>
    groups.reduce((sum, g) => sum + g.pictograms.length, 0);
</script>

<template>
    <Head title="Pictogramas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 px-4 py-8">
            <div
                class="flex flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div>
                    <h1 class="text-2xl font-bold text-foreground">
                        Pictogramas
                    </h1>
                    <p class="text-muted-foreground">
                        Gestiona las imágenes y recursos para rutinas y juegos.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link :href="pictogramsCreate().url">
                        <PlusIcon />
                        Nuevo Pictograma
                    </Link>
                </Button>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row">
                <SearchInput
                    id="search"
                    class="flex-1"
                    :default-value="filters.search"
                    placeholder="Buscar pictograma..."
                    :debounce-ms="300"
                    :on-debounced-change="
                        (value: string) => (filters.search = value)
                    "
                />
                <Select v-model="filters.category_id">
                    <SelectTrigger class="w-full sm:w-48">
                        <SelectValue placeholder="Todas las categorías" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all"
                            >Todas las categorías</SelectItem
                        >
                        <SelectItem
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="String(cat.id)"
                        >
                            {{ cat.icon_emoji }} {{ cat.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div
                v-if="totalPictograms(groupedPictograms) === 0"
                class="py-16 text-center text-muted-foreground"
            >
                <span class="mb-3 block text-5xl">🖼️</span>
                <p class="font-medium">No hay pictogramas registrados aún.</p>
                <p class="mt-1 text-sm">¡Crea el primer pictograma!</p>
            </div>

            <div v-else class="space-y-8">
                <section
                    v-for="group in groupedPictograms"
                    :key="group.category.id"
                >
                    <div class="mb-4 flex items-center gap-3">
                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl text-lg"
                            :style="{
                                backgroundColor:
                                    group.category.color_hex + '20',
                            }"
                        >
                            {{ group.category.icon_emoji || '📁' }}
                        </div>
                        <h2 class="text-lg font-semibold text-foreground">
                            {{ group.category.name }}
                        </h2>
                        <span
                            class="rounded-full bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                        >
                            {{ group.pictograms.length }}
                        </span>
                    </div>

                    <div
                        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"
                    >
                        <Card
                            v-for="pictogram in group.pictograms"
                            :key="pictogram.id"
                        >
                            <CardHeader>
                                <div class="flex items-center gap-3">
                                    <div
                                        v-if="pictogram.image_url"
                                        class="h-14 w-14 shrink-0 overflow-hidden rounded-xl border"
                                    >
                                        <img
                                            :src="`/storage/${pictogram.image_url}`"
                                            :alt="pictogram.name"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <div
                                        v-else-if="pictogram.icon_text"
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-muted text-3xl"
                                    >
                                        {{ pictogram.icon_text }}
                                    </div>
                                    <div
                                        v-else
                                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-muted text-2xl"
                                    >
                                        🖼️
                                    </div>
                                    <div class="min-w-0">
                                        <CardTitle class="truncate">{{
                                            pictogram.name
                                        }}</CardTitle>
                                    </div>
                                </div>
                                <CardAction>
                                    <Badge
                                        :variant="
                                            pictogram.is_active
                                                ? 'default'
                                                : 'secondary'
                                        "
                                    >
                                        {{
                                            pictogram.is_active
                                                ? 'Activo'
                                                : 'Inactivo'
                                        }}
                                    </Badge>
                                </CardAction>
                            </CardHeader>
                            <CardContent>
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        class="rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                    >
                                        {{
                                            difficultyLabel(
                                                pictogram.difficulty_level,
                                            )
                                        }}
                                    </span>
                                    <span
                                        v-if="pictogram.audio_url"
                                        class="rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                                    >
                                        🔊 Con audio
                                    </span>
                                </div>
                            </CardContent>
                            <CardFooter class="grid grid-cols-2 gap-2">
                                <Button as-child variant="outline">
                                    <Link :href="pictogramsEdit(pictogram).url">
                                        <EditIcon class="h-4 w-4" />
                                        Editar
                                    </Link>
                                </Button>
                                <Button
                                    variant="outline"
                                    @click="archivePictogram(pictogram)"
                                >
                                    <ArchiveIcon class="h-4 w-4" />
                                    Archivar
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>
                </section>
            </div>
        </div>
    </AppLayout>
</template>
