<script setup lang="ts">
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
import doctors from '@/routes/doctors';
import shiftTypes from '@/routes/shift-types';
import shifts from '@/routes/shifts';
import patients from '@/routes/patients';
import pathologies from '@/routes/pathologies';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, User, Briefcase, Calendar, Heart, Stethoscope, Clock } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavItems: NavItem[] = [
    {
        title: 'Inicio',
        href: dashboard(),
        icon: LayoutGrid,
    },
    {
        title: 'Doctores',
        href: doctors.index(),
        icon: User,
    },
    {
        title: 'Pacientes',
        icon: Heart,
        items: [
            {
                title: 'Pacientes',
                href: patients.index(),
                icon: Heart,
            },
            {
                title: 'Patologías',
                href: pathologies.index(),
                icon: Stethoscope,
            },
        ],
    },
    {
        title: 'Guardias',
        icon: Calendar,
        items: [
            {
                title: 'Guardias',
                href: shifts.index(),
                icon: Clock,
            },
            {
                title: 'Tipos de Guardias',
                href: shiftTypes.index(),
                icon: Briefcase,
            },
        ],
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
            <NavFooter :items="[]" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
