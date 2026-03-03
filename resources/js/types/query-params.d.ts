export interface InertiaVisitOptions {
    only?: string[];
    preserveScroll?: boolean;
    preserveState?: boolean;
    replace?: boolean;
}

type RouteResult = { url: string; method: string };
type RouteQueryOptions = { query?: Record<string, any> };

type RouteWithoutArgs = (options?: RouteQueryOptions) => RouteResult;
type RouteWithArgs<A> = (args: A, options?: RouteQueryOptions) => RouteResult;

interface BaseQueryParamsConfig<T extends Record<string, any>> {
    debounce?: Partial<Record<keyof T, number>>;
    only?: string[];
    preserveScroll?: boolean;
    preserveState?: boolean;
    replace?: boolean;
}

export interface QueryParamsConfigWithoutArgs<
    T extends Record<string, any>,
> extends BaseQueryParamsConfig<T> {
    route: RouteWithoutArgs;
    routeArgs?: never;
}

export interface QueryParamsConfigWithArgs<
    T extends Record<string, any>,
    A = any,
> extends BaseQueryParamsConfig<T> {
    route: RouteWithArgs<A>;
    routeArgs: A;
}

export type QueryParamsConfig<T extends Record<string, any>, A = any> =
    | QueryParamsConfigWithoutArgs<T>
    | QueryParamsConfigWithArgs<T, A>;

export interface QueryParamsReturn<T extends Record<string, any>> {
    params: T;
    reset: () => void;
    resetField: (key: keyof T) => void;
}
