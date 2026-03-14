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
    create as routinesCreate,
    index as routinesIndex,
    store as routinesStore,
} from '@/routes/admin/routines';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import type { Pictogram } from '@/types/data/pictogram';
import type { RoutineStep } from '@/types/data/routine';
import RoutineFields from './components/RoutineFields.vue';
import StepEditor from './components/StepEditor.vue';

interface CategoryWithPictograms extends Category {
    pictograms: Pictogram[];
}

interface Props {
    patients: { id: number; name: string }[];
    categories: CategoryWithPictograms[];
}
defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Rutinas',
        href: routinesIndex().url,
    },
    {
        title: 'Nueva Rutina',
        href: routinesCreate().url,
    },
];

const form = useForm({
    patient_id: '',
    name: '',
    description: '',
    steps: [] as RoutineStep[],
});

const submit = () => {
    form.post(routinesStore().url);
};
</script>

<template>
    <Head title="Nueva Rutina" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <form @submit.prevent="submit">
                <Card class="w-full sm:w-sm md:w-md lg:w-3xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Nueva Rutina
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <RoutineFields :form="form" :patients="patients" />
                        <StepEditor
                            v-model="form.steps"
                            :categories="categories"
                            :errors="form.errors"
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
                            <Link :href="routinesIndex()">Cancelar</Link>
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
