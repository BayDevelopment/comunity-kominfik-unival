<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import {
    ClipboardList,
    LayoutGrid,
    FolderKanban,
    Users,
    Briefcase,
    CalendarRange
} from '@lucide/vue';

import AppLogo from '@/components/AppLogo.vue';
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
import { dashboard, project, anggota, layanan } from '@/routes';
import * as periodePendaftaran from '@/routes/periode-pendaftaran';
import type { NavItem } from '@/types';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Project',
        href: project(),
        icon: FolderKanban,
    },
    {
        title: 'Anggota',
        href: anggota(),
        icon: Users,
    },
    {
        title: 'Layanan',
        href: layanan(),
        icon: Briefcase,
    },
    {
        title: 'Periode',
        href: periodePendaftaran.index(),
        icon: CalendarRange,
    },
];

const footerNavItems: NavItem[] = [
    {
        title: 'User',
        href: '/users',
        icon: Users,
    },
    {
        title: 'Activity Log',
        href: '/activity-log',
        icon: ClipboardList,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
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
