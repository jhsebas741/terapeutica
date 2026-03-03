import { usePage } from '@inertiajs/vue3';

export function useCan() {
    const page = usePage();

    const can = (permission: string | string[]): boolean => {
        const userPermissions = page.props.auth.permissions;
        if (Array.isArray(permission)) {
            return permission.some((p) => userPermissions.includes(p));
        }
        return userPermissions.includes(permission);
    };

    const hasRole = (role: string | string[]): boolean => {
        const userRoles = page.props.auth.roles;
        if (Array.isArray(role)) {
            return role.some((r) => userRoles.includes(r));
        }
        return userRoles.includes(role);
    };

    return { can, hasRole };
}
