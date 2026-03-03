import { router } from '@inertiajs/vue3';
import { reactive, watch } from 'vue';
import type { QueryParamsConfig } from '@/types/query-params';

export function useQueryParams<T extends Record<string, any>>(
    defaults: T,
    config: QueryParamsConfig<T>,
) {
    const urlParams = new URLSearchParams(window.location.search);
    const initialValues = { ...defaults };

    Object.keys(defaults).forEach((key) => {
        let urlValue: string | null = null;

        if (key === 'sort' || key === 'page') {
            urlValue = urlParams.get(key);
        } else {
            urlValue = urlParams.get(`filter[${key}]`);
        }

        if (urlValue !== null) {
            if (key === 'page') {
                (initialValues as any)[key] = parseInt(urlValue, 10) || 1;
            } else {
                (initialValues as any)[key] = urlValue;
            }
        }
    });

    const params = reactive<T>(initialValues);

    const debounceTimeouts: Partial<Record<keyof T, number>> = {};

    const navigate = () => {
        const query: Record<string, any> = {};

        Object.keys(params).forEach((key) => {
            const value = (params as any)[key];
            if (value !== '' && value !== null && value !== undefined) {
                if (key === 'sort') {
                    query.sort = value;
                } else if (key === 'page') {
                    query.page = value;
                } else {
                    if (!query.filter) {
                        query.filter = {};
                    }
                    query.filter[key] = value;
                }
            }
        });

        const queryOptions = { query: query as any };

        const { url } = config.routeArgs
            ? (config.route as any)(config.routeArgs, queryOptions)
            : (config.route as any)(queryOptions);

        router.visit(url, {
            only: config.only,
            preserveScroll: config.preserveScroll ?? true,
            preserveState: config.preserveState ?? true,
            replace: config.replace ?? true,
        });
    };

    Object.keys(params).forEach((key) => {
        watch(
            () => (params as any)[key],
            (newValue, oldValue) => {
                if (newValue === oldValue) return;

                const debounceTime = config.debounce?.[key as keyof T];

                if (debounceTime) {
                    const existingTimeout = debounceTimeouts[key as keyof T];
                    if (existingTimeout) {
                        clearTimeout(existingTimeout);
                    }
                    debounceTimeouts[key as keyof T] = setTimeout(() => {
                        navigate();
                    }, debounceTime) as unknown as number;
                } else {
                    navigate();
                }
            },
        );
    });

    const reset = () => {
        Object.keys(defaults).forEach((key) => {
            (params as any)[key] = defaults[key as keyof T];
        });
    };

    const resetField = (key: keyof T) => {
        (params as any)[key] = defaults[key];
    };

    return {
        params,
        reset,
        resetField,
    };
}
