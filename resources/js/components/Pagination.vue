<script setup lang="ts">
import {
    ChevronFirstIcon,
    ChevronLastIcon,
    ChevronLeftIcon,
    ChevronRightIcon,
} from 'lucide-vue-next';
import { computed } from 'vue';
import { Button } from '@/components/ui/button';
import type { PaginatedData } from '@/types/paginated-data';

interface Props {
    meta?: PaginatedData<unknown>;
    modelValue?: number;
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'update:modelValue', page: number): void;
}>();

const isFirstPage = computed(
    () => !props.meta || props.meta.current_page === 1,
);
const isLastPage = computed(
    () => !props.meta || props.meta.current_page >= props.meta.last_page,
);

const goToPage = (page: number) => {
    if (props.meta && page >= 1 && page <= props.meta.last_page) {
        emits('update:modelValue', page);
    }
};

const goToFirstPage = () => goToPage(1);
const goToPreviousPage = () =>
    goToPage(props.meta ? props.meta.current_page - 1 : 1);
const goToNextPage = () =>
    goToPage(props.meta ? props.meta.current_page + 1 : 1);
const goToLastPage = () => goToPage(props.meta?.last_page ?? 1);
</script>
<template>
    <div class="flex items-center justify-between gap-8">
        <div
            class="flex grow justify-end text-sm whitespace-nowrap text-muted-foreground"
        >
            <p aria-live="polite">
                <span class="text-foreground">
                    {{ meta?.from ?? 0 }} - {{ meta?.to ?? 0 }}
                </span>
                de
                <span class="text-foreground">{{ meta?.total ?? '--' }}</span>
            </p>
        </div>
        <nav
            role="navigation"
            aria-label="pagination"
            data-slot="pagination"
            class="mx-auto flex w-full justify-center"
        >
            <ul
                data-slot="pagination-content"
                class="flex flex-row items-center gap-1"
            >
                <li data-slot="pagination-item">
                    <Button
                        variant="ghost"
                        :disabled="isFirstPage"
                        @click="goToFirstPage"
                    >
                        <ChevronFirstIcon :size="16" aria-hidden="true" />
                    </Button>
                </li>
                <li data-slot="pagination-item">
                    <Button
                        variant="ghost"
                        :disabled="isFirstPage"
                        @click="goToPreviousPage"
                    >
                        <ChevronLeftIcon :size="16" aria-hidden="true" />
                    </Button>
                </li>
                <li data-slot="pagination-item">
                    <Button
                        variant="ghost"
                        :disabled="isLastPage"
                        @click="goToNextPage"
                    >
                        <ChevronRightIcon :size="16" aria-hidden="true" />
                    </Button>
                </li>
                <li data-slot="pagination-item">
                    <Button
                        variant="ghost"
                        :disabled="isLastPage"
                        @click="goToLastPage"
                    >
                        <ChevronLastIcon :size="16" aria-hidden="true" />
                    </Button>
                </li>
            </ul>
        </nav>
    </div>
</template>
