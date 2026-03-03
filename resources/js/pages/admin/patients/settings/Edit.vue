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
import {
    Field,
    FieldContent,
    FieldDescription,
    FieldError,
    FieldLabel,
    FieldLegend,
    FieldSet,
    FieldTitle,
} from '@/components/ui/field';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Slider } from '@/components/ui/slider';
import { Spinner } from '@/components/ui/spinner';
import { Switch } from '@/components/ui/switch';
import AppLayout from '@/layouts/AppLayout.vue';
import { index as patientsIndex } from '@/routes/admin/patients';
import {
    edit as settings,
    update as settingsUpdate,
} from '@/routes/admin/patients/settings';
import type { BreadcrumbItem } from '@/types';
import type { Patient } from '@/types/data/patient';
import { getMinutes } from '@/lib/get-minutes';

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
        href: settings(patient).url,
    },
    {
        title: 'Configuración Sensorial',
        href: settings(patient).url,
    },
];

const options = [
  {
    value: 'low',
    label: 'Baja',
  },
  {
    value: 'medium',
    label: 'Media',
  },
  {
    value: 'high',
    label: 'Alta',
  },
] as const

const form = useForm({
    tts_enabled: patient.patient_setting?.tts_enabled ?? false,
    smooth_animations: patient.patient_setting?.smooth_animations ?? false,
    stimulation_level: patient.patient_setting?.stimulation_level ?? 'medium',
    default_routine_time_sec: patient.patient_setting?.default_routine_time_sec ?? 60,
});

function submit() {
    form.put(settingsUpdate(patient).url);
}
</script>

<template>
    <Head title="Nuevo Paciente" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div
            class="container flex flex-1 flex-col p-6 sm:items-center sm:justify-center"
        >
            <form
                @submit.prevent="submit"
            >
                <Card class="w-full sm:w-sm md:w-md lg:w-3xl">
                    <CardHeader>
                        <CardTitle class="text-2xl font-bold text-primary">
                            Configuración Sensorial de {{ patient.name }}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-6">
                        <Field
                            orientation="horizontal"
                            :data-invalid="!!form.errors.tts_enabled"
                        >
                            <FieldContent>
                                <FieldLabel for="tts_enabled">
                                    Sonidos Activados
                                </FieldLabel>
                                <FieldDescription>
                                    Habilita los audios en los pictogramas y
                                    rutinas
                                </FieldDescription>
                                <FieldError :errors="form.errors.tts_enabled" />
                            </FieldContent>
                            <Switch
                                id="tts_enabled"
                                name="tts_enabled"
                                :aria-invalid="!!form.errors.tts_enabled"
                                v-model="form.tts_enabled"
                            />
                        </Field>
                        <Field
                            orientation="horizontal"
                            :data-invalid="!!form.errors.smooth_animations"
                        >
                            <FieldContent>
                                <FieldLabel for="smooth_animations">
                                    Animaciones Suaves
                                </FieldLabel>
                                <FieldDescription>
                                    Transiciones tranquilas para niños con TDAH
                                </FieldDescription>
                                <FieldError
                                    :errors="form.errors.smooth_animations"
                                />
                            </FieldContent>
                            <Switch
                                id="smooth_animations"
                                name="smooth_animations"
                                :aria-invalid="!!form.errors.smooth_animations"
                                v-model="form.smooth_animations"
                            />
                        </Field>
                        <FieldSet>
                            <FieldLegend>Nivel de Estimulación</FieldLegend>
                            <FieldDescription>
                                Elige el nivel de estimulación para el paciente
                            </FieldDescription>
                            <RadioGroup
                                v-model="form.stimulation_level"
                                orientation="horizontal"
                                class="flex flex-col sm:flex-row gap-4"
                            >
                                <FieldLabel v-for="option in options" :key="option.value" :for="`option-${option.value}`">
                                    <Field orientation="horizontal" :data-invalid="!!form.errors.stimulation_level">
                                        <FieldContent>
                                            <FieldTitle>{{ option.label }}</FieldTitle>
                                        </FieldContent>
                                        <RadioGroupItem
                                            :id="`option-${option.value}`"
                                            :value="option.value"
                                            :aria-invalid="!!form.errors.stimulation_level"
                                        />
                                    </Field>
                                </FieldLabel>
                            </RadioGroup>
                            <FieldError :errors="form.errors.stimulation_level" />
                        </FieldSet>
                        <Field>
                            <FieldLabel for="default_routine_time_sec">
                                Tiempo por defecto de la rutina ({{ getMinutes(form.default_routine_time_sec) }})
                            </FieldLabel>
                            <Slider
                                id="default_routine_time_sec"
                                name="default_routine_time_sec"
                                :aria-invalid="!!form.errors.default_routine_time_sec"
                                :model-value="[form.default_routine_time_sec]"
                                :min="10"
                                :max="60 * 5"
                                :step="10"
                                @update:model-value="form.default_routine_time_sec = $event?.[0] ?? 0"
                            />
                            <FieldError :errors="form.errors.default_routine_time_sec" />
                        </Field>
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
                            :disabled="form.processing"
                            class="w-full sm:w-2/3"
                        >
                            <Spinner v-if="form.processing" />
                            <SaveIcon v-else />
                            Guardar
                        </Button>
                    </CardFooter>
                </Card>
            </Form>
        </div>
    </AppLayout>
</template>
