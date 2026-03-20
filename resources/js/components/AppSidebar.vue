<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ClipboardListIcon,
    Gamepad2Icon,
    ImageIcon,
    LayoutGrid,
    TagsIcon,
    Users2Icon,
} from 'lucide-vue-next';
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { index as categoriesIndex } from '@/routes/admin/categories';
import { index as patientsIndex } from '@/routes/admin/patients';
import { index as pictogramsIndex } from '@/routes/admin/pictograms';
import { index as routinesIndex } from '@/routes/admin/routines';
import { index as gamesIndex } from '@/routes/admin/games';
import type { NavItem } from '@/types';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Pacientes',
        href: patientsIndex(),
        icon: Users2Icon,
    },
    {
        title: 'Categorías',
        href: categoriesIndex(),
        icon: TagsIcon,
    },
    {
        title: 'Pictogramas',
        href: pictogramsIndex(),
        icon: ImageIcon,
    },
    {
        title: 'Rutinas',
        href: routinesIndex(),
        icon: ClipboardListIcon,
    },
    {
        title: 'Juegos',
        href: gamesIndex(),
        icon: Gamepad2Icon,
    },
];

const footerNavItems: NavItem[] = [
    // {
    //     title: 'Github Repo',
    //     href: 'https://github.com/laravel/vue-starter-kit',
    //     icon: Folder,
    // },
    // {
    //     title: 'Documentation',
    //     href: 'https://laravel.com/docs/starter-kits#vue',
    //     icon: BookOpen,
    // },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="floating">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
