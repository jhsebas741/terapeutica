<script setup lang="ts">
import type { HTMLAttributes } from 'vue';
import { computed } from 'vue';
import { useDebounceFn, useVModel } from '@vueuse/core';
import { Search } from 'lucide-vue-next';
import { cn } from '@/lib/utils';
import Input from './Input.vue';

interface SearchInputProps {
    /**
     * Callback that is called when the input value changes
     */
    onDebouncedChange?: (value: string) => void;

    /**
     * Time in milliseconds for the debounce
     * @default 500
     */
    debounceMs?: number;

    /**
     * Controlled value of the input (optional)
     * If provided, the component works in controlled mode
     */
    modelValue?: string;

    /**
     * Initial value of the input (optional)
     * Only used if 'modelValue' is not provided
     */
    defaultValue?: string;

    /**
     * Show the search icon
     * @default true
     */
    showIcon?: boolean;

    /**
     * Size of the search icon
     * @default 16
     */
    iconSize?: number;

    /**
     * CSS class for the input element
     */
    class?: HTMLAttributes['class'];

    /**
     * CSS class for the wrapper container
     */
    wrapperClass?: HTMLAttributes['class'];

    /**
     * Placeholder text for the input
     */
    placeholder?: string;

    /**
     * Whether the input is disabled
     */
    disabled?: boolean;
}

const props = withDefaults(defineProps<SearchInputProps>(), {
    debounceMs: 500,
    defaultValue: '',
    showIcon: true,
    iconSize: 16,
});

const emits = defineEmits<{
    (e: 'update:modelValue', payload: string): void;
}>();


const internalValue = useVModel(props, 'modelValue', emits, {
    passive: true,
    defaultValue: props.defaultValue,
});

const debouncedChange = useDebounceFn((value: string) => {
    props.onDebouncedChange?.(value);
}, props.debounceMs);

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const newValue = target.value;

    internalValue.value = newValue;
    debouncedChange(newValue);
};

const inputClasses = computed(() => {
    return props.showIcon ? cn('peer ps-9', props.class) : props.class;
});
</script>

<template>
    <div v-if="showIcon" :class="cn('relative', wrapperClass)">
        <input
            :value="internalValue"
            data-slot="input"
            :placeholder="placeholder"
            :disabled="disabled"
            :class="
                cn(
                    'file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground dark:bg-input/30 border-input h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
                    'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
                    'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
                    inputClasses,
                )
            "
            @input="handleInput"
        />
        <div
            class="text-muted-foreground/80 pointer-events-none absolute inset-y-0 inset-s-0 flex items-center justify-center ps-3 peer-disabled:opacity-50"
        >
            <Search :size="iconSize" aria-hidden="true" />
        </div>
    </div>

    <Input
        v-else
        :model-value="internalValue"
        :class="props.class"
        :placeholder="placeholder"
        :disabled="disabled"
        @input="handleInput"
    />
</template>
