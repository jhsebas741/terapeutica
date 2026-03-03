<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { PlusIcon } from 'lucide-vue-next';
import { Button } from '@/components/ui/button';
import { SearchInput } from '@/components/ui/input';
import { useQueryParams } from '@/composables/useQueryParams';
import AppLayout from '@/layouts/AppLayout.vue';
import {
    index as patientsIndex,
    create as patientsCreate,
} from '@/routes/admin/patients';
import type { BreadcrumbItem } from '@/types';
import type { Patient } from '@/types/data/patient';
import type { PaginatedData } from '@/types/paginated-data';
import PatientCardList from './components/PatientCardList.vue';

interface Props {
    paginatedPatients: PaginatedData<Patient>;
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: patientsIndex().url,
    },
];

const { params: filters } = useQueryParams(
    {
        search: '',
        sort: '',
        page: 1,
    },
    {
        route: patientsIndex,
        only: ['paginatedPatients'],
        preserveScroll: true,
        preserveState: true,
    },
);
</script>

<template>
    <Head title="Pacientes" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex-1 space-y-6 px-4 py-8">
            <div
                className="flex flex-col items-center justify-between gap-4 sm:flex-row"
            >
                <div>
                    <h1 className="text-2xl font-bold text-foreground">
                        Pacientes
                    </h1>
                    <p className="text-muted-foreground">
                        Gestiona los niños registrados en el sistema.
                    </p>
                </div>
                <Button as-child class="w-full sm:w-auto">
                    <Link :href="patientsCreate().url">
                        <PlusIcon />
                        Nuevo Paciente
                    </Link>
                </Button>
            </div>
            <SearchInput
                id="search"
                :default-value="filters.search"
                placeholder="Buscar paciente"
                :debounce-ms="300"
                :on-debounced-change="(value) => (filters.search = value)"
            />
            <PatientCardList :patients="paginatedPatients.data" />
        </div>
    </AppLayout>
</template>
