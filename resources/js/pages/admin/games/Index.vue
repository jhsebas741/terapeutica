<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ArrowRightLeftIcon,
    EditIcon,
    HeartIcon,
    ListOrderedIcon,
    PlusIcon,
    Trash2Icon,
} from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
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
import { create, destroy, edit, index } from '@/routes/admin/games';
import type { BreadcrumbItem } from '@/types';
import type { Game } from '@/types/data/game';
import type { PaginatedData } from '@/types/paginated-data';

interface Props {
    paginatedGames: PaginatedData<Game>;
}

const { paginatedGames } = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Juegos Dinámicos',
        href: index().url,
    },
];

const { params: filters } = useQueryParams(
    {
        search: '',
        type: 'all',
        level: 'all',
        sort: '',
        page: 1,
    },
    {
        route: index,
        only: ['paginatedGames'],
        preserveScroll: true,
        preserveState: true,
    },
);

const deleteGame = (game: Game) => {
    if (
        confirm(`¿Estás seguro de que deseas eliminar el juego: ${game.name}?`)
    ) {
        router.delete(destroy(game).url);
    }
};

const getTypeLabel = (type: string) => {
    const labels: Record<string, string> = {
        emparejar: 'Emparejar',
        secuencia: 'Secuencia',
        emocion: 'Emociones',
    };
    return labels[type] || type;
};

const getTypeConfig = (type: string) => {
    const configs: Record<
        string,
        {
            iconBg: string;
            badgeClass: string;
            accentBorder: string;
            description: string;
        }
    > = {
        emparejar: {
            iconBg: 'bg-blue-100 text-blue-600 dark:bg-blue-950 dark:text-blue-400',
            badgeClass:
                'bg-blue-100 text-blue-700 border-blue-200 dark:bg-blue-950 dark:text-blue-300 dark:border-blue-800',
            accentBorder: 'border-t-blue-500',
            description: 'Asociar imagen con texto',
        },
        secuencia: {
            iconBg: 'bg-amber-100 text-amber-600 dark:bg-amber-950 dark:text-amber-400',
            badgeClass:
                'bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-950 dark:text-amber-300 dark:border-amber-800',
            accentBorder: 'border-t-amber-500',
            description: 'Ordenar pasos correctamente',
        },
        emocion: {
            iconBg: 'bg-rose-100 text-rose-600 dark:bg-rose-950 dark:text-rose-400',
            badgeClass:
                'bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-950 dark:text-rose-300 dark:border-rose-800',
            accentBorder: 'border-t-rose-500',
            description: 'Identificar la emoción correcta',
        },
    };
    return (
        configs[type] || {
            iconBg: 'bg-muted text-muted-foreground',
            badgeClass: '',
            accentBorder: 'border-t-primary',
            description: '',
        }
    );
};
</script>

<template>
    <Head title="Juegos Dinámicos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container flex-1 space-y-4 p-4 pt-6 md:p-8">
            <div class="flex items-center justify-between">
                <h2 class="text-3xl font-bold tracking-tight text-primary">
                    Juegos Dinámicos
                </h2>

                <DropdownMenu>
                    <DropdownMenuTrigger as-child>
                        <Button>
                            <PlusIcon class="mr-2 h-4 w-4" />
                            Nuevo Juego
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end" class="w-56">
                        <DropdownMenuItem as-child class="cursor-pointer">
                            <Link
                                :href="
                                    create({
                                        mergeQuery: { type: 'emparejar' },
                                    })
                                "
                            >
                                <ArrowRightLeftIcon class="mr-2 h-4 w-4 text-blue-500" />
                                Juego de Emparejar
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem as-child class="cursor-pointer">
                            <Link
                                :href="
                                    create({
                                        mergeQuery: { type: 'secuencia' },
                                    })
                                "
                            >
                                <ListOrderedIcon class="mr-2 h-4 w-4 text-amber-500" />
                                Secuencia Lógica
                            </Link>
                        </DropdownMenuItem>
                        <DropdownMenuItem as-child class="cursor-pointer">
                            <Link
                                :href="
                                    create({ mergeQuery: { type: 'emocion' } })
                                "
                            >
                                <HeartIcon class="mr-2 h-4 w-4 text-rose-500" />
                                Reconocimiento de Emociones
                            </Link>
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            <Card>
                <CardHeader>
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <CardTitle>Catálogo de Juegos</CardTitle>
                            <CardDescription>
                                Gestiona los juegos disponibles para los
                                pacientes y niveles.
                            </CardDescription>
                        </div>
                        <div
                            class="flex flex-col gap-2 sm:flex-row sm:items-center"
                        >
                            <SearchInput
                                v-model="filters.search"
                                placeholder="Buscar juegos..."
                                class="w-full sm:w-[300px]"
                            />

                            <Select v-model="filters.type">
                                <SelectTrigger class="w-full sm:w-[150px]">
                                    <SelectValue placeholder="Tipo de juego" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all">
                                        Todos los tipos
                                    </SelectItem>
                                    <SelectItem value="emparejar">
                                        Emparejar
                                    </SelectItem>
                                    <SelectItem value="secuencia">
                                        Secuencias
                                    </SelectItem>
                                    <SelectItem value="emocion">
                                        Emociones
                                    </SelectItem>
                                </SelectContent>
                            </Select>

                            <Select v-model="filters.level">
                                <SelectTrigger class="w-full sm:w-[150px]">
                                    <SelectValue placeholder="Nivel" />
                                </SelectTrigger>
                                <SelectContent>
                                    <SelectItem value="all"
                                        >Todos los niveles</SelectItem
                                    >
                                    <SelectItem value="1">Nivel 1</SelectItem>
                                    <SelectItem value="2">Nivel 2</SelectItem>
                                    <SelectItem value="3">Nivel 3</SelectItem>
                                    <SelectItem value="4">Nivel 4</SelectItem>
                                    <SelectItem value="5">Nivel 5</SelectItem>
                                </SelectContent>
                            </Select>
                        </div>
                    </div>
                </CardHeader>
                <CardContent>
                    <div
                        v-if="paginatedGames.data.length === 0"
                        class="flex flex-col items-center justify-center py-12 text-center text-muted-foreground"
                    >
                        <p class="mb-4">
                            No se encontraron juegos con los filtros
                            seleccionados.
                        </p>
                        <Button
                            variant="outline"
                            @click="filters.search = ''"
                            v-if="
                                filters.search ||
                                filters.type !== 'all' ||
                                filters.level !== 'all'
                            "
                        >
                            Limpiar filtros
                        </Button>
                    </div>

                    <div
                        v-else
                        class="grid gap-4 md:grid-cols-2 lg:grid-cols-3"
                    >
                        <Card
                            v-for="game in paginatedGames.data"
                            :key="game.id"
                            class="relative flex flex-col justify-between overflow-hidden border-t-4 transition-shadow hover:shadow-md"
                            :class="getTypeConfig(game.type).accentBorder"
                        >
                            <CardHeader class="pb-3">
                                <div
                                    class="flex items-start justify-between gap-2"
                                >
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                                            :class="
                                                getTypeConfig(game.type).iconBg
                                            "
                                        >
                                            <ArrowRightLeftIcon
                                                v-if="
                                                    game.type === 'emparejar'
                                                "
                                                class="h-4 w-4"
                                            />
                                            <ListOrderedIcon
                                                v-else-if="
                                                    game.type === 'secuencia'
                                                "
                                                class="h-4 w-4"
                                            />
                                            <HeartIcon
                                                v-else
                                                class="h-4 w-4"
                                            />
                                        </div>
                                        <div class="min-w-0">
                                            <CardTitle
                                                class="line-clamp-1 text-base"
                                                >{{
                                                    game.name
                                                }}</CardTitle
                                            >
                                            <p
                                                class="text-xs text-muted-foreground"
                                            >
                                                {{
                                                    getTypeConfig(game.type)
                                                        .description
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex shrink-0 gap-1.5">
                                        <Badge
                                            variant="outline"
                                            class="text-xs"
                                            >Nv.
                                            {{ game.level }}</Badge
                                        >
                                        <Badge
                                            class="border text-xs"
                                            :class="
                                                getTypeConfig(game.type)
                                                    .badgeClass
                                            "
                                            >{{
                                                getTypeLabel(game.type)
                                            }}</Badge
                                        >
                                    </div>
                                </div>
                            </CardHeader>

                            <CardContent class="pt-0 pb-3">
                                <!-- Emparejar preview: 2x2 grid of pictograms -->
                                <div
                                    v-if="
                                        game.type === 'emparejar' &&
                                        game.game_elements?.length
                                    "
                                    class="grid grid-cols-4 gap-2"
                                >
                                    <div
                                        v-for="element in game.game_elements"
                                        :key="element.id"
                                        class="flex flex-col items-center gap-1"
                                    >
                                        <div
                                            class="flex h-12 w-12 items-center justify-center overflow-hidden rounded-lg border bg-blue-50/50 shadow-sm dark:bg-blue-950/30"
                                        >
                                            <img
                                                v-if="
                                                    element.pictogram
                                                        ?.image_url
                                                "
                                                :src="`/storage/${element.pictogram.image_url}`"
                                                :alt="element.pictogram?.name"
                                                class="h-full w-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-lg"
                                                >{{
                                                    element.pictogram
                                                        ?.icon_text
                                                }}</span
                                            >
                                        </div>
                                        <span
                                            class="max-w-[60px] truncate text-[10px] text-muted-foreground"
                                            >{{
                                                element.pictogram?.name
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <!-- Secuencia preview: ordered list of steps -->
                                <div
                                    v-else-if="
                                        game.type === 'secuencia' &&
                                        game.game_elements?.length
                                    "
                                    class="space-y-1.5"
                                >
                                    <div
                                        v-for="(element, idx) in game
                                            .game_elements"
                                        :key="element.id"
                                        class="flex items-center gap-2 rounded-md bg-amber-50/50 px-2 py-1.5 dark:bg-amber-950/20"
                                    >
                                        <span
                                            class="flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 text-[10px] font-bold text-white"
                                            >{{ idx + 1 }}</span
                                        >
                                        <div
                                            class="flex h-7 w-7 shrink-0 items-center justify-center overflow-hidden rounded border bg-background"
                                        >
                                            <img
                                                v-if="
                                                    element.pictogram
                                                        ?.image_url
                                                "
                                                :src="`/storage/${element.pictogram.image_url}`"
                                                :alt="element.pictogram?.name"
                                                class="h-full w-full object-cover"
                                            />
                                            <span
                                                v-else
                                                class="text-sm"
                                                >{{
                                                    element.pictogram
                                                        ?.icon_text
                                                }}</span
                                            >
                                        </div>
                                        <span
                                            class="truncate text-xs font-medium"
                                            >{{
                                                element.pictogram?.name
                                            }}</span
                                        >
                                    </div>
                                </div>

                                <!-- Emociones preview: situation + correct answer highlighted -->
                                <div
                                    v-else-if="game.type === 'emocion'"
                                    class="space-y-2"
                                >
                                    <div
                                        v-if="game.description"
                                        class="rounded-md bg-rose-50/50 p-2 text-xs dark:bg-rose-950/20"
                                    >
                                        <span
                                            class="font-semibold text-rose-600 dark:text-rose-400"
                                            >Situación:</span
                                        >
                                        <span class="ml-1 line-clamp-2">{{
                                            game.description
                                        }}</span>
                                    </div>
                                    <div
                                        v-if="game.game_elements?.length"
                                        class="flex gap-2"
                                    >
                                        <div
                                            v-for="(
                                                element, idx
                                            ) in game.game_elements"
                                            :key="element.id"
                                            class="flex flex-col items-center gap-1"
                                        >
                                            <div
                                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg border shadow-sm"
                                                :class="
                                                    idx === 0
                                                        ? 'border-green-500 ring-2 ring-green-500/30 bg-green-50 dark:bg-green-950/30'
                                                        : 'bg-muted/30'
                                                "
                                            >
                                                <img
                                                    v-if="
                                                        element.pictogram
                                                            ?.image_url
                                                    "
                                                    :src="`/storage/${element.pictogram.image_url}`"
                                                    :alt="
                                                        element.pictogram?.name
                                                    "
                                                    class="h-full w-full object-cover"
                                                />
                                                <span
                                                    v-else
                                                    class="text-sm"
                                                    >{{
                                                        element.pictogram
                                                            ?.icon_text
                                                    }}</span
                                                >
                                            </div>
                                            <span
                                                class="max-w-[50px] truncate text-[10px]"
                                                :class="
                                                    idx === 0
                                                        ? 'font-bold text-green-600 dark:text-green-400'
                                                        : 'text-muted-foreground'
                                                "
                                                >{{
                                                    element.pictogram?.name
                                                }}</span
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Fallback if no elements loaded -->
                                <div
                                    v-else
                                    class="flex items-center text-sm text-muted-foreground"
                                >
                                    <Badge variant="secondary" class="mr-2">{{
                                        game.game_elements_count
                                    }}</Badge>
                                    Elementos configurados
                                </div>
                            </CardContent>

                            <CardFooter
                                class="flex justify-end gap-2 border-t bg-muted/20 pt-4 pb-4"
                            >
                                <Button size="sm" variant="outline" as-child>
                                    <Link :href="edit(game)">
                                        <EditIcon class="h-4 w-4" />
                                        Editar
                                    </Link>
                                </Button>
                                <Button
                                    size="sm"
                                    variant="outline"
                                    class="text-destructive hover:text-destructive"
                                    @click="deleteGame(game)"
                                >
                                    <Trash2Icon class="h-4 w-4" />
                                    Eliminar
                                </Button>
                            </CardFooter>
                        </Card>
                    </div>

                    <div
                        class="mt-6 flex justify-center"
                        v-if="paginatedGames.data.length > 0"
                    >
                        <Pagination :meta="paginatedGames" />
                    </div>
                </CardContent>
            </Card>
        </div>
    </AppLayout>
</template>
