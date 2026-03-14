<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import {
    ClipboardListIcon,
    EditIcon,
    PlusIcon,
    Trash2Icon,
} from 'lucide-vue-next';
import Pagination from '@/components/Pagination.vue';
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
    create as routinesCreate,
    destroy as routinesDestroy,
    edit as routinesEdit,
    index as routinesIndex,
} from '@/routes/admin/routines';
import type { BreadcrumbItem } from '@/types';
import type { Routine } from '@/types/data/routine';
import type { PaginatedData } from '@/types/paginated-data';

interface Props {
    paginatedRoutines: PaginatedData<Routine>;
    patients: { id: number; name: string }[];
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rutinas',
        href: routinesIndex().url,
    },
];

const { params: filters } = useQueryParams(
    {
        search: '',
        patient_id: 'all',
        sort: '',
        page: 1,
    },
    {
        route: routinesIndex,
        only: ['paginatedRoutines'],
        preserveScroll: true,
        preserveState: true,
    },
);

const deleteRoutine = (routine: Routine) => {
    if (!confirm('¿Deseas eliminar esta rutina?')) return;
    router.delete(routinesDestroy(routine).url);
};
</script>

<template>
    <Head title="Rutinas" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 px-4 py-8">
            <div
                class="flex flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div>
                    <h1 class="text-2xl font-bold text-foreground">Rutinas</h1>
                    <p class="text-muted-foreground">
                        Gestiona las rutinas asignadas a cada paciente.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link :href="routinesCreate().url">
                        <PlusIcon />
                        Nueva Rutina
                    </Link>
                </Button>
            </div>

            <div class="flex flex-col gap-4 sm:flex-row">
                <SearchInput
                    id="search"
                    class="flex-1"
                    :default-value="filters.search"
                    placeholder="Buscar rutina..."
                    :debounce-ms="300"
                    :on-debounced-change="
                        (value: string) => (filters.search = value)
                    "
                />
                <Select v-model="filters.patient_id">
                    <SelectTrigger class="w-full sm:w-48">
                        <SelectValue placeholder="Todos los pacientes" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="all">Todos los pacientes</SelectItem>
                        <SelectItem
                            v-for="p in patients"
                            :key="p.id"
                            :value="String(p.id)"
                        >
                            {{ p.name }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>

            <div
                v-if="paginatedRoutines.data.length === 0"
                class="py-16 text-center text-muted-foreground"
            >
                <span class="mb-3 block text-5xl">📋</span>
                <p class="font-medium">No hay rutinas registradas aún.</p>
                <p class="mt-1 text-sm">¡Crea la primera rutina!</p>
            </div>
            <div
                v-else
                class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Card
                    v-for="routine in paginatedRoutines.data"
                    :key="routine.id"
                >
                    <CardHeader>
                        <div class="min-w-0">
                            <CardTitle class="truncate">{{
                                routine.name
                            }}</CardTitle>
                            <p
                                v-if="routine.patient"
                                class="mt-1 text-xs text-muted-foreground"
                            >
                                👤 {{ routine.patient.name }}
                            </p>
                        </div>
                        <CardAction>
                            <Badge
                                :variant="
                                    routine.is_active ? 'default' : 'secondary'
                                "
                            >
                                {{ routine.is_active ? 'Activa' : 'Inactiva' }}
                            </Badge>
                        </CardAction>
                    </CardHeader>
                    <CardContent>
                        <div class="flex flex-wrap gap-2">
                            <span
                                class="inline-flex items-center gap-1 rounded-md bg-muted px-2 py-0.5 text-xs text-muted-foreground"
                            >
                                <ClipboardListIcon class="h-3 w-3" />
                                {{ routine.routine_steps_count ?? 0 }} pasos
                            </span>
                        </div>
                        <p
                            v-if="routine.description"
                            class="mt-2 line-clamp-2 text-sm text-muted-foreground"
                        >
                            {{ routine.description }}
                        </p>
                    </CardContent>
                    <CardFooter class="grid grid-cols-2 gap-2">
                        <Button as-child variant="outline">
                            <Link :href="routinesEdit(routine).url">
                                <EditIcon class="h-4 w-4" />
                                Editar
                            </Link>
                        </Button>
                        <Button
                            variant="outline"
                            @click="deleteRoutine(routine)"
                        >
                            <Trash2Icon class="h-4 w-4" />
                            Eliminar
                        </Button>
                    </CardFooter>
                </Card>
            </div>

            <Pagination
                v-if="paginatedRoutines.data.length > 0"
                :pagination="paginatedRoutines"
            />
        </div>
    </AppLayout>
</template>
