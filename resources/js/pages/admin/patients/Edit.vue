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
    index as patientsIndex,
    edit as patientsEdit,
    update as patientsUpdate,
} from '@/routes/admin/patients';
import type { BreadcrumbItem } from '@/types';
import type { Patient } from '@/types/data/patient';
import PatientFields from './components/PatientFields.vue';

interface Props {
    patient: Patient;
}

const { patient } = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Pacientes',
        href: patientsIndex().url,
    },
    {
        title: patient.name,
        href: patientsEdit(patient).url,
    },
    {
        title: 'Editar Paciente',
        href: patientsEdit(patient).url,
    },
];
</script>

<template>
    <Head title="Nuevo Paciente" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <Form
                v-bind="patientsUpdate.form(patient)"
                v-slot="{ errors, processing }"
            >
                <Card class="w-full sm:w-sm md:w-md lg:w-3xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Registrar Paciente
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <PatientFields :patient="patient" :errors="errors" />
                    </CardContent>
                    <CardFooter
                        class="flex flex-col gap-4 sm:flex-row sm:justify-between"
                    >
                        <Button
                            variant="outline"
                            as-child
                            class="w-full sm:w-2/7"
                        >
                            <Link :href="patientsIndex()">Cancelar</Link>
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
