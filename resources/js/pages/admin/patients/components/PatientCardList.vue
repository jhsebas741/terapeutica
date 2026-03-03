<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardAction,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { getAge } from '@/lib/get-age';
import { edit as patientsEdit, toggleActive } from '@/routes/admin/patients';
import type { Patient } from '@/types/data/patient';

interface Props {
    patients: Patient[];
}

defineProps<Props>();
</script>

<template>
    <div
        class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5"
    >
        <Card v-for="patient in patients" :key="patient.id">
            <CardHeader>
                <div class="flex items-center gap-4">
                    <Avatar>
                        <AvatarImage
                            src="https://github.com/evilrabbit.png"
                            alt="@evilrabbit"
                        />
                        <AvatarFallback>ER</AvatarFallback>
                    </Avatar>
                    <div>
                        <CardTitle class="text-2xl">{{
                            patient.name
                        }}</CardTitle>
                        <CardDescription
                            >{{
                                getAge(patient.birth_date)
                            }}
                            Años</CardDescription
                        >
                    </div>
                </div>
                <CardAction>
                    <Badge v-if="patient.is_active">Activo</Badge>
                    <Badge v-else>Desactivado</Badge>
                </CardAction>
            </CardHeader>
            <CardContent>
                <div
                    class="w-full rounded-xl bg-muted p-1 text-center text-muted-foreground"
                >
                    {{ patient.diagnosis }}
                </div>
            </CardContent>
            <CardFooter class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                <Button as-child variant="outline">
                    <Link :href="patientsEdit(patient).url"> Editar </Link>
                </Button>
                <Button variant="outline" as-child>
                    <Link :href="toggleActive(patient).url" method="patch">
                        {{ patient.is_active ? 'Desactivar' : 'Activar' }}
                    </Link>
                </Button>
            </CardFooter>
        </Card>
    </div>
</template>
