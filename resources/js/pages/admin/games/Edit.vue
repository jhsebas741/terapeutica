<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { SaveIcon } from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Spinner } from '@/components/ui/spinner';
import AppLayout from '@/layouts/AppLayout.vue';
import { index, update } from '@/routes/admin/games';
import type { BreadcrumbItem } from '@/types';
import type { Category } from '@/types/data/category';
import type { Game, GameElement } from '@/types/data/game';
import EmocionForm from './types/EmocionForm.vue';
import EmparejarForm from './types/EmparejarForm.vue';
import SecuenciaForm from './types/SecuenciaForm.vue';

interface Props {
    game: Game;
    categories: Category[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Juegos Dinámicos',
        href: index().url,
    },
    {
        title: props.game.name,
        href: '#',
    },
];

const form = useForm({
    name: props.game.name,
    description: props.game.description ?? '',
    type: props.game.type,
    level: props.game.level,
    elements: (props.game.game_elements ?? []).map((s) => ({
        pictogram_id: s.pictogram_id,
        situation_text: s.situation_text,
        sequence_order: s.sequence_order,
        pictogram: s.pictogram,
    })) as GameElement[],
});

// Define condition logic based on type to validate submit button
const isSubmitDisabled = computed(() => {
    if (form.processing || !form.name || !form.type) return true;

    if (form.type === 'emparejar' || form.type === 'emocion') {
        return form.elements.length !== 4;
    }

    if (form.type === 'secuencia') {
        return form.elements.length < 2;
    }

    return form.elements.length < 2;
});

function submit() {
    form.put(update(props.game).url);
}
</script>

<template>
    <Head :title="`Editar Juego: ${game.name}`" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="container mx-auto max-w-5xl flex-1 p-4 pt-6 md:p-8">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-primary">
                        Editar Juego
                    </h2>
                    <p class="mt-1 text-muted-foreground">
                        Ajusta la configuración y los elementos del juego.
                    </p>
                </div>
            </div>

            <form @submit.prevent="submit" class="w-full">
                <template v-if="form.type === 'emparejar'">
                    <Card class="mx-auto max-w-4xl shadow-sm">
                        <CardContent class="pt-6">
                            <EmparejarForm
                                :categories="categories"
                                :form="form"
                            />

                            <div
                                v-if="form.errors.elements"
                                class="mt-2 text-sm text-destructive"
                            >
                                {{ form.errors.elements }}
                            </div>

                            <div
                                v-if="
                                    Object.keys(form.errors).some((k) =>
                                        k.startsWith('elements.'),
                                    )
                                "
                                class="mt-4 rounded-md border border-destructive/20 bg-destructive/10 p-3"
                            >
                                <p
                                    class="mb-1 text-sm font-medium text-destructive"
                                >
                                    Errores en los elementos:
                                </p>
                                <ul
                                    class="list-disc pl-5 text-sm text-destructive/90"
                                >
                                    <li
                                        v-for="(error, key) in form.errors"
                                        :key="key"
                                        v-show="key.startsWith('elements.')"
                                    >
                                        Elemento
                                        {{ Number(key.split('.')[1]) + 1 }}:
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </CardContent>
                        <div
                            class="flex flex-col gap-4 rounded-b-xl border-t bg-muted/20 px-6 py-4 sm:flex-row sm:justify-end"
                        >
                            <Button
                                variant="outline"
                                type="button"
                                as-child
                                class="w-full sm:w-32"
                            >
                                <Link :href="index()">Cancelar</Link>
                            </Button>
                            <Button
                                type="submit"
                                :disabled="isSubmitDisabled"
                                class="w-full sm:w-48"
                            >
                                <Spinner
                                    v-if="form.processing"
                                    class="mr-2 h-4 w-4"
                                />
                                <SaveIcon v-else class="mr-2 h-4 w-4" />
                                Actualizar
                            </Button>
                        </div>
                    </Card>
                </template>

                <template v-else-if="form.type === 'secuencia'">
                    <Card class="mx-auto max-w-4xl shadow-sm">
                        <CardContent class="pt-6">
                            <SecuenciaForm
                                :categories="categories"
                                :form="form"
                            />

                            <div
                                v-if="form.errors.elements"
                                class="mt-2 text-sm text-destructive"
                            >
                                {{ form.errors.elements }}
                            </div>

                            <div
                                v-if="
                                    Object.keys(form.errors).some((k) =>
                                        k.startsWith('elements.'),
                                    )
                                "
                                class="mt-4 rounded-md border border-destructive/20 bg-destructive/10 p-3"
                            >
                                <p
                                    class="mb-1 text-sm font-medium text-destructive"
                                >
                                    Errores en los elementos:
                                </p>
                                <ul
                                    class="list-disc pl-5 text-sm text-destructive/90"
                                >
                                    <li
                                        v-for="(error, key) in form.errors"
                                        :key="key"
                                        v-show="key.startsWith('elements.')"
                                    >
                                        Elemento
                                        {{ Number(key.split('.')[1]) + 1 }}:
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </CardContent>
                        <div
                            class="flex flex-col gap-4 rounded-b-xl border-t bg-muted/20 px-6 py-4 sm:flex-row sm:justify-end"
                        >
                            <Button
                                variant="outline"
                                type="button"
                                as-child
                                class="w-full sm:w-32"
                            >
                                <Link :href="index()">Cancelar</Link>
                            </Button>
                            <Button
                                type="submit"
                                :disabled="isSubmitDisabled"
                                class="w-full sm:w-48"
                            >
                                <Spinner
                                    v-if="form.processing"
                                    class="mr-2 h-4 w-4"
                                />
                                <SaveIcon v-else class="mr-2 h-4 w-4" />
                                Actualizar
                            </Button>
                        </div>
                    </Card>
                </template>

                <template v-else-if="form.type === 'emocion'">
                    <Card class="mx-auto max-w-4xl shadow-sm">
                        <CardContent class="pt-6">
                            <EmocionForm
                                :categories="categories"
                                :form="form"
                            />

                            <div
                                v-if="form.errors.elements"
                                class="mt-2 text-sm text-destructive"
                            >
                                {{ form.errors.elements }}
                            </div>

                            <div
                                v-if="
                                    Object.keys(form.errors).some((k) =>
                                        k.startsWith('elements.'),
                                    )
                                "
                                class="mt-4 rounded-md border border-destructive/20 bg-destructive/10 p-3"
                            >
                                <p
                                    class="mb-1 text-sm font-medium text-destructive"
                                >
                                    Errores en los elementos:
                                </p>
                                <ul
                                    class="list-disc pl-5 text-sm text-destructive/90"
                                >
                                    <li
                                        v-for="(error, key) in form.errors"
                                        :key="key"
                                        v-show="key.startsWith('elements.')"
                                    >
                                        Elemento
                                        {{ Number(key.split('.')[1]) + 1 }}:
                                        {{ error }}
                                    </li>
                                </ul>
                            </div>
                        </CardContent>
                        <div
                            class="flex flex-col gap-4 rounded-b-xl border-t bg-muted/20 px-6 py-4 sm:flex-row sm:justify-end"
                        >
                            <Button
                                variant="outline"
                                type="button"
                                as-child
                                class="w-full sm:w-32"
                            >
                                <Link :href="index()">Cancelar</Link>
                            </Button>
                            <Button
                                type="submit"
                                :disabled="isSubmitDisabled"
                                class="w-full sm:w-48"
                            >
                                <Spinner
                                    v-if="form.processing"
                                    class="mr-2 h-4 w-4"
                                />
                                <SaveIcon v-else class="mr-2 h-4 w-4" />
                                Actualizar
                            </Button>
                        </div>
                    </Card>
                </template>
            </form>
        </div>
    </AppLayout>
</template>
